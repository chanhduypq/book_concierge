<?php
class Multicurl
{	
    private $errlist=array('Bad Request','Request Timeout','Http Error 404');
	function multiple_threads_request($nodes)
	{
           // echo 'mult curl calling...';
		$mh = curl_multi_init();
		$curl_array = array();
		foreach($nodes as $i => $url)
		{
                   // echo 'Multicurl.php calling...'.$url;
		    $curl_array[$i] = curl_init($url);
		    curl_setopt($curl_array[$i], CURLOPT_RETURNTRANSFER, true);
		    curl_setopt($curl_array[$i], CURLOPT_HEADER, 0);
		    curl_multi_add_handle($mh, $curl_array[$i]);
		}
		$running = NULL;
		do {
		    usleep(10000);
		    curl_multi_exec($mh,$running);
		} while($running > 0);
	       
		$res = array();
		foreach($nodes as $i => $url)
		{
		    $res[$url] = curl_multi_getcontent($curl_array[$i]);		   
		}
	       
		foreach($nodes as $i => $url){
		    curl_multi_remove_handle($mh, $curl_array[$i]);
		}
		curl_multi_close($mh);       
		return $res;
	}
        
        function checkError()
        {
            
        }

}

?>
