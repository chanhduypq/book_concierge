<?php  
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class_exists('simple_html_dom_node') or require_once(APPPATH.'third_party/simple_html_dom.php');

class Fishpond
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
        $url="http://www.fishpond.com.hk/Books/Pets-Lift-The-Flap-Sarah-Khan/".$isin;
        $defaultcurrency="USD";
        $price=0;  $stock='';  $currency=$defaultcurrency;  $condition='new';  $delivery='';    $targeturl=$url; 
        
//        $html=file_get_html($url);
        $html=file_get_contents($url);
        if($html)
        { 
            $html_base = new simple_html_dom();
            $html_base->load($html);
            $html = $html_base;
                $content=$html->find('div[class="add_button_container"]');
                foreach($content[0]->find('b') as $bold)
                {
                    $text=$bold->plaintext;
                }
                if(trim($text)=="This item is unavailable.")
                {
                    //echo "<br>No Stocks Available";
                    $stock="Out of Stock";
                    $currency='';
                    $condition='';
                }
                else 
                {
                    $content=$html->find('span[class="productSpecialPrice"]');
                    $pricetext=$content[0]->plaintext;
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
                    $currency = str_replace("US$", $defaultcurrency, $currency);
                    $stock="In Stock";
                }
            
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
        //print_r($retarr);
        return $retarr;
    }
   
}