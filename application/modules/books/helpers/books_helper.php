<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function search_API_by_ISBN($isbn, $orBy=null) {
	$CI =& get_instance();
        
        if (!empty($orBy) && $orBy != 'ASIN')
            $orBy = null;
	
	$CI->load->library('amazon');
	$data = $CI->amazon->fetch($isbn, $orBy);
	
	if ($data && count($data) && isset($data[0]['ean'])) {
		$CI->load->model('books/books_model');
		
                $saved = false;
		foreach ($data as $item) {
                    if ($item['book_format'] == 'book') {
			$CI->books_model->insert($item);
                        $saved = true;
			break;
                    }
		}
                
                // could not find any item where format is book, saved for ebook
                if (!$saved) {
                    foreach ($data as $item) {
                        $CI->books_model->insert($item);
                        break;
                    }
                }
		
		$result = new stdClass;
		$result->ean = $data[0]['ean'];
		$result->name = $data[0]['name'];
		
		return $result;
	}
	
	return false;
}

function search_API_by_Keywords($keywords) {
	$CI =& get_instance();
	
	$CI->load->library('amazon');
	$data = $CI->amazon->search($keywords);
	
	$result = array();
	
	if ($data && count($data)) {
		$CI->load->model('books/books_model');
		$matches = array();
		
		foreach ($data as $item) {
			if (empty($item['ean']))
				continue;
				
			if (!$exists = $CI->books_model->find($item['ean'])) {
				$CI->books_model->insert($item);
			}
			
			$matches[] = array(
				'attrs' => array(
					'isbn'		=> $item['ean'],
					'name'		=> $item['name'],
					'author'	=> $item['author'],
					'description' => isset($item['description']) ? $item['description'] : '',
					'cdn_image'	=> $item['cdn_image']
				)
			);
		}
		
		$result['total_found'] = count($matches);
		$result['matches'] = $matches;
		
		return $result;
	}
	
	return false;
}

if (!function_exists('show_featured')) {
	function show_featured($limit=5, $category='default') {
		$CI =& get_instance();
		
		$country = $CI->session->userdata('country');
		
		$CI->load->model('books/bestsellers_model');
		$records = $CI->bestsellers_model->where('category', $category)->order_by('rand()')->limit($limit)->find_all();
		
		echo $CI->load->view('books/featured', array('records'=>$records), true);
		//save_image_queue();
	}
}

if (!function_exists('book_image_url')) {
	function book_image_url($isbn, $image='', $size=array()) {
            /****
		if (empty($image) || !is_file('assets/covers/'.$image)) {
                    //add_image_queue($isbn);		
                    $CI =& get_instance();

                    $CI->load->library('amazon');
                    $data = $CI->amazon->fetch($isbn);

                    $CI->load->model('books/books_model');

                    foreach ($data as $item) {
                        $CI->books_model->update($item['ean'], $item);

                        $image = $item['cdn_image'];
                    }
		}
            *****/
		
		if (empty($image) || $image == '-')
			return Template::theme_url('images/no-image.jpg');
		else {
			if (empty($size))		
				return base_url('assets/covers/'.$image);
			else {
				$CI =& get_instance();
				
				$w = $size[0];
				$h = $size[1];
				
				$image_ext = end(explode('.', $image));
				$image_name = str_replace('.'.$image_ext, '', $image);
				
				$new_name = $image_name.'-'.$w.'x'.$h.'.'.$image_ext;
				
				if (!file_exists('assets/covers/'.$new_name)) {				
					$config['image_library'] = 'gd2';
					$config['source_image'] = 'assets/covers/'.$image;
					$config['create_thumb'] = TRUE;
					$config['maintain_ratio'] = TRUE;
					$config['width'] = $w;
					$config['height'] = $h;
					$config['new_image'] = 'assets/covers/'.$new_name;
					
					$CI->load->library('image_lib', $config);
					
					$CI->image_lib->resize();
				}
				
				return base_url('assets/covers/'.$new_name);
			}
		}				
	}
}

if (!function_exists('add_image_queue')) {
	function add_image_queue($isbns, $reset_queue = false) {
		static $image_queue = array();
	
		if (empty($isbns)) {
			$queue = $image_queue;
	
			if ($reset_queue)
				$image_queue = array();	
				
			return $queue;
		}			
			
		$CI =& get_instance();
			
		if (!is_array($isbns))
			$isbns = array($isbns);
			
		if (!is_array($image_queue))
			$image_queue = array();
			
		$image_queue = array_merge($image_queue, $isbns);
	}
}

if (!function_exists('save_image_queue')) {
	function save_image_queue() {
		$image_queue = add_image_queue(false, true);
		
		if (count($image_queue)) {
			$CI =& get_instance();
			
			$sql = "";
			foreach ($image_queue as $isbn) {
				$insert_query = $CI->db->insert_string('image_queue', array('ean'=>$isbn));
				$sql = str_replace('INSERT INTO', 'INSERT IGNORE INTO', $insert_query).";";
				$CI->db->query($sql);
			}			 
		}
	}
}

function get_book_prices($ean) {
	$CI =& get_instance();
	
	$local_country = $CI->session->userdata('country') ? $CI->session->userdata('country') : 'HK';
	$local_currency = $CI->session->userdata('currency') ? $CI->session->userdata('currency') : 'HKD';
	
	$store_prices = $sort_helper = array();
	$prices = $CI->db->query("SELECT bf_stores.id, bf_stores.name, price, currency, `condition`, delivery, target_url, price + shipping as total_price FROM (`bf_stores`) LEFT JOIN `bf_books_prices` ON `bf_books_prices`.`engine` = `bf_stores`.`id` LEFT JOIN `bf_stores_countries` ON `bf_stores_countries`.`store_id` = `bf_stores`.`id` WHERE `bf_stores_countries`.`country_iso` = '".$local_country."' AND `bf_books_prices`.`ean` = '".$ean."' AND currency='".$local_currency."' UNION SELECT bf_stores.id, bf_stores.name, price*xrate as price, currency, `condition`, delivery, target_url, (price + shipping)*xrate as total_price FROM (`bf_stores`) LEFT JOIN `bf_books_prices` ON `bf_books_prices`.`engine` = `bf_stores`.`id` LEFT JOIN `bf_stores_countries` ON `bf_stores_countries`.`store_id` = `bf_stores`.`id` LEFT JOIN bf_xrates x ON bf_books_prices.currency = x.from WHERE `bf_stores_countries`.`country_iso` = '".$local_country."' AND `bf_books_prices`.`ean` = '".$ean."' AND currency != '".$local_currency."' AND x.to = '".$local_currency."' ORDER BY `condition` asc, `total_price` asc");
	
	if ($prices->num_rows()) {
		$CI->load->helper('shippingrates/shipping_rate');
		
		foreach ($prices->result() as $price) {
                        if ($price->name == 'Bookdepository.com' && !strstr($price->target_url, 'bookconc-20'))
                            $price->target_url = $price->target_url.'&a_aid=bookconc-20';
                        
			if ($price->condition == 'new') {
				// get shipping
				$shipping = get_shipping_price($price->id, $local_currency);
				if ($shipping && isset($shipping->rate)) {
					$price->shipping = round($shipping->rate, 2);
					$price->total_price = $price->price + $price->shipping;
					if (!empty($shipping->availability))
						$price->delivery = $shipping->availability;
				} else {
					$price->shipping = 0;
				}
			} else {
				$price->shipping = 0;
			}
			
			$helper_id = round($price->total_price, 2)*100;
			while (true) {
				if (array_key_exists((int)$helper_id, $sort_helper))
					$helper_id++;
				else
					break;
			}
			$sort_helper[$helper_id] = array('price_id'=>$price->id, 'condition'=>$price->condition);
			$store_prices[$price->id][$price->condition] = $price;
		}	
		
		ksort($sort_helper);
		
		$sorted = array();
		foreach ($sort_helper as $price=>$info) {
			$sorted[$info['condition']][$info['price_id']] = $store_prices[$info['price_id']][$info['condition']];
		}
		
		$store_prices = $sorted;
		
		unset($sort_helper);
		unset($sorted);
		
	}
	
	return $store_prices;
}

function get_book_image($ean) {
	$CI =& get_instance();
	$CI->load->model('books/books_model');
	$details = $CI->books_model->select('cdn_image')->find($ean);
	
	if (!empty($details))
		return $details->cdn_image;
	else
		return '';
}

function shorten_string($oldstring, $wordsreturned)
{
  $oldstring = strip_tags($oldstring);
  $oldstring = html_entity_decode($oldstring);
  $oldstring = strip_tags($oldstring);
  
  $retval = $oldstring;
  $string = preg_replace('/(?>=\S,)(?=\S)/', ' ', $oldstring);
  $string = str_replace("\n", " ", $string);
  $array = explode(" ", $string);
  if (count($array)<=$wordsreturned)
  {
    $retval = $oldstring;
  }
  else
  {
    array_splice($array, $wordsreturned);
    $retval = implode(" ", $array)." ...";
  }
  return $retval;
}

function createSlug($title){
	$title = html_entity_decode($title);
	$slug = preg_replace("![^a-z0-9]+!i", "-", $title);
	return trim($slug, '-');
}

function is_valid_isbn($isbn_number)
{
	$isbn_digits = array_filter(preg_split('//', $isbn_number, -1, PREG_SPLIT_NO_EMPTY), '_is_numeric_or_x');
	$isbn_length = count($isbn_digits);
	$isbn_sum = 0;
	 
	if((10 != $isbn_length) && (13 != $isbn_length))
	{ return false; }
	 
	if(10 == $isbn_length)
	{
	foreach(range(1, 9) as $weight)
	{ $isbn_sum += $weight * array_shift($isbn_digits); }
	 
	return (10 == ($isbn_mod = ($isbn_sum % 11))) ? ('x' == mb_strtolower(array_shift($isbn_digits), 'UTF-8')) : ($isbn_mod == array_shift($isbn_digits));
	}
	 
	if(13 == $isbn_length)
	{
	foreach(array(1, 3, 1, 3, 1, 3, 1, 3, 1, 3, 1, 3) as $weight)
	{ $isbn_sum += $weight * array_shift($isbn_digits); }
	 
	return (0 == ($isbn_mod = ($isbn_sum % 10))) ? (0 == array_shift($isbn_digits)) : ($isbn_mod == (10 - array_shift($isbn_digits)));
	}
	 
	return false;
}
 
function _is_numeric_or_x($val)
{ return ('x' == mb_strtolower($val, 'UTF-8')) ? true : is_numeric($val); }

function add_recently_viewed($ean, $name)
{
	$CI =& get_instance();
	$recently_viewed = $CI->session->userdata('recently_viewed');
	if (!is_array($recently_viewed))
		$recently_viewed = array();
		
	if (!array_key_exists($ean, $recently_viewed))
		$recently_viewed[$ean] = $name;
		
	if (count($recently_viewed) > 10)
		array_shift($recently_viewed);
	
	$CI->session->set_userdata('recently_viewed', $recently_viewed);
}

function recently_viewed() {
	$CI =& get_instance();
	$recently_viewed = $CI->session->userdata('recently_viewed');
	if (!is_array($recently_viewed))
		$recently_viewed = array();
		
	return $recently_viewed;
}