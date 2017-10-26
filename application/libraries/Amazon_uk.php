<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Amazon_uk {

	var $CI;

	protected $AWS_ACCESS_KEY_ID;
	protected $AWS_SECRET_ACCESS_KEY;
	protected $AWS_ASSOCIATE_ID;
	
	private $ResponseGroup;
	private $ItemId;

	// --------------------------------------------------------------------

	public function __construct($config = array())
	{
		$this->CI =& get_instance();
		$this->CI->load->config('API');
		
		$this->AWS_ACCESS_KEY_ID = $this->CI->config->item('AWS_UK_ACCESS_KEY_ID');
		$this->AWS_SECRET_ACCESS_KEY = $this->CI->config->item('AWS_UK_SECRET_ACCESS_KEY');
		$this->AWS_ASSOCIATE_ID = $this->CI->config->item('AWS_UK_ASSOCIATE_ID');
	}
	
	public function fetch($items) {
		$this->ItemId = $items;
		$this->ResponseGroup = "Images,ItemAttributes";		
		
		$xmlin = $this->backoff('process');
		$response = array();
		
		if (is_object($xmlin)) {
			$items = $xmlin->Items->Item;
			foreach ($items as $item){						
				$AmaThumb = $item->LargeImage->URL;
				
				$data = $this->extractData($item);
				
				if (isset($data['ean']) && !empty($data['ean']) && !empty($AmaThumb)) {			
					$itemISBN = $data['ean'];
					if ($_SERVER['HTTP_HOST'] != 'localhost')
						@copy($AmaThumb, 'assets/covers/'.$itemISBN.'.jpg');
					$image = $itemISBN.'.jpg';
				} else {
					$image = '-';					
				}
								
				$additional = array('cdn_image'=>$image);
				
				$response[] = array_merge($data, $additional);
			}
		}
		
		// set hardcopy_ean if there are any ebooks
		$i = 0;
		foreach ($response as &$data) {
			if ($i) {
				$previous = $response[$i-1];
				if ($data['book_format'] == 'ebook')
					$data['hardcopy_ean'] = $previous['ean'];
			}	
			$i++;
		}
		
		return $response;
	}
	
	public function fetchBooks($items) {
		// should only be for internal purpose
		
		$this->ItemId = $items;
		$this->ResponseGroup = "Large";		
		
		$xmlin = $this->backoff('process');
		$response = array();
		
		if (is_object($xmlin)) {
			$items = $xmlin->Items->Item;
			foreach ($items as $item){						
				$AmaThumb = $item->LargeImage->URL;
				
				$data = $this->extractData($item);
				
				if (isset($data['ean']) && !empty($data['ean']) && !empty($AmaThumb)) {			
					$itemISBN = $data['ean'];
					if ($_SERVER['HTTP_HOST'] != 'localhost')
						@copy($AmaThumb, 'assets/covers/'.$itemISBN.'.jpg');
					$image = $itemISBN.'.jpg';
				} else {
					$image = '-';					
				}
								
				$additional = array('cdn_image'=>$image);
				
				$response[] = array_merge($data, $additional);
			}
		}
		
		// set hardcopy_ean if there are any ebooks
		$i = 0;
		foreach ($response as &$data) {
			if ($i) {
				$previous = $response[$i-1];
				if ($data['book_format'] == 'ebook')
					$data['hardcopy_ean'] = $previous['ean'];
			}	
			$i++;
		}
		
		return $response;
	}
	
	public function fetchPrice($items){
		$this->ItemId = $items;
		$this->ResponseGroup = "ItemAttributes,OfferSummary";	
		
		$xmlin = $this->backoff('process');
		$response = array();
		
		if (is_object($xmlin)) {
			$items = $xmlin->Items->Item;
			foreach ($items as $item){				
				$data = $this->extractData($item);
								
				if (isset($data['ean'])) {
					$target_url = (string)$item->DetailPageURL;	
					$offers =  $item->OfferSummary;
					
					$price = $currency = '';
					
					//for used products
					if (isset($offers->LowestUsedPrice->Amount)) {
						$price = (int)$offers->LowestUsedPrice->Amount/100;
						$currency = (string)$offers->LowestUsedPrice->CurrencyCode;
						
						if ($price) {
							$response[$data['ean']][] = array(
								'price'=>$price,
								'currency'=>$currency,
								'condition'=>'used',
								'target_url'=>$target_url,
								'delivery'=>'Usually dispatched within 1 to 3 weeks '
							);	
						}
					}
					
					// for new products
					$price = $currency = '';
					
					if (isset($item->ItemAttributes->ListPrice->Amount)) {
						$price = (int)$item->ItemAttributes->ListPrice->Amount/100;
						$currency = (string)$item->ItemAttributes->ListPrice->CurrencyCode;
					} elseif (isset($offers->LowestNewPrice->Amount)) {
						$price = (int)$offers->LowestNewPrice->Amount/100;
						$currency = (string)$offers->LowestNewPrice->CurrencyCode;
					}
					
					if (empty($currency)) $currency = 'GBP';		
					
					$response[$data['ean']][] = array(
						'price'=>(float)$price,
						'currency'=>$currency,
						'condition'=>'new',
						'target_url'=>$target_url,
						'delivery'=>'Usually dispatched within 1 to 3 weeks '
					);	
				}
			}
		}
		
		return $response;	
	}

	// --------------------------------------------------------------------

	
	private function process() {
		$base_url = "http://webservices.amazon.co.uk/onca/xml?";
		$url_params = array('Operation'=>"ItemLookup",'Service'=>"AWSECommerceService",
		 'AWSAccessKeyId'=>$this->AWS_ACCESS_KEY_ID,'AssociateTag'=>$this->AWS_ASSOCIATE_ID,
		 'Version'=>"2006-09-11",'Availability'=>"Available",'Condition'=>"All",
		 'ItemPage'=>"1",'ResponseGroup'=>$this->ResponseGroup,
		 'ItemId'=>$this->ItemId,'IdType'=>'ISBN','SearchIndex'=>'Books');
		
		// Add the Timestamp
		$url_params['Timestamp'] = gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", time());
			
		// Sort the URL parameters
		$url_parts = array();
		foreach(array_keys($url_params) as $key)
			$url_parts[] = $key."=".$url_params[$key];
		
		sort($url_parts);
		
		$url = $this->aws_signed_request('co.uk', $url_params, $this->AWS_ACCESS_KEY_ID, $this->AWS_SECRET_ACCESS_KEY, $this->AWS_ASSOCIATE_ID);
		//print $url;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		
		$xml_response = curl_exec($ch);
		$xmlin = simplexml_load_string($xml_response);
		
		if(!empty($xmlin)) {
			if (!empty($xmlin->Error)) {
				return false;
			}			
			
			return $xmlin;
		}
		
		return true;
	}
	
	private function aws_signed_request($region, $params, $public_key, $private_key, $associate_tag=NULL, $version='2011-08-01')
	{		
		/*
		Parameters:
			$region - the Amazon(r) region (ca,com,co.uk,de,fr,co.jp)
			$params - an array of parameters, eg. array("Operation"=>"ItemLookup",
							"ItemId"=>"B000X9FLKM", "ResponseGroup"=>"Small")
			$public_key - your "Access Key ID"
			$private_key - your "Secret Access Key"
			$version (optional)
		*/
		
		// some paramters
		$method = 'GET';
		$host = 'webservices.amazon.'.$region;
		$uri = '/onca/xml';
		
		// additional parameters
		$params['Service'] = 'AWSECommerceService';
		$params['AWSAccessKeyId'] = $public_key;
		// GMT timestamp
		$params['Timestamp'] = gmdate('Y-m-d\TH:i:s\Z');
		// API version
		$params['Version'] = $version;
		if ($associate_tag !== NULL) {
			$params['AssociateTag'] = $associate_tag;
		}
		
		// sort the parameters
		ksort($params);
		
		// create the canonicalized query
		$canonicalized_query = array();
		foreach ($params as $param=>$value)
		{
			$param = str_replace('%7E', '~', rawurlencode($param));
			$value = str_replace('%7E', '~', rawurlencode($value));
			$canonicalized_query[] = $param.'='.$value;
		}
		$canonicalized_query = implode('&', $canonicalized_query);
		
		// create the string to sign
		$string_to_sign = $method."\n".$host."\n".$uri."\n".$canonicalized_query;
		
		// calculate HMAC with SHA256 and base64-encoding
		$signature = base64_encode(hash_hmac('sha256', $string_to_sign, $private_key, TRUE));
		
		// encode the signature for the request
		$signature = str_replace('%7E', '~', rawurlencode($signature));
		
		// create request
		$request = 'http://'.$host.$uri.'?'.$canonicalized_query.'&Signature='.$signature;
		
		return $request;
	}
	
	private function backoff($callback) {
		$shouldRetry = true;
		$retries = 0;
		
		$started_at = time();
		
		$response = false;
		
		do {
			$response = $this->$callback();
			
			$shouldRetry = !$response;
			
			$retries++;
				
			if ($shouldRetry) {
				$delay = rand(1,5) * (pow(4, $retries) / 10);
				
				if (time()+$delay-$started_at > 120) {
					$shouldRetry = false;
				} else {			
					sleep($delay);
				}
			}
				
		} while ($shouldRetry && $retries < 5);
		
		return $response;
	}
	
	private function extractData($item) {
		$authors = array();
		if ($item->ItemAttributes->Author) {
			foreach($item->ItemAttributes->Author as $author) {
				$authors[] = (string)$author;
			}
		}
		
		$languages = array();
		if ($item->ItemAttributes->Languages->Language) {
			foreach($item->ItemAttributes->Languages->Language as $language) {
				if ($language->Type == 'Published')
					$languages[] = (string)$language->Name;
			}
		}
		
		if (!empty($item->ItemAttributes->EISBN)) {
			$ean = (string)$item->ItemAttributes->EISBN;
			$book_format = 'ebook';
		} else {
			$ean = (string)$item->ItemAttributes->EAN;
			$book_format = 'book';
		}
		
		$book_data = array(
			'ean'			=> $ean,
			'isbn'			=> (string)$item->ItemAttributes->ISBN,
			'ASIN'			=> (string)$item->ASIN,
			'name'			=> (string)$item->ItemAttributes->Title,
			'author'		=> implode(', ', $authors),
			'manufacturer'	=> (string)$item->ItemAttributes->Manufacturer,
			'publisher'		=> (string)$item->ItemAttributes->Publisher,
			'publication'	=> (string)$item->ItemAttributes->PublicationDate,
			'pages'			=> (string)$item->ItemAttributes->NumberOfPages,
			'binding'		=> (string)$item->ItemAttributes->Binding,
			'label'			=> (string)$item->ItemAttributes->Label,
			'studio'		=> (string)$item->ItemAttributes->Studio,
			'height'		=> (int)$item->ItemAttributes->PackageDimensions->Height,
			'length'		=> (int)$item->ItemAttributes->PackageDimensions->Length,
			'width'			=> (int)$item->ItemAttributes->PackageDimensions->Width,
			'weight'		=> (int)$item->ItemAttributes->PackageDimensions->Weight,
			'language'		=> implode(', ', $languages),
			'book_format'	=> $book_format
		);
		
		// To DO: check for additional data like book description etc and add to book_data
		
		return $book_data;
	}

}

// END CI_Profiler class

//--------------------------------------------------------------------

/* End of file Profiler.php */
/* Location: ./system/libraries/Profiler.php */