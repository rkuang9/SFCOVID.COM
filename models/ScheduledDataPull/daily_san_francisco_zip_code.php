<?php
/**
 * cron job - every 2 hours
 * COVID-19 Cases by Geography and Date, used by /views/sanfrancisco.php via AJAX to populate map
 * https://data.sfgov.org/COVID-19/COVID-19-Cases-by-Geography-and-Date/d2ef-idww
 * API Endpoint: https://data.sfgov.org/resource/d2ef-idww.json
 */

(function () {
    // begin counting script duration
    $start = microtime(true);

    include('/var/www/html/models/OrionBulkRecord.php');
    date_default_timezone_set('America/Los_Angeles');

    $orion = new OrionBulkRecord('san_francisco_zip_code');
    $result = $orion->parseJSON("https://data.sfgov.org/resource/d2ef-idww.json",
        "?specimen_collection_date=" . date('Y-m-d', time() - 60 * 60 * 24 * 5)."T00:00:00.000", true);

    $orion->setColumns('specimen_collection_date', 'area_type', 'id', 'acs_population', 'new_confirmed_cases', 'cumulative_confirmed_cases');

    $count = count($result);
    for ($i = 0; $i < $count; $i++) {
        if (strlen($result[$i]['id']) == 5 && $result[$i]['id'][0] == '9') {
            $orion->insertRow(
                substr($result[$i]['specimen_collection_date'], 0, 10),
                $result[$i]['area_type'],
                $result[$i]['id'],
                $result[$i]['acs_population'],
                $result[$i]['new_confirmed_cases'],
                $result[$i]['cumulative_confirmed_cases']);
        }
    }

    $num_inserts = $orion->insertBatch('replace');

    $end = microtime(true);
    $orion->logIssue('Schedule', $orion->getTable(), basename(__FILE__) . " completed with $num_inserts inserts.", $end - $start);
})();