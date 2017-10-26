<?php  
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class_exists('simple_html_dom_node') or require_once(APPPATH.'third_party/simple_html_dom.php');

class Pollubooks
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
        $siteurl="http://http://stores.polluxbooks.com/";
        $url="http://stores.polluxbooks.com/search.php?search_query=".$isin;
        $defaultcurrency="HKD";
        $price=0;  $stock='';  $currency=$defaultcurrency;  $condition='new';  $delivery='';    $targeturl=$url; 
        
        $html=file_get_html($url);
        //echo "<br>".$url."<br>";
        
        if($html)
        {
            $data=$html->find('a[class="TrackLink"]');  
            $text=$data[0]->href;
           
            if(!empty($text))
            {  
                    $nexturl=$text;
                    //echo "Text=".$text;
                    $nexthtml=file_get_html($nexturl);
                    if($nexthtml)
                    {
                        $pricecontent=$nexthtml->find('em[class="ProductPrice VariationProductPrice"]',0);
                        $pricetext=trim($pricecontent);
                        $pricetext=  str_replace("$", "", $pricetext);                        
                        $pricetext=  str_replace(",", "", $pricetext);
                        if(isset($pricetext))
                        {
                            preg_match('/([0-9,]+[\.]*[0-9]*)/', $pricetext, $match);		
                            if (isset($match[0])) 
                            {
                                 $price=$match[0];
                            }
                        }
                        
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
                    }
                    //echo "<br>Price=$price Stock=$stock Currency=$currency Condition=$condition Deliver=$deliver Target Url=".$targeturl;
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