<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('localize_options')) {
	function localize_options() {
		$CI =& get_instance();		
					
		$CI->load->config('localization/config');
		$CI->load->helper('file');
		$CI->load->helper('form');
		
		$countries_loaded = $currencies_loaded = false;
		$countries_cache = $CI->config->item('localization_countries_cache');
		$currency_cache = $CI->config->item('localization_currency_cache');
		
		// get countries
		$countries = array();
		if (file_exists($countries_cache)) {
			$countries = read_file($countries_cache);
			$countries = unserialize($countries);
			
			if (is_array($countries) && count($countries))
				$countries_loaded = true;
		}
		
//		 if (!$countries_loaded) {	
//			$CI->load->model('localization/country_model');
//			$countries_data = $CI->country_model->order_by('group')->order_by('name')->find_all();
//			
//			foreach ($countries_data as $country) {
//				$countries[ucwords($country->group)][$country->iso] = $country->name;
//			}
//			
//			write_file($countries_cache, serialize($countries));
//		}
                $CI->load->model('localization/country_model');
                $countries_data = $CI->country_model->order_by('group')->order_by('name')->find_all();

                foreach ($countries_data as $country) {
                        $countries[ucwords($country->group)][$country->iso] = $country->name;
                }

                write_file($countries_cache, serialize($countries));
		
		// get currencies
		$currencies = array();
		if (file_exists($currency_cache)) {
			$currencies = read_file($currency_cache);
			$currencies = unserialize($currencies);
			
			if (is_array($currencies) && count($currencies))
				$currencies_loaded = true;
		}
		
		 if (!$currencies_loaded) {	
			$CI->load->model('localization/currency_model');
			$currencies_data = $CI->currency_model->find_all();
			
			foreach ($currencies_data as $currency) {
				$currencies[$currency->iso] = $currency->iso;
			}
			
			write_file($currency_cache, serialize($currencies));
		}
		
		echo form_open('localization/switcher', array('name'=>'cc_selector', 'class'=>'form-inline'));
		
		$default_country = $CI->session->userdata('country') ? $CI->session->userdata('country') : 'US';
		$default_currency = $CI->session->userdata('currency') ? $CI->session->userdata('currency') : 'USD';
		
                $country = $CI->session->userdata('country_iso');
                if (is_string($country) && trim($country) != "") {
                    $default_country = $country;
                }

                if (count($countries) > 0) {
			echo '
				<div class="form-group">
					'.form_dropdown('country', $countries, $default_country, '', ' class="form-control" onChange="document.cc_selector.submit()"').'
				</div>
			';
		}
		
		if (count($currencies) > 1) {
			echo '
				<div class="form-group">
					'.form_dropdown('currency', $currencies, $default_currency, '', ' class="form-control" onChange="document.cc_selector.submit()"').'
				</div>
			';
		}
		
		echo form_close();		
	}
}

if (!function_exists('visitor_data')) {
	function visitor_data()
	{
		$CI =& get_instance();
		
		if ($CI->session->userdata('visitor_data') && is_array($CI->session->userdata('visitor_data'))) {
			return $CI->session->userdata('visitor_data');
		}
		
		$client  = @$_SERVER['HTTP_CLIENT_IP'];
		$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
		$remote  = $_SERVER['REMOTE_ADDR'];
		$result  = array(
			'countryCode' => '',
			'countryName' => '',
			'currencyCode' => ''
		);
		if(filter_var($client, FILTER_VALIDATE_IP))
		{
			$ip = $client;
		}
		elseif(filter_var($forward, FILTER_VALIDATE_IP))
		{
			$ip = $forward;
		}
		else
		{
			$ip = $remote;
		}
		
		if ($_SERVER['HTTP_HOST']=='localhost')
			$ip = '122.177.160.221';
	
		$ip_data = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip));
	
		if($ip_data && $ip_data->geoplugin_countryName != null)
		{
			$result['countryCode'] = $ip_data->geoplugin_countryCode;
			$result['countryName'] = $ip_data->geoplugin_countryName;
			$result['currencyCode'] = $ip_data->geoplugin_currencyCode;
		}
			
		$CI->session->set_userdata('visitor_data', $result);
	
		return $result;
	}
}

function exchange($price, $from, $to) {
	if ($from == $to)
		return $price;
		
	$CI =& get_instance();
	$CI->load->model('localization/xrates_model');
	
	$xRate = $CI->xrates_model->find_by(array('from'=>$from, 'to'=>$to));
	if ($xRate) {
		$price = round($price * $xRate->xrate, 2);
	}
	
	return $price;
}