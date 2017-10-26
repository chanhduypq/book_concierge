<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

function displayCart($mode='full') {
	Assets::add_module_css('books', 'books.css');
	Assets::add_module_js('books', 'books.js');
	
	$CI =& get_instance();
	$CI->load->library('cart');
	$CI->load->model('bookstores/stores_model');
	$CI->load->model('books/books_prices_model');
	
	if ($mode == 'summary')
		$row_tmpl = 'row_summary';
	else
		$row_tmpl = 'row_full';
	
	$data = array('cart'=>$CI->cart);	
	if ($CI->auth->is_logged_in()) {
		$current_user = $CI->auth->user();	
		
		$CI->load->model('users/user_model');
		$CI->load->model('localization/country_model');
				
		$user = $CI->user_model->find_user_and_meta($current_user->id);
		if ($user && isset($user->country)) {
			$country = $CI->country_model->find($user->country);
			
			if (!$country) $data['ask_shipping'] = true;
		}		
	}
	
	echo $CI->load->view('cart/partials/total_products', $data, true);
	unset($data);
	
	$local_currency = $CI->session->userdata('currency') ? $CI->session->userdata('currency') : 'USD';
	$total = 0;
	
	if ($CI->cart->total_items()) {
		$CI->load->helper('shippingrates/shipping_rate');
		
		foreach ($CI->cart->contents() as $item) {		
			$item['store'] = $CI->stores_model->find($item['store_id']);
			$item['price'] = $CI->books_prices_model->find_by(array('ean'=>$item['id'], 'engine'=>$item['store_id'], 'condition'=>$item['condition']));
						
			// get shipping
			if ($item['condition'] == 'new') {
				// get shipping				
				$shipping = get_shipping_price($item['store_id'], $local_currency);				
				
				if ($shipping && isset($shipping->rate)) {
					$item['shipping'] = round($shipping->rate, 2);
					if (!empty($shipping->availability))
						$item['delivery'] = $shipping->availability;
				} else {
					$item['shipping'] = 0;
				}
			} else {
				$item['shipping'] = 0;
			}
			
			echo $CI->load->view('cart/partials/'.$row_tmpl, array('item'=>$item), true);
			if (!empty($item['price']))
				$total += exchange($item['price']->price, $item['price']->currency, $local_currency) + $item['shipping'];
		}
		echo $CI->load->view('cart/partials/checkout', array('total'=>$total), true);
	}
}