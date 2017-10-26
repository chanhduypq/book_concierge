<?php  

////include_once 'simple_html_dom.php';

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class_exists('simple_html_dom_node') or require_once(APPPATH.'third_party/simple_html_dom.php');

////////$isin="9781409507888";
////////$isin="9780794509149";
///////$isin="9781409524441";
//////$isin="9781409507390";

class Popularsg
{
    var $CI;
    private $price=0;
    private $stock='';
    private $currency='SGD';
    private $condition='new';
    private $delivery='';
    private $targeturl='';
    
    public function __construct($config = array())
    {
	$this->CI =& get_instance();		
    }
    
    public function fetchPrice($isin)
    {
        $url="https://www.popular.com.sg/catalogsearch/result/?q=".$isin;
        $defaultcurrency="SGD";
        $price=0;  $stock='';  $currency=$defaultcurrency;  $condition='new';  $delivery='';    $targeturl=$url; 
        
        $html=file_get_html($url);
        //echo "<br>".$url."<br>";
        if($html)
        {
            $data=$html->find('p[class="note-msg"]');
            if(count($data)<=0)
            {
                    
                    $content=$html->find('h2[class="product-name"]');
                    foreach($content[0]->find('a') as $link)
                    {
                        $nexturl=$link->href;
                    }
                    $nexthtml=file_get_html($nexturl);

                    if($nexthtml)
                    {
                        $pricecontent=$nexthtml->find('div[class="price-box"]');

                        foreach ($pricecontent[0]->find('span[class="price"]') as $span)
                        {
                            $pricetext=$span->plaintext;
                            if(isset($pricetext))
                            {
                                preg_match('/([0-9,]+[\.]*[0-9]*)/', $pricetext, $match);		
                                if (isset($match[1])) 
                                {
                                    $price = (float)str_replace(',', '', $match[1]);
                                }
                            }
                        }
                        $currency = trim(str_replace($price, "", $pricetext));
                        $currency = str_replace("SG$", $defaultcurrency, $currency);
                        $currency = str_replace("S$", $defaultcurrency, $currency);
                        $stockcontent=$nexthtml->find('p[class="availability in-stock"]');  //availability out-of-stock   availability in-stock
                        if(count($stockcontent)>0)
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
//        if(trim($currency)!='')
//        {
//            $this->currency=$currency;
//        }
//        else
//        {
//            $this->currency=$defaultcurrency;
//        }
        $this->currency=$defaultcurrency;
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