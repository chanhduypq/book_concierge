<?php  
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class_exists('simple_html_dom_node') or require_once(APPPATH.'third_party/simple_html_dom.php');

class Selectbooks
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
        $siteurl="http://www.selectbooks.com.sg/";
        $defaultcurrency="SGD";
        $url="http://www.selectbooks.com.sg/SearchResults.aspx?strt=1&keywords=".$isin;
        $price=0;  $stock='';  $currency=$defaultcurrency;  $condition='new';  $delivery='';    $targeturl=$url; 
        
        $html=file_get_html($url);
        //echo "<br>".$url."<br>";
        
        if($html)
        {
            $data=$html->find('font[face="Verdana,\'Arial Black\',Arial,sans-serif"]');
            $text=$data[0]->plaintext;
            if(strpos($text, "Search Results: Found 0 matches")===FALSE)
            { 
                    $content=$html->find('div[id="mainDiv"]');
                    foreach($content[0]->find('a') as $link)
                    {
                        if($link->hasAttribute('href') && strpos($link->getAttribute('href'),"getTitle.aspx?")!==FALSE)
                        {
                            $nexturl=$link->href;
                        }
                    }
                    $nexturl=$siteurl.$nexturl;
                    $nexthtml=file_get_html($nexturl);
                    if($nexthtml)
                    {
                        $pricecontent=$nexthtml->find('div[id="mainDiv"]',0)->find('table',0)->find('table',0)->find('p',0);
                        $para=trim($pricecontent->plaintext);
                        $arr =  explode("*)", $para);
                        $arr1=  explode("(", $arr[0]);
                        $arr1[0]=  trim(str_replace("Price:", "", $arr1[0]));
                        $pricetext=trim($arr1[1]);
                        if(isset($pricetext))
                        {
                            preg_match('/([0-9,]+[\.]*[0-9]*)/', $pricetext, $match);		
                            if (isset($match[1])) 
                            {
                                $price = (float)str_replace(',', '', $match[1]);
                            }
                        }
                        $currency = trim(str_replace($price, "", $pricetext));  
                        $currency = str_replace("SG$", $defaultcurrency, $currency);
                        $currency = str_replace("S$", $defaultcurrency, $currency);
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