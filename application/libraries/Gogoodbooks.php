<?php  

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class_exists('simple_html_dom_node') or require_once(APPPATH.'third_party/simple_html_dom.php');

class Gogoodbooks
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
        $url="https://search.gogoodbooks.com/search?c=GBP&format=0&q=".$isin."&size=15";
        $defaultcurrency="GBP";
        $price=0;  $stock='';  $currency=$defaultcurrency;  $condition='new';  $delivery='';    $targeturl=$url; 
        
        $html=file_get_html($url);
       
        if($html)
        {
            $response=json_decode($html);
            $record=$response->Count;
            if($record)
            {
                $url = "https://gogoodbooks.com/".$response->Products[0]->SeName;
                $pricetext=$response->Products[0]->CostPrice;
                $pricetext=str_replace(",", "", $pricetext);
                if(isset($pricetext))
                {
                    preg_match('/([0-9,]+[\.]*[0-9]*)/', $pricetext, $match);		
                    if (isset($match[0])) 
                    {
                        $price=$match[0];
                    }
                }
                $currency = trim(str_replace($price, "", $pricetext));
                //echo "<br>Price=".$price." Currency=".$currency;
                
                if($price)
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
                $targeturl='';
            }
        }
        else
        {
            
        }
        
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
        $this->targeturl=$url;
        
        $retarr[$isin][]=array('price'=>$this->price,'currency'=>$this->currency,'condition'=>$this->condition,
                      'target_url'=>$this->targeturl,'delivery'=>$this->delivery);
        //print_r($retarr);
        return $retarr;
        
    }
    
}