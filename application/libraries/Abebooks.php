<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Abebooks {

    //var $CI;
    // --------------------------------------------------------------------

    public function __construct($config = array()) {
        //$this->CI =& get_instance();
    }

    public function fetchPrice($itemId) {
//		$xml_file = "http://products.betterworldbooks.com/service.aspx?ItemId=".$itemId."&CustomerCountryCode=SG";
        $xml_file = 'response.xml';
        $xml = simplexml_load_file($xml_file);

        if ($xml == FALSE) {
            return array();
        }

        $response = array();
        $newTotalListingPrices = $usedTotalListingPrices = array();
        for ($i = 0; $i < count($xml->Book); $i++) {
            if (isset($xml->Book[$i]->listingCondition) && $xml->Book[$i]->listingCondition == 'NOT NEW BOOK') {
                $usedTotalListingPrices[] = $xml->Book[$i]->totalListingPrice;
            } else {
                $newTotalListingPrices[] = $xml->Book[$i]->totalListingPrice;
            }
        }
        $minNewTotalListingPrice = min($newTotalListingPrices);
        $minUsedTotalListingPrice = min($usedTotalListingPrices);

        for ($i = 0; $i < count($xml->Book); $i++) {
            if (isset($xml->Book[$i]->listingCondition) && $xml->Book[$i]->listingCondition == 'NOT NEW BOOK' && $xml->Book[$i]->totalListingPrice == $minUsedTotalListingPrice) {
                $response[$itemId][] = array(
                    'price' => $xml->Book[$i]->listingPrice,
                    'currency' => $xml->Book[$i]->vendorCurrency,
                    'condition' => 'used',
                    'target_url' => $xml->Book[$i]->listingUrl,
                    'delivery' => $xml->Book[$i]->firstBookShipCost
                );
            } else if (!isset($xml->Book[$i]->listingCondition) && $xml->Book[$i]->totalListingPrice == $minNewTotalListingPrice) {
                $response[$itemId][] = array(
                    'price' => $xml->Book[$i]->listingPrice,
                    'currency' => $xml->Book[$i]->vendorCurrency,
                    'condition' => 'new',
                    'target_url' => $xml->Book[$i]->listingUrl,
                    'delivery' => $xml->Book[$i]->firstBookShipCost
                );
            }
        }

        return $response;
    }

}

// END CI_Profiler class

//--------------------------------------------------------------------

/* End of file Profiler.php */
/* Location: ./system/libraries/Profiler.php */