<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class_exists('simple_html_dom_node') or require_once(APPPATH.'third_party/simple_html_dom.php');

class Kinokuniya {

	var $CI;

	// --------------------------------------------------------------------

	public function __construct($config = array())
	{
		$this->CI =& get_instance();		
	}
	
	public function fetchPrice($itemId)
	{
		$url="https://singapore.kinokuniya.com/bw/".$itemId;
		//print $url;	
		
		$price = 0;
		$delivery = 'Usually dispatches within 3 - 4 weeks.';
		
		$html = file_get_html($url);
		
		if ($html) {
			foreach ($html->find('div[class="dContent"]') as $content) {
				foreach($content->find('li[class="price"]') as $element) {
					if (strstr($element->plaintext, 'Online Price')) {
						preg_match('/([0-9,]+[\.]*[0-9]*)/', $element->plaintext, $match);		
						if (isset($match[1])) {
							$price = (float)str_replace(',', '', $match[1]);
						}
					}
				 }	 
				 
				foreach($content->find('li[class="dispatches"]') as $element) {				
					$delivery = $element->plaintext;
					break;				
				 }
				 
				 if (strlen($delivery) > 100)
				 	$delivery = substr($delivery, 0, 95).'...';
				 
				 break;
			}
		 }
		 
		 $response[$itemId][] = array(
						'price'=>(float)$price,
						'currency'=>'SGD',
						'condition'=>'new',
						'target_url'=>$url,
						'delivery'=>$delivery
					);
		
		 return $response;
	}
	
	// --------------------------------------------------------------------

}

// END CI_Profiler class

//--------------------------------------------------------------------

/* End of file Profiler.php */
/* Location: ./system/libraries/Profiler.php */