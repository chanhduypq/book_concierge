<?php  
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class_exists('simple_html_dom_node') or require_once(APPPATH.'third_party/simple_html_dom.php');

class Pbookshop
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
        $url="http://www.pbookshop.com/catalogsearch/result/?cat=0&q=".$isin;
        $defaultcurrency="HKD";
        $price=0;  $stock='';  $currency=$defaultcurrency;  $condition='new';  $delivery='';    $targeturl=$url; 
        
//        $html=file_get_html($url);
        $html=file_get_contents($url);
        
        if($html)
        {
            $html_base = new simple_html_dom();
            $html_base->load($html);
            $html = $html_base;
            $data=$html->find('div[class="product-name"]');  
            $text=$data[0]->plaintext;
           
            //echo "Text=".$text;
           
            if(!empty($text))
            {  
                $nextlink=$data[0]->find('a');
                $url=$nextlink[0]->href;
                /*****
                    $nextlink=$data[0]->find('a');
                    $nexturl=$nextlink[0]->href;
                  
                    //$nexturl=$text;
                    //echo "Text=".$text;
                    $nexthtml=file_get_html($nexturl);
                    if($nexthtml)
                    {
                        $delv=$nexthtml->find('div[class="d_time"]',0);
                        $deliverytext=$delv->plaintext;
                        
                        $prtext=$nexthtml->find('span[class="price"]',1);
                        $pricetext=$prtext->plaintext;
                       
                        $pricetext= str_replace(",", "", $pricetext);
                        if(isset($pricetext))
                        {
                            preg_match('/([0-9,]+[\.]*[0-9]*)/', $pricetext, $match);		
                            if (isset($match[0])) 
                            {
                                 $price=$match[0];
                            }
                        }
                        $currency = trim(str_replace($price, "", $pricetext)); 
                        $currency = str_replace("HK$", $defaultcurrency, $currency);
                        $delivery=$deliverytext;
                                               
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
                             $delivery='';
                        }
                    }
                 **************/
                $price = false;
                $price_box = $html->find('div[class="price-box"]', 0);
                
                if ($price_box) {
                    $pricetext = $price_box->find('span[class="price"]', 0)->plaintext;
                    preg_match('/([0-9,]+[\.]*[0-9]*)/', $pricetext, $match);		
                    if (isset($match[0])) 
                    {
                         $price=$match[0];
                         $currency = trim(str_replace($price, "", $pricetext)); 
                         $delivery='';
                    }
                }
            }
            
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