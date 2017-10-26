<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function get_shipping_price($store_id, $currency){
	$CI =& get_instance();
	$CI->load->model('shippingrates/shippingrates_model');
	
	// get customer region
	$region_id = 0;
	$country = '';
        // disabled as no login is available
	if ($CI->auth->is_logged_in() && false) {
		$current_user = $CI->auth->user();	
		
		$CI->load->model('users/user_model');
				
		$user = $CI->user_model->find_user_and_meta($current_user->id);
		if ($user && isset($user->country)) {
			$country = $user->country;
			if (isset($user->state) && (int)$user->state)
				$region_id = (int)$user->state;
		}
	}
	
	if (empty($country))
		$country = $CI->session->userdata('country') ? $CI->session->userdata('country') : 'HK';
	
	if ($region_id) {
		$rate = $CI->shippingrates_model->where('region_id', $region_id)->find_by('store_id', $store_id);
	}
	
	if (!$region_id || !$rate) {
		// get rate default for the country
		$CI->load->model('shippingrates/country_shippingrates_model');
		$rate = $CI->country_shippingrates_model->find_by(array('store_id'=>$store_id, 'iso'=>$country));
	}
	
	if ($rate) {
		if ($rate->currency != $currency) {
			$CI->load->helper('localization/localization');
			$rate->rate = exchange($rate->rate, $rate->currency, $currency);
		}
		
		return $rate;
	}

	return false;
}