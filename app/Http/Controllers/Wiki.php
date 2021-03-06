<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use voku\helper\HtmlDomParser;

class Wiki extends BaseController
{
    function parseData($query)
    {
        $query = ucwords($query);
        echo "Data for $query";
        $params = [
            'action' => 'parse',
            'format' => 'json',
            'page' => $query,
            'prop' => 'text'
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

        if (!empty($err)) {
            print_r($err);
            exit;
        }

        $text = json_decode($res, true);
        if (empty($text['parse'])) {
            print_r($text);
            exit("NO data");
        }

        $wikiBaseUrl = "https://en.wikipedia.org/";

        $sideSummary =  $text['parse']['text']['*'];
        $dom = HtmlDomParser::str_get_html($sideSummary);
        dd($this->parseFields($dom));
    }

    /**
     * Extraxt fields from provided dom
     * @param object $dom
     * @return array
     */
    function parseFields($dom)
    {
        $months = "January|February|March|April|May|June|July|August|September|October|November|December";
        $dateRegx = "/(\d{4}-\d{2}-\d{1,2})|(\d{1,2}\s($months)\s\d{4})|(\d{4}-\d{2,4})|(\d{4})/i";

        $table = $dom->find("table tr");
        $data = [];
        $dates = [];
        $locations = [];

        foreach ($table as $row) {
            $th = $row->findOne('th');
            $td = $row->findOne('td');

            # parsing dates
            if (in_array(trim($th->textContent), $this->dateMap())) {
                $td->textContent = preg_replace("/[^0-9A-z]/u", '-', $td->textContent,);
                preg_match_all($dateRegx, $td->textContent, $extractedDates);

                if (!empty($extractedDates) && !empty($extractedDates[1])) {

                    preg_match("/[\-A-Z]/i", $extractedDates[0][0], $matches);
                    $date =  date('Y-m-d', strtotime($extractedDates[0][0]));
                    if (count($matches) && $date !== "1970-01-01")
                        $dates[$th->textContent] = $date;
                    else
                        $dates[$th->textContent] = $extractedDates[0][0];
                }
            }
            # parsing locations
            if (in_array(trim($th->textContent), $this->locationMap())) {
                // $td->textContent = preg_replace("/(&#\w+;)/i", ', ', $td->textContent);
                if ($th->textContent == 'Coordinates') {
                    $locations['Coordinates'] =   [
                        "latitude" => $td->findOne('span.latitude')->textContent,
                        "longitude" => $td->findOne('span.longitude')->textContent
                    ];
                } else
                    $locations[$th->textContent] = $td->textContent;
            }
        }

        $data['dates'] = $dates;
        $data['locations'] = $locations;

        return $data;
    }

    /**
     *Posible labels for dates
     *@return array
     */
    function dateMap()
    {
        return [
            'Born',
            'Died',
            'Published',
            'Built',
            'Date',
            'Release date',
            'First edition',
            'Latest edition',
            'Founded',
            'Designated',
            'Construction',
            'Completed'
        ];
    }
    /**
     *Posible labels for locations
     *@return array
     */
    function locationMap()
    {
        return [
            'Location',
            'Coordinates'
        ];
    }
}
