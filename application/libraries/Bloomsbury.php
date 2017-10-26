<?php  
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class_exists('simple_html_dom_node') or require_once(APPPATH.'third_party/simple_html_dom.php');

class Bloomsbury
{
    var $CI;
    private $price=0;
    private $stock='';
    private $currency='HKD';
    private $condition='new';
    private $delivery='';
    private $targeturl='';
    
    public function __construct($config = array())
    {
	$this->CI =& get_instance();		
    }
    
    public function fetchPrice($isin)
    {
        
        $url="http://www.bloomsbury.com.hk/eng/p2.asp";
        $defaultcurrency="HKD"; 
        $price=0;  $stock='';  $currency=$defaultcurrency;  $condition='new';  $delivery='';    $targeturl=$url; 
        
        $post_data  = 'PC1Name=-&SearchWord='.$isin.'&viewresult=1';
        
        $ch = curl_init($url);
        
        
        curl_setopt($ch,CURLOPT_POST, 3);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $post_data);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        $html = curl_exec($ch);
        curl_close($ch);

        $html = str_get_html($html);
       
        $rectext=$html->find('td[class="std"]',0);
        $recordtext=$rectext->plaintext;           
            
        if(trim($recordtext)!='Sorry, we were unable to find exact matches for your search.')
        { 
            $prtext=$html->find('span[class="f01_aa_bu"]',0);
            $pricetext= $prtext->plaintext; 
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
            $currency = str_replace("HK$", $defaultcurrency, $currency);
            if($price>0)
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
            
           //die('xxxxxxx');
            
        }
        else
        {
            $currency='';
            $condition='';
            $targeturl='';
        }
        //die('yyyyy');
        
        $currency = strip_tags($currency);
        
        $this->price=$price;
        if(trim($currency)!='')
        {
            $this->currency=trim($currency);
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