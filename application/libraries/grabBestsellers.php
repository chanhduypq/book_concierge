<?php
mysql_connect('localhost', 'i12reader', 'Y1>03$5&,}5b7/G');
mysql_select_db('bkdata_486');

class main
{
    public function curl_function($url, $json = false)
    {
        // delay for a bit, do not run too fast
        //sleep(rand(10,20));
        
        $ch = curl_init();
        if ($json) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, str_replace("\\", "", json_encode($json)));
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_PROXY, 'proxy.crawlera.com:8010');
        curl_setopt($ch, CURLOPT_PROXYUSERPWD, '84d0030b0161465fa014c80993d492cb:');
        curl_setopt($ch, CURLOPT_COOKIE, "x-wl-uid=1QJahplkO/3SegDskEc+P64s4ghZzo0myK6cmV0WCObh8kRllBDll0xvJnnY6YJZSRunjC5tY6TE=; session-token=K9ATzRPHflw4uOsuFWwcildrYHCFfMzRGOQ+nuIXg2rOkez5VOJlOhV6bFNNiy48p02aagGEzVj7EqsgJjzN46phTGKwIJOK1hkN7bYCkPfPVsfa41M4+TAphEKwLU9YVpfZ3A1A532DfAwxfkFaZJd++kKNQkNjIb9UHeyhmA25NU+IsIS1KpGafAU9PEcJeRKLMoAIU4jVAEs0AtV5UQsqN96ZQtbG/Rp4hl0eakNR3nUQJ4iRHfsC8frql86P; lc-main=en_US; csm-hit=s-SA5E1J5XX0X6X4YTFMMF|1486414957656; ubid-main=164-9426889-7804412; session-id-time=2082787201l; session-id=151-3875392-8658026");
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 30000); //in miliseconds
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.0; ru; rv:1.9.1.3) Gecko/20090824 Firefox/3.5.3');
        curl_setopt($ch, CURLOPT_URL, $url);

        $output = curl_exec($ch);


        if ($output !== FALSE) {

            return $output;

        }

        return false;
    }

    public function get_isbn($data)
    {

        preg_match("/ISBN-13:(.*)<\/li>/U", $data, $isbn13);

        if (!empty($isbn13[1])) {

            $isbn = trim(strip_tags($isbn13[1]));
            return $isbn;

        } else {

            preg_match("/ISBN-10:(.*)<\/li>/U", $data, $isbn10);

            if (!empty($isbn10[1])) {

                $isbn = trim(strip_tags($isbn10[1]));
                return $isbn;

            }
        }
        return false;
    }
    
    public function fetchAndSaveBook($isbn)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://bookconcierge.hk/".$isbn."/asdfasf-asdfasdf");
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
        curl_exec($ch);
        curl_close($ch);
    }
}

set_time_limit(0);
$main = new main();
$arr = array();

$categories = array(
    'default' => 'https://www.amazon.com/best-sellers-books-Amazon/zgbs/books/',
    //'children' => 'https://www.amazon.com/Best-Sellers-Books-Childrens/zgbs/books/4/',
    //'management' => 'http://www.amazon.com/Best-Sellers-Books-Business-Management-Leadership/zgbs/books/2675/',
    //'religion' => 'http://www.amazon.com/Best-Sellers-Books-Religion-Spirituality/zgbs/books/22/',
    //'crime' => 'http://www.amazon.com/Best-Sellers-Books-Mystery-Thriller-Suspense/zgbs/books/18/'
);

foreach ($categories as $category => $url) {
    
    $isbns = array();
    
    if ($data = $main->curl_function($url)) {

        preg_match_all("/<a class=\"a-link-normal\" href=\"(.*)\"/U", $data, $matchs);

        for ($i = 0; $i < 20; $i++) {

            $arr[$i] = "https://www.amazon.com" . $matchs[1][$i];

        }
        unset($matchs);

    }

    if (count($arr) > 0) {

        for ($i = 0; $i < count($arr); $i++) {

            $data = $main->curl_function($arr[$i]);
            $data = preg_replace("/\n/", "", $data);

            preg_match("/<h1(.|\n)+<\/h1>/U", $data, $h1);

            if (!empty($h1) && strrpos($h1[0], "Kindle") === false) {

                if ($isbn = $main->get_isbn($data)) {

                    $arr_data[$i]['url'] = $arr[$i];
                    $arr_data[$i]['isbn'] = $isbn;
                    $isbns[] = $arr_data[$i]['isbn'];
                    flush();

                }

            } else {

                preg_match_all("/<li class=\"swatchElement unselected\"(.|\n)+<\/li>/U", $data, $matchs);

                foreach ($matchs[0] as $key => $value) {

                    if (strrpos($value, "Hardcover") !== false || strrpos($value, "Paperback") !== false) {

                        preg_match("/<a href=\"(.*)\"/U", $value, $link);

                        if (!empty($link[1])) {

                            $data = $main->curl_function("https://www.amazon.com" . $link[1]);

                            if ($isbn = $main->get_isbn($data)) {

                                $arr_data[$i]['url'] = "https://www.amazon.com" . $link[1];
                                $arr_data[$i]['isbn'] = $isbn;
                                $isbns[] = $arr_data[$i]['isbn'];
                                flush();

                            }

                        }
                        break;
                    }
                }
            }
        }
    }
    
    if (count($isbns)>=10) {
        mysql_query("DELETE FROM bf_bestsellers WHERE category = '$category'");
        foreach ($isbns as $isbn) {
            $isbn = str_replace('-', '',$isbn);            
            $isbn = substr($isbn, 0, 13);
            
            // check if this isbn exsits in db
            $query = mysql_query("SELECT * FROM bf_books WHERE ean = '$isbn' LIMIT 1") or die(mysql_error());
            if (!mysql_num_rows($query))
                $main->fetchAndSaveBook($isbn);
            
            $sql = "INSERT INTO bf_bestsellers (category, ean) values ('$category', '$isbn')";
            mysql_query($sql) or die(mysql_error().' '.$sql);
        }
    }
    
}

