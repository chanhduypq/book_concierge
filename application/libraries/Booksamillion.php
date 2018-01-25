<?php  

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class_exists('simple_html_dom_node') or require_once(APPPATH.'third_party/simple_html_dom.php');

class Booksamillion
{
    var $CI;
    private $price=0;
    private $stock='';
    private $currency='USD';
    private $condition='new';
    private $delivery='';
    private $targeturl='';
    
    public function __construct($config = array())
    {
	$this->CI =& get_instance();		
    }
    
    public function fetchPrice($isin)
    {
        $url="http://www.booksamillion.com/p/Nerdy-Nummies-Cookbook/Rosanna-Pansino/".$isin;
        $defaultcurrency="USD";
        $price=0;  $stock='';  $currency=$defaultcurrency;  $condition='new';  $delivery='';    $targeturl=$url; 
        
//        $html=file_get_html($url);     
        $html= file_get_contents($url);     
        
        if($html)
        { 
            
                $html_base = new simple_html_dom();
                $html_base->load($html);
                $html = $html_base;

                $prcdiv=$html->find('span[class="details-title-text"]',1);
                $prtext=$prcdiv->find('strong',0)->plaintext;

                $stkdiv=$html->find('div[class="price-block-quote-text"]',0);
                $stocktext=$stkdiv->find('strong',0)->plaintext;
                //echo "StockText=".$stocktext." PriceText=".$prtext;
                
                if(trim($stocktext)!="In Stock.")
                {
                    //echo "<br>No Stocks Available";
                    $stock="Out of Stock";
                    $currency='';
                    $condition='';
                }
                else 
                {
                    $pricetext=$prtext;
                    $pricetext= str_replace(",", "", $pricetext);
                    //echo "PriceText=",$pricetext;
                    if(isset($pricetext))
                    {
                        preg_match('/([0-9,]+[\.]*[0-9]*)/', trim($pricetext), $match);		
                        if (isset($match[1])) 
                        {
                            $price = (float)str_replace(',', '', $match[1]);
                        }
                    }
                    $price=$match[1];
                    $currency = trim(str_replace($price, "", $pricetext));
                    $currency = str_replace("$", $defaultcurrency, $currency);
                    $stock="In Stock";
                }
                //echo "<br>Price=$price Stock=$stock Currency=$currency Condition=$condition Deliver=$deliver Target Url=".$targeturl;
            
        }
        else
        {
            $currency='';
            $condition='';
            $targeturl='';
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
        //echo "Url=".$url;
        //print_r($retarr);
        return $retarr;
    }
   
}