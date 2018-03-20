<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bookdepository {

	var $CI;

	protected $ACCESS_KEY_ID;
	protected $SECRET_ACCESS_KEY;
	
	private $ItemId;
	private $userIP;
        
        private $CLIENT_ID = '64c787ea';
        private $AFFILIATE_ID = 'bookconc-20';
        private $API_KEY = '97990d4a5d4ff3837494d6945d12b2be393aa70c';

    // --------------------------------------------------------------------

	public function __construct($config = array())
	{
		$this->CI =& get_instance();
		$this->CI->load->config('API');
		
		$this->ACCESS_KEY_ID = $this->CI->config->item('BD_ACCESS_KEY_ID');
		$this->SECRET_ACCESS_KEY = $this->CI->config->item('BD_SECRET_ACCESS_KEY');
		$this->BD_ASSOCIATE_ID = $this->CI->config->item('BD_ASSOCIATE_ID');
	}
        
        public function fetchPrice($itemId, $user_ip)
	{
		$response = array();
		
		$this->userIP = $user_ip;
		
		$this->CI->load->library('settings/settings_lib');
		$scrap = (int)$this->CI->settings_lib->item('site.scrap_bookdepository');
		if ($scrap) {
			$this->CI->load->model('books/books_model');
			$book_details = $this->CI->books_model->find($itemId);
			if ($book_details) {
				$this->CI->load->helper('books/books');
				
				class_exists('simple_html_dom_node') or require_once(APPPATH.'third_party/simple_html_dom.php');
			
                                $xmlContent= file_get_contents("https://api.bookdepository.com/search/lookup?clientId=".$this->CLIENT_ID."&authenticationKey=".$this->API_KEY."&IP=$user_ip&isbn13=$itemId");
                                
				$price = 0;
                                
                                $xmlContent = simplexml_load_string($xmlContent);
                                
                                $targetUrl='';

                                if (is_object($xmlContent)) {
                                    $items = $xmlContent->items->item;
                                    foreach ($items as $item) {

                                        $attrs = $item->pricing->price->attributes();
                                        $attrs = (array) $attrs;
                                        $currency = $attrs['@attributes']['currency'];
                                        if (isset($item->pricing->price->selling)){
                                            $price = (float)$item->pricing->price->selling;
                                        }
						
					else{
                                            $price = (float)$item->pricing->price->retail ? (float)$item->pricing->price->retail : 0;
                                        }
                                        
                                        if (isset($item->url)){
                                            $targetUrl = $item->url;
                                            $targetUrl = (array) $targetUrl;
                                            $targetUrl = $targetUrl[0];
                                        }
                                        if (isset($item->biblio->title)){
                                            $name = $item->biblio->title;
                                        }
                                        if (isset($item->availability)){
                                            $availability = $item->availability;
                                        }
						
                                    }
                                    
                                    
                                    $response[$itemId][] = array(
                                            'price'=>$price,
                                            'currency'=>$currency,
                                            'condition'=>'new',
                                            'target_url'=>$targetUrl,
                                            'delivery'=>isset($availability)?$availability:NULL,
                                            'name'=> isset($name)?$name:NULL,
                                            'language'=> isset($language)?$language:NULL,
                                            'height'=>isset($height)?$height:NULL,
                                            'length'=>isset($length)?$length:NULL,
                                            'width'=>isset($width)?$width:NULL,
                                            'weight'=>isset($weigth)?$weigth:NULL,
                                            'publisher'=>isset($publisher)?$publisher:NULL,
                                            'publication'=>isset($publication)?$publication:NULL,
                                        );
                                }
				
				
			}
		}
		if (count($response)){
                    return $response;
                }
                    
                else{
                    // no API available, return blank for now
                    return array(
                        'price'=>false,
                        'currency'=>'',
                        'condition'=>'new',
                        'target_url'=>'',
                        'delivery'=>''
                    );
                }
                    
		
		
	}
	
	/*
         * backup
         *
        public function fetchPrice($itemId, $user_ip)
	{
		$response = array();
		
		$this->userIP = $user_ip;
		
		$this->CI->load->library('settings/settings_lib');
		$scrap = (int)$this->CI->settings_lib->item('site.scrap_bookdepository');
		if ($scrap) {
			$this->CI->load->model('books/books_model');
			$book_details = $this->CI->books_model->find($itemId);
			if ($book_details) {
			
				$this->CI->load->helper('books/books');
				
				class_exists('simple_html_dom_node') or require_once(APPPATH.'third_party/simple_html_dom.php');
			
				$targetUrl = "http://www.bookdepository.com/".createSlug($book_details->name)."/".$itemId."?utm_term=".$itemId."&utm_source=book_link&utm_content=".createSlug($book_details->name);
//				$html = file_get_html($targetUrl);
                                $html = file_get_contents($targetUrl);
				
				$price = 0;
				
				if ($html)
				{
                                    $html_base = new simple_html_dom();
                                    $html_base->load($html);
                                    $html = $html_base;
                                            /////////////// Price Extaction Code Begins /////////////////
                                       $pricecontainer=$html->find('div[class="item-info-wrap"]',0);
                                       
                                       if ($pricecontainer)
                                            $pricetext=$pricecontainer->find('span[class="sale-price"]',0); 
                                       
                                       if (!empty($pricetext)) {
                                            str_replace(",", "", $pricetext);
                                            preg_match('/([0-9,]+[\.]*[0-9]*)/', $pricetext, $match);
                                            if (isset($match[1])) 
                                            {
                                                $price = (float)str_replace(',', '', $match[1]);
                                            }

                                            ///////////////// Delivery Message Code Begins ////////////

                                            $available=$html->find('div[class="availability-text"]',0)->find('i[class="icon-check"]', 0);
                                            if (!$available) {
                                                $actual_price = $price = 0;
                                                $dtime = '';
                                            } 
                                            else 
                                            {
                                                $delivery = $html->find('div[class="availability-text"]',0)->plaintext;
                                                $dtime = trim(str_replace(array('Available', 'When will my order arrive?'), '', strstr($delivery, 'Available')));
                                            }

                                            /////////////////// Target Url Code Begins //////////

                                            if (strlen($this->BD_ASSOCIATE_ID))
                                            $targetUrl=str_replace("?","?utm_medium=api&utm_campaign=".$this->ACCESS_KEY_ID."&a_aid=".$this->BD_ASSOCIATE_ID."&", $targetUrl);
                                            $ul=$html->find('ul[class="biblio-info"]',0);
                                            $lis=$ul->find('li');
                                            foreach ($lis as $li){
                                                $label=$li->find('label',0)->plaintext;
                                                $span=trim($li->find('span',0)->plaintext);
                                                if(trim($label)=='Language'){
                                                    $language=$span;
                                                }
                                                else if(trim($label)=='Publisher'){
                                                    $publisher=$span;
                                                }
                                                else if(trim($label)=='Publication City/Country'){
                                                    $publication=$span;
                                                }
                                                else if(trim($label)=='Dimensions'){
                                                    list($dimensions,$weigth)= explode("|", $span);
                                                    $weigth=preg_replace('/[^0-9\.]/', '', $weigth);
                                                    $weigth= intval($weigth);
                                                    $dimensions=trim($dimensions);
                                                    list($length,$width,$height)= explode("x", $dimensions);
                                                    $length=trim($length);
                                                    $width=trim($width);
                                                    $height=preg_replace('/[^0-9]/', '', $height);
                                                    
                                                }
                                            }
                                            $h1=$html->find('h1[itemprop="name"]',0);
                                            if($h1){
                                                $name=$h1->plaintext;
                                            }
                                            
                                       }
				}
				
				if ($price) {
					$response[$itemId][] = array(
                                            'price'=>$price,
                                            'currency'=>'USD',
                                            'condition'=>'new',
                                            'target_url'=>$targetUrl,
                                            'delivery'=>$dtime,
                                            'name'=> isset($name)?$name:NULL,
                                            'language'=> isset($language)?$language:NULL,
                                            'height'=>isset($height)?$height:NULL,
                                            'length'=>isset($length)?$length:NULL,
                                            'width'=>isset($width)?$width:NULL,
                                            'weight'=>isset($weigth)?$weigth:NULL,
                                            'publisher'=>isset($publisher)?$publisher:NULL,
                                            'publication'=>isset($publication)?$publication:NULL,
                                        );
				}
			}
		}
		if (count($response))
                    return $response;
                else
                    // no API available, return blank for now
                    return array(
                        'price'=>false,
                        'currency'=>'',
                        'condition'=>'new',
                        'target_url'=>'',
                        'delivery'=>''
                    );
		
		$this->ItemId = $itemId;
		
		$xmlin = $this->process();
		
		if (is_object($xmlin)) {
			$items = $xmlin->items->item;
			if (!empty($items)) {
				foreach ($items as $item)
				{
					if (isset($item->pricing->price->selling))
						$price = (float)$item->pricing->price->selling;
					else
						$price = (float)$item->pricing->price->retail ? (float)$item->pricing->price->retail : 0;
					
					$delivery = 0;
					
					$targetUrl = !empty($item->url) ? (string)$item->url : '';
					
					$offer="";
					
					$dtime = !empty($item->availability) ? (string)$item->availability : 'Usually dispatched within 72 hours';
					
					if ($price && ($dtime != 'Out of stock')) {
						$response[$itemId][] = array(
									'price'=>$price,
									'currency'=>'SGD',
									'condition'=>'new',
									'target_url'=>$targetUrl,
									'delivery'=>$dtime
								);
					}
					
					break;           
				}
			}
		}
		
		return $response;
	}*/
	
	private function process() {
		$url = 'http://api.bookdepository.com/search/lookup?clientId='.$this->ACCESS_KEY_ID.'&authenticationKey='.$this->SECRET_ACCESS_KEY.'&IP='.$this->userIP.'&isbn13='.$this->ItemId.'&currencies=SGD';
		//print $url;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		
		$xml_response = curl_exec($ch);
		//mail('astonishingcreations@gmail.com', 'BD Response', $xml_response);
		$xmlin = simplexml_load_string($xml_response);
		
		if(!empty($xmlin)) {
			if (!empty($xmlin->Error) || (isset($xmlin->resultset->status) && (string)$xmlin->resultset->status == 'Not Found')) {
				return false;
			}			
			
			return $xmlin;
		}
		
		return true;
	}
        
	// --------------------------------------------------------------------

}

// END CI_Profiler class

//--------------------------------------------------------------------

/* End of file Profiler.php */
/* Location: ./system/libraries/Profiler.php */
