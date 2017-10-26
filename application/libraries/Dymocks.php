<?php  
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class_exists('simple_html_dom_node') or require_once(APPPATH.'third_party/simple_html_dom.php');

class Dymocks
{
    var $CI;
    private $price=0;
    private $stock='';
    private $currency='AUD';
    private $condition='new';
    private $delivery='';
    private $targeturl='';
    
    public function __construct($config = array())
    {
	$this->CI =& get_instance();		
    }
    
    public function fetchPrice($isin)
    {
        $url="https://www.dymocks.com.au/book/the-65-storey-treehouse-by-andy-griffiths-".$isin;
        $defaultcurrency="AUD";
        $price=0;  $stock='';  $currency=$defaultcurrency;  $condition='new';  $delivery='';    $targeturl=$url; 
        
        $html=file_get_html($url);
        if($html)
        { 
            
                $content=$html->find('h1[class="small_heading product_h1-color"]',0);
                $text=$content->plaintext;
                
                if(!empty($text))
                {
                    $pricetext=$html->find('span[id="ctl00_plcMain_lblPrice"]',0)->plaintext;
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
                    $currency = trim(str_replace("$", $defaultcurrency, $currency));


                    $delivertext=$html->find('p[class="stock"]',0)->plaintext;
                    $delivertext=  trim(str_replace("Availability:", "", $delivertext));
                    $delivertext=  trim(str_replace("Delivery Rates", "", $delivertext));
                    $delivery=$delivertext;
                    
                    if($price>0)
                    {
                        $stock="In Stock";
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
                    echo "Record Not Found";
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