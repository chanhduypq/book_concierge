<?php  
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class_exists('simple_html_dom_node') or require_once(APPPATH.'third_party/simple_html_dom.php');

class Paddyfield
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
        $url="http://www.paddyfield.com/mainstore2/details.php?prod=".$isin;
        $defaultcurrency="HKD";
        $price=0;  $stock='';  $currency=$defaultcurrency;  $condition='new';  $delivery='';    $targeturl=$url; 
        
//        $html=file_get_html($url);
        $html=file_get_contents($url);
        if($html)
        { 
            $html_base = new simple_html_dom();
            $html_base->load($html);
            $html = $html_base;
                $pricetext='';
                foreach($html->find('a') as $link)
                {
                    $carturl=trim($link->href);
                    
                    if(strpos($carturl, "add2cart.php?prod=")!==FALSE)
                    {
                        $image=$link->find('img',0);
                        $alttext=$image->getAttribute('alt');
                        $pricetext=trim(str_replace("click here to buy the book for:", "", $alttext));
                    }
                }
               
                //die('xxxxxx');
                
                
                if(!empty($pricetext))
                {
                    $pricetext=  str_replace(",", "", $pricetext);
                    if(isset($pricetext))
                    {
                            preg_match('/([0-9,]+[\.]*[0-9]*)/', trim($pricetext), $match);		
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
                        $delivery = 'Usually dispatches within 4 - 6 weeks.';
                    }
                    else 
                    {
                        $stock="Out of Stock";
                        $price=0;
                        $currency='';
                        $delivery='';
                        $condition='';
                        //$targeturl='';
                    }                    
                }
                else
                {
                    $currency='';
                    $condition='';
                    $delivery='';
                    //$targeturl='';
                }
                //echo "<br>Price=$price Stock=$stock Currency=$currency Condition=$condition Deliver=$deliver Target Url=".$targeturl;
        }
        else
        {
            $currency='';
            $condition='';
            $delivery='';
            //$targeturl='';
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