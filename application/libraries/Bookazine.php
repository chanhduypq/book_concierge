<?php  

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class_exists('simple_html_dom_node') or require_once(APPPATH.'third_party/simple_html_dom.php');


class Bookazine
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
        $url="http://bookazine.com.hk/eshop/search_result.php";
        $defaultcurrency="USD";   
        $price=0;  $stock='';  $currency=$defaultcurrency;  $condition='new';  $delivery='';    $targeturl=$url; 
        
//        $html = file_get_html('https://orders.bookazine.com/cgi-bin/bvrtSearch?act=search&stockType=NEW&subAct=search&searchType=ISBN&topSearch='.$isin);
        $html = file_get_contents('https://orders.bookazine.com/cgi-bin/bvrtSearch?act=search&stockType=NEW&subAct=search&searchType=ISBN&topSearch='.$isin);
        $html_base = new simple_html_dom();
        $html_base->load($html);
        $html = $html_base;
       
        $rectext=$html->find('div[class="dropShadow"]',0);
        
        $in_stock = true;
        foreach ($html->find('td') as $td) {
            if (strstr($td->plaintext, 'Out Of Stock')) {
                $in_stock = false;
                break;
            }
        }
        
        if ($rectext)
            $recordtext=trim($rectext->plaintext);           
            
        if(!empty($recordtext) && $in_stock)
        { 
            $pricetext=$recordtext;
            $pricetext=  str_replace(",", "", $pricetext);
            if(isset($pricetext))
            {
                preg_match('/([0-9,]+[\.]*[0-9]*)/', $pricetext, $match);		
                if (isset($match[1])) 
                {                    
                    $price = $match[0];
                }   
            }     
            
            $currency = $defaultcurrency;
            
            $url = $html->find('table[id="tblSearchRes"]', 0)->find('a', 0)->href;
            $url = preg_replace('/[^0-9]*/', '', $url);
            $targeturl = 'https://orders.bookazine.com/'.$url.'/detail/';
            
           //die('xxxxxxx');
            
        }
        else
        {
            $price = false;
            $currency='';
            $condition='';
            $targeturl='';
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
        $this->targeturl=$targeturl;
        
        $retarr[$isin][]=array('price'=>$this->price,'currency'=>$this->currency,'condition'=>$this->condition,
                      'target_url'=>$this->targeturl,'delivery'=>$this->delivery);
        
        //print_r($retarr);
        return $retarr;
        
    }
}