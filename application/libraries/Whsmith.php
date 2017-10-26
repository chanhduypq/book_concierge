<?php  
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class_exists('simple_html_dom_node') or require_once(APPPATH.'third_party/simple_html_dom.php');

class Whsmith
{ 
    var $CI;
    private $price=0;
    private $stock='';
    private $currency='GBP';
    private $condition='new';
    private $delivery='';
    private $targeturl='';
    
    public function __construct($config = array())
    {
	$this->CI =& get_instance();		
    }
    
    public function fetchPrice($isin)
    {
        $scrapurl="http://www.whsmith.co.uk/products/noisy-pets/".$isin;        
        $url="http://3a6a88b4826b4d6d805aad8cd1b413c1:@proxy.crawlera.com/fetch?url=".$scrapurl;        
        $html=file_get_html($url);
        $defaultcurrency="GBP";       
        $price=0;  $stock='';  $currency=$defaultcurrency;  $condition='new';  $delivery='';    $targeturl=$scrapurl;  
            
        if($html)
        { 
            $stock=$html->find('p[id="stock_status"]',0)->plaintext;
            $rectext=$html->find('span[class="price"]',0);
            $pricetext=$rectext->plaintext; 
            //die('xxx');
        
            $pricetext=  str_replace(",", "", $pricetext);
            if(isset($pricetext))
            {
                preg_match('/([0-9,]+[\.]*[0-9]*)/', $pricetext, $match);		
                if (isset($match[1])) 
                {
                    $price = (float)str_replace(',', '', $match[1]);
                }   
            }     
            $currency = trim(str_replace($price, "", $pricetext));
            $currency = str_replace("£", $defaultcurrency, $currency);
            
            if(trim($stock)!="Out of Stock")
            {
                $stock="In Stock";
            }
            else
            {
                $stock="Out of Stock";
                $price=0;
                $currency='';
                $condition='';
            }            
        }
        else
        {
            $currency='';
            $condition='';
            //$targeturl='';
        }
        //die('yyyyy');
        
        $this->price=$price;
        if(trim($currency)!='')
        {
            $this->currency=$currency;
        }
        else
        {
            $this->currency=$defaultcurrency;
        }
        if(trim($condition)!='')
        {
            $this->condition="$condition";
        }
        else
        {
            $this->condition="new";
        }
        $this->stock=$stock;
        $this->delivery=$delivery;        
        $this->targeturl=$scrapurl;
        
        $retarr[$isin][]=array('price'=>$this->price,'currency'=>$this->currency,'condition'=>$this->condition,
                      'target_url'=>$this->targeturl,'delivery'=>$this->delivery);
        //print_r($retarr);
        return $retarr;        
    }
}