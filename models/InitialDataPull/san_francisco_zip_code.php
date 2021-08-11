<?php

/**
 * COVID-19 Cases by Geography and Date
 * https://data.sfgov.org/COVID-19/COVID-19-Cases-by-Geography-and-Date/d2ef-idww
 * API Endpoint: https://data.sfgov.org/resource/d2ef-idww.json
 */
include($_SERVER['DOCUMENT_ROOT'] . '/models/OrionRecord.php');

(function () {
    // set time limit to something longer
    set_time_limit(900);

    $list_zip = [94102, 94103, 94105, 94107, 94108, 94109, 94110, 94111, 94112, 94114, 94115, 94116, 94117, 94118, 94121, 94122, 94123, 94127, 94129, 94130, 94131, 94132, 94133, 94134, 94158];
    // 94124 has too much location data that exceeds default memory limit using json_decode

    $count = count($list_zip);
    for ($i = 0; $i < $count; $i++) {
        getData($list_zip[$i]);
    }
})();


// unset variables when done to avoid hitting memory limit
function getData($zip_code)
{
    $orion = new OrionRecord('san_francisco_zip_code');
    $url = "https://data.sfgov.org/resource/d2ef-idww.json?id=$zip_code";
    $file = file_get_contents($url);
    $array = json_decode($file, true);
    $length = count($array);
    $orion->setColumns('specimen_collection_date', 'area_type', 'id', 'acs_population', 'new_confirmed_cases', 'cumulative_confirmed_cases');
    for ($i = 0; $i < $length; $i++) {

        $orion->insertRow(
            substr($array[$i]['specimen_collection_date'], 0, 10),
            $array[$i]['area_type'],
            $array[$i]['id'],
            $array[$i]['acs_population'],
            $array[$i]['new_confirmed_cases'],
            $array[$i]['cumulative_confirmed_cases']
        );
    }

    $orion->insertBatch();

    unset($file);
    unset($array);
}