<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Amazon {

	var $CI;

	protected $AWS_ACCESS_KEY_ID;
	protected $AWS_SECRET_ACCESS_KEY;
	protected $AWS_ASSOCIATE_ID;
	
	private $ResponseGroup;
	private $ItemId;
	private $operation;
        private $LookupBy;

	// --------------------------------------------------------------------

	public function __construct($config = array())
	{
		$this->CI =& get_instance();
		$this->CI->load->config('API');
		
		$this->operation = 'ItemLookup';
                $this->LookupBy = 'ISBN';
		
		$this->AWS_ACCESS_KEY_ID = $this->CI->config->item('AWS_ACCESS_KEY_ID');
		$this->AWS_SECRET_ACCESS_KEY = $this->CI->config->item('AWS_SECRET_ACCESS_KEY');
		$this->AWS_ASSOCIATE_ID = $this->CI->config->item('AWS_ASSOCIATE_ID');
	}
	
	public function fetch($items, $by=null) {
		$this->ItemId = $items;
		$this->ResponseGroup = "Images,ItemAttributes";	
                
                if (!empty($by) && $by == 'ASIN')
                    $this->LookupBy = 'ASIN';
		
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
	
	public function search($keywords) {
		$this->ItemId = $keywords;
		$this->ResponseGroup = "Images,ItemAttributes,EditorialReview";		
		
		$this->operation = 'ItemSearch';
		
		$xmlin = $this->backoff('process');
		$response = array();
		
		if (is_object($xmlin)) {
			$items = $xmlin->Items->Item;
			foreach ($items as $item){						
				$AmaThumb = $item->LargeImage->URL;
				
				$data = $this->extractData($item);
				
				if (isset($data['ean']) && !empty($data['ean']) && !empty($AmaThumb)) {			
					$itemISBN = $data['ean'];
					if ($_SERVER['HTTP_HOST'] != 'localhost' && !is_file('assets/covers/'.$itemISBN.'.jpg'))
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
				if ($data['book_format'] == 'ebook' && !empty($previous['ean']))
					$data['hardcopy_ean'] = $previous['ean'];
			}	
			$i++;
		}
		
		return $response;
	}
	
	public function fetchBooks($items, $image_exists=array()) {
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
				$extra = $this->extractExtraData($item);
				
				$additional = array();
				if (isset($data['ean']) && !empty($data['ean']) && !in_array($data['ean'], $image_exists)) {
					if (!empty($AmaThumb)) {			
						$itemISBN = $data['ean'];
						if ($_SERVER['HTTP_HOST'] != 'localhost')
							@copy($AmaThumb, 'assets/covers/'.$itemISBN.'.jpg');
						$image = $itemISBN.'.jpg';
					} else {
						$image = '-';					
					}
					
					$additional = array('cdn_image'=>$image);
				}				
				
				$response[] = array_merge($data, $extra, $additional);
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
		$response = array();
		
		// check if admin specified to check as scrapping
		$this->CI->load->library('settings/settings_lib');
		$scrap = (int)$this->CI->settings_lib->item('site.scrap_amazon');
		if ($scrap) {
			if (!is_array($items)) {
				$orig_items = $items;
				$items = explode(',', $items);				
			}
				
			foreach ($items as $item) {
				// get asin from database
				$this->CI->load->model('books/books_model');
				$book_details = $this->CI->books_model->find($item);
				
				$this->CI->load->helper('books/books');
				
				if ($book_details && !empty($book_details->ASIN)) {
					$u="http://www.amazon.com/".createSlug($book_details->name)."/dp/".$book_details->ASIN;
					
					$price = '';
					
					class_exists('simple_html_dom_node') or require_once(APPPATH.'third_party/simple_html_dom.php');

					$targetUrl = $u.'?tag='.$this->AWS_ASSOCIATE_ID;		
//					$html = file_get_html($targetUrl);
                                        $html= file_get_contents($targetUrl);
					
					if ($html) {
                                            $html_base = new simple_html_dom();
                                            $html_base->load($html);
                                            $html = $html_base;
						foreach ($html->find('b[class="priceLarge"]') as $node)
						{		
							$price = str_replace(array("$", ","),"",$node->plaintext); //$result->Amount/100;
							break;
						}	
						
						if (empty($price)) {
							foreach ($html->find('span[class="rentPrice"]') as $node)
							{		
								$price = str_replace(array("$", ","),"",$node->plaintext); //$result->Amount/100;
								break;
							}	
						}
						
						if (empty($price)) {
							foreach ($html->find('span[class="a-size-medium a-color-price offer-price a-text-normal"]') as $node)
							{		
								$price = str_replace(array("$", ","),"",$node->plaintext); //$result->Amount/100;
								break;
							}	
						}
						
						if (empty($price)) {
							$prices_set = array();
							foreach ($html->find('div[class="rbb_header"]') as $node)
							{	
								foreach ($node->find('span[class="bb_title"]') as $item) {
									if (trim($item->plaintext) == 'Buy Used') {
										$price_type = 'used';
									} else {
										$price_type = 'new';
									}
								}
								
								foreach ($node->find('span[class="bb_price"]') as $item) {
									$price = (float)str_replace(array("$", ","),"",$item->plaintext); //$result->Amount/100;
									break;
								}
								
								if (!empty($price) && is_numeric($price)) {
									$response[$book_details->ean][] = array(
										'price'=>$price,
										'currency'=>'USD',
										'condition'=>$price_type,
										'target_url'=>$targetUrl,
										'delivery'=>'Usually ships in 1 to 3 weeks'
									);	
									
									$prices_set[] = $price_type;
								}
							}
							
							if (count($prices_set) && !in_array('new', $prices_set)) {
								$response[$book_details->ean][] = array(
									'price'=>0,
									'currency'=>'USD',
									'condition'=>'new',
									'target_url'=>$targetUrl,
									'delivery'=>'Usually ships in 1 to 3 weeks'
								);	
							}	
							
							// reset price as already added to array
							$price = '';
						}
					}
					
					if (is_numeric($price) && (float)$price) {
						
					} else {
						$price=(float)str_replace("$","",$price);
					}
					
					if (!empty($price) && is_numeric($price)) {
						$response[$book_details->ean][] = array(
							'price'=>$price,
							'currency'=>'USD',
							'condition'=>'new',
							'target_url'=>$targetUrl,
							'delivery'=>'Usually ships in 1 to 3 weeks'
						);	
					}
				}
			}
			
			$items = $orig_items;
		}
		
		if (count($response))
			return $response;
						
		$this->ItemId = $items;				
		$this->ResponseGroup = "ItemAttributes,OfferSummary";	
		
		$xmlin = $this->backoff('process');
		
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
								'delivery'=>'Usually ships in 1 to 3 weeks'
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
					
					if (empty($currency)) $currency = 'USD';
					
					$response[$data['ean']][] = array(
						'price'=>(float)$price,
						'currency'=>$currency,
						'condition'=>'new',
						'target_url'=>$target_url,
						'delivery'=>'Usually ships in 1 to 3 weeks'
					);	
				}
			}
		}
		
		return $response;	
	}

	// --------------------------------------------------------------------

	
	private function process() {
		$base_url = "http://webservices.amazon.com/onca/xml?";
		if ($this->operation == 'ItemSearch') {
			$url_params = array('Operation'=>"ItemSearch",'Service'=>"AWSECommerceService",
			 'AWSAccessKeyId'=>$this->AWS_ACCESS_KEY_ID,'AssociateTag'=>$this->AWS_ASSOCIATE_ID,
			 'Version'=>"2006-09-11",'Availability'=>"Available",'Condition'=>"All",
			 'ItemPage'=>"1",'ResponseGroup'=>$this->ResponseGroup,
			 'Keywords'=>$this->ItemId,'SearchIndex'=>'Books');
		} else {
			$url_params = array('Operation'=>"ItemLookup",'Service'=>"AWSECommerceService",
			 'AWSAccessKeyId'=>$this->AWS_ACCESS_KEY_ID,'AssociateTag'=>$this->AWS_ASSOCIATE_ID,
			 'Version'=>"2006-09-11",'Availability'=>"Available",'Condition'=>"All",
			 'ItemPage'=>"1",'ResponseGroup'=>$this->ResponseGroup,
			 'ItemId'=>$this->ItemId,'IdType'=>$this->LookupBy);
                        if ($this->LookupBy != 'ASIN')
                            $url_params['SearchIndex'] = 'Books';
		}
		
		// Add the Timestamp
		$url_params['Timestamp'] = gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", time());
			
		// Sort the URL parameters
		$url_parts = array();
		foreach(array_keys($url_params) as $key)
			$url_parts[] = $key."=".$url_params[$key];
		
		sort($url_parts);
		
		$url = $this->aws_signed_request('com', $url_params, $this->AWS_ACCESS_KEY_ID, $this->AWS_SECRET_ACCESS_KEY, $this->AWS_ASSOCIATE_ID);
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
		if (!$item)
			return array();
		
		$authors = array();
		if (isset($item->ItemAttributes->Author)) {
			foreach($item->ItemAttributes->Author as $author) {
				$authors[] = (string)$author;
			}
		}
		
		$languages = array();
		if (isset($item->ItemAttributes->Languages->Language)) {
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
			'manufacturer'	=> isset($item->ItemAttributes->Manufacturer) ? (string)$item->ItemAttributes->Manufacturer : '',
			'publisher'		=> isset($item->ItemAttributes->Publisher) ? (string)$item->ItemAttributes->Publisher : '',
			'publication'	=> isset($item->ItemAttributes->PublicationDate) ? (string)$item->ItemAttributes->PublicationDate : '',
			'pages'			=> isset($item->ItemAttributes->NumberOfPages) ? (string)$item->ItemAttributes->NumberOfPages : '',
			'binding'		=> isset($item->ItemAttributes->Binding) ? (string)$item->ItemAttributes->Binding : '',
			'label'			=> isset($item->ItemAttributes->Label) ? (string)$item->ItemAttributes->Label : '',
			'studio'		=> isset($item->ItemAttributes->Studio) ? (string)$item->ItemAttributes->Studio : '',
			'height'		=> isset($item->ItemAttributes->PackageDimensions->Height) ? (int)$item->ItemAttributes->PackageDimensions->Height : 0,
			'length'		=> isset($item->ItemAttributes->PackageDimensions->Length) ? (int)$item->ItemAttributes->PackageDimensions->Length : 0,
			'width'			=> isset($item->ItemAttributes->PackageDimensions->Width) ? (int)$item->ItemAttributes->PackageDimensions->Width : 0,
			'weight'		=> isset($item->ItemAttributes->PackageDimensions->Weight) ? (int)$item->ItemAttributes->PackageDimensions->Weight : 0,
			'language'		=> implode(', ', $languages),
			'book_format'	=> $book_format
		);
		
		if (isset($item->EditorialReviews->EditorialReview->Content)) {
			$book_data['description'] = html_entity_decode((string)$item->EditorialReviews->EditorialReview->Content);
		}
		
		if (strlen($book_data['manufacturer']) > 255) {
			if (!isset($book_data['description']) || empty($book_data['description']))
				$book_data['description'] = $book_data['manufacturer'];
			
			$book_data['manufacturer'] = '';
		}
		
		if (strlen($book_data['publisher']) > 255) {
			if (!isset($book_data['description']) || empty($book_data['description']))
				$book_data['description'] = $book_data['publisher'];
			
			$book_data['publisher'] = '';
		}
		
		if (strlen($book_data['label']) > 255) {
			if (!isset($book_data['description']) || empty($book_data['description']))
				$book_data['description'] = $book_data['label'];
			
			$book_data['label'] = '';
		}
		
		if (strlen($book_data['studio']) > 255) {
			if (!isset($book_data['description']) || empty($book_data['description']))
				$book_data['description'] = $book_data['studio'];
			
			$book_data['studio'] = '';
		}
		
		
		return $book_data;
	}
	
	private function extractExtraData($item) {
		if (!$item)
			return array();
		
		$other_eans = array();
		if (isset($item->EANList->EANListElement)) {
			foreach($item->EANList->EANListElement as $other_ean) {
				if ((string)$other_ean != $ean)
					$other_eans[] = (string)$other_ean;
			}
		}
		$book_data['other_eans'] = $other_eans;
			
		//extract nodes and similar products
		
		$nodes = array();
		if (isset($item->BrowseNodes->BrowseNode)) {
			foreach ($item->BrowseNodes->BrowseNode as $node) {
				$children = $parents = array();
				if ($node->Children->BrowseNode) {
					foreach ($node->Children->BrowseNode as $child) {
						$children[] = array('id'=>(int)$child->BrowseNodeId, 'name'=>(string)$child->Name);
					}
				}
				if ($node->Ancestors->BrowseNode) {
					foreach ($node->Ancestors->BrowseNode as $parent) {
						$parents[] = array('id'=>(int)$parent->BrowseNodeId, 'name'=>(string)$parent->Name);
					}
				}
				$nodes[] = array('id'=>(int)$node->BrowseNodeId, 'name'=>(string)$node->Name, 'children'=>$children, 'parent'=>$parents);
			}
		}
		
		$similar = array();
		if (isset($item->SimilarProducts->SimilarProduct)) {
			foreach ($item->SimilarProducts->SimilarProduct as $similar_product) {
				$similar[] = array('ASIN'=>(string)$similar_product->ASIN, 'name'=>(string)$similar_product->Title);
			}
		}
		
		return array('nodes'=>$nodes, 'similar'=>$similar);
	}
}

// END CI_Profiler class

//--------------------------------------------------------------------

/* End of file Profiler.php */
/* Location: ./system/libraries/Profiler.php */