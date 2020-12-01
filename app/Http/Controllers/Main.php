<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use voku\helper\HtmlDomParser;

class Main extends BaseController
{
    function test($query)
    {
        echo $query . PHP_EOL;
        $params = [
            'action' => 'parse',
            'format' => 'json',
            'page' => $query, //'Mahatma Gandhi',
            'prop' => 'text',
            // 'section' => 'info'
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_URL, 'https://en.wikipedia.org/w/api.php?' . http_build_query($params));

        $res = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        echo "<pre>";
        $text = json_decode($res, true);
        $sideSummary =  $text['parse']['text']['*'];

        // print_r($text);

        $wikiBaseUrl = "https://en.wikipedia.org/";
        $datesMap = [
            'Born',
            'Died',
            'Published',
            'Built',
            'Date',
            'Release date',
            'First edition',
            'Latest edition',
            'Founded'
        ];
        $months = "January|February|March|April|May|June|July|August|September|October|November|December";
        echo $dateRegx = "/(\d{4}-\d{2}-\d{1,2})|(\d{1,2}\s($months)\s\d{4})|(\d{4}-\d{2,4})/i";
        // die("HALTING");
        $dom = HtmlDomParser::str_get_html($sideSummary);

        $table = $dom->find("table tr");
        $dates = [];

        foreach ($table as $row) {
            $th = $row->findOne('th');
            $td = $row->findOne('td');

            if (in_array(trim($th->textContent), $datesMap)) {
                // echo '<b>DATE </b>';
                // echo "$th->textContent : $td->textContent" . PHP_EOL;
                $td->textContent = preg_replace("/[^0-9A-z]/u", '-', $td->textContent,);

                preg_match_all($dateRegx, $td->textContent, $extractedDates);

                // echo "$th->textContent : $td->textContent" . PHP_EOL;
                // print_r($extractedDates);

                if (!empty($extractedDates) && !empty($extractedDates[1])) {
                    $date =  date('Y-m-d', strtotime($extractedDates[0][0]));
                    if ($date !== "1970-01-01")
                        $dates[$th->textContent] = $date;
                    else
                        $dates[$th->textContent] = $extractedDates[0][0];
                }
            }
            // print_r($th);
            // print_r($td);

            // echo "$th->textContent : $td->textContent" . PHP_EOL;
        }
        print_r($dates);
    }
}
