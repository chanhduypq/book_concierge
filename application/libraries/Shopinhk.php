<?php  
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class_exists('simple_html_dom_node') or require_once(APPPATH.'third_party/simple_html_dom.php');

class Shopinhk
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
        $siteurl="http://www.shopinhk.com/";
        $url="http://www.shopinhk.com/search.php?type=0&mode=search&searchterm=".$isin;
        $defaultcurrency="HKD";
        $price=0;  $stock='';  $currency=$defaultcurrency;  $condition='new';  $delivery='';    $targeturl=$url; 
        
//        $html=file_get_html($url);
        $html=file_get_contents($url);
        //echo "<br>".$url."<br>";
        if($html)
        {
            $html_base = new simple_html_dom();
            $html_base->load($html);
            $html = $html_base;
            $data=$html->find('a[class="product-title"]');
            $newurl=$data[0]->href;
           
            /********** GETING DATA FROM DETAILS PAGE ***********
            if(!empty($newurl))
            {   
                $nexturl=$siteurl.$newurl;
                $nexthtml=file_get_html($nexturl);

                if($nexthtml)
                {
                    $curtext=$nexthtml->find('span[itemprop="priceCurrency"]',0);
                    $currencytext=$curtext->plaintext;
                    
                    $prtext=$nexthtml->find('span[id="product_price"]',0);
                    $pricetext=$prtext->plaintext;
                    $price =  str_replace(",", "", $pricetext);
                    
                    $tbl=$nexthtml->find('table',5)->find('tr');
                    $totstock=0;
                    for($i=0;$i<count($tbl);$i++)
                    {
                        if($i>0)
                        {
                            $totstock+=(int)$tbl[$i]->find('td',1)->plaintext;
                        }
                    }
                    
                    $delivery='Usually dispatches within 1 - 2 weeks.';
                    
                    $stockcontent=$nexthtml->find('p[class="availability in-stock"]');  //availability out-of-stock   availability in-stock
                    
                    if($totstock>0)
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
                    //$targeturl='';
                    $delivery='';
                }
            }
            else 
            {
                $currency='';
                $condition='';
                //$targeturl='';
                $delivery='';
            }
            *************************/
            $price = $html->find('div[class="res-price"]', 0);
            if ($price) {
                $price = $price->plaintext;
                $currency = preg_replace('/[^a-zA-z]*/', '', $price);
                preg_match('/[0-9.\,]+/', $price, $matches);
                if (!empty($matches))
                    $price = $matches[0];
                else
                    $price = false;
            }
            
            if ($price){
                $delivery='Usually dispatches within 1 - 2 weeks.';
                $stock="In Stock";
                $condition='';
            } else {
                $currency='';
                $condition='';
                //$targeturl='';
                $delivery='';
            }
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
        $this->targeturl=$targeturl;
        
        $retarr[$isin][]=array('price'=>$this->price,'currency'=>$this->currency,'condition'=>$this->condition,
                      'target_url'=>$this->targeturl,'delivery'=>$this->delivery);
        return $retarr;
        
    }
}