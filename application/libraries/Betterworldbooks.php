<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Betterworldbooks {

	//var $CI;

	// --------------------------------------------------------------------

	public function __construct($config = array())
	{
		//$this->CI =& get_instance();
	}
	
	public function fetchPrice($itemId)
	{
		$xml_file = "http://products.betterworldbooks.com/service.aspx?ItemId=".$itemId."&CustomerCountryCode=SG";
		$xml = simplexml_load_file($xml_file);
		
		$target_url = '';
		$response = array();
		http://www.betterworldbooks.com/head-first-java-id-0596009208.aspx
		if (isset($xml->Items->Item->DetailURLPage))
			$target_url = "http://www.anrdoezrs.net/click-7389342-10474571?url=".(string)$xml->Items->Item->DetailURLPage.urlencode("?utm_source=affiliate&utm_campaign=text&utm_medium=booklink&utm_term=%zp&utm_content=product");
		
		//for used products
		if (isset($xml->Items->Item->OfferSummary->LowestUsedPrice)) {
			$price = (float)str_replace("$","",$xml->Items->Item->OfferSummary->LowestUsedPrice);
			$currency = 'USD';
			
			if ($price) {
				$response[$itemId][] = array(
					'price'=>$price,
					'currency'=>$currency,
					'condition'=>'used',
					'target_url'=>$target_url,
					'delivery'=>'FREE Shipping Worldwide.'
				);	
			} else {
				$response[$itemId][] = array(
					'price'=>0,
					'currency'=>$currency,
					'condition'=>'used',
					'target_url'=>'',
					'delivery'=>''
				);	
			}
		}
		
		//for new products
		if (isset($xml->Items->Item->OfferSummary->LowestNewPrice)) {
			$price = (float)str_replace("$","",$xml->Items->Item->OfferSummary->LowestNewPrice);
			$currency = 'USD';
			
			if ($price) {
				$response[$itemId][] = array(
					'price'=>$price,
					'currency'=>$currency,
					'condition'=>'new',
					'target_url'=>$target_url,
					'delivery'=>'FREE Shipping Worldwide.'
				);	
			} else {
				$response[$itemId][] = array(
					'price'=>0,
					'currency'=>$currency,
					'condition'=>'new',
					'target_url'=>'',
					'delivery'=>''
				);	
			}
		}
		
		return $response;
	}
}

// END CI_Profiler class

//--------------------------------------------------------------------

/* End of file Profiler.php */
/* Location: ./system/libraries/Profiler.php */