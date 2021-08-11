<?php
/**
 * cron job - every 2 hours
 * COVID-19 Hospitalizations
 * https://data.sfgov.org/COVID-19/COVID-19-Hospitalizations/nxjg-bhem
 * API Endpoint: https://data.sfgov.org/resource/nxjg-bhem.json
 */

(function () {
    // begin counting script duration
    $start = microtime(true);

    include('/var/www/html/models/OrionBulkRecord.php');
    date_default_timezone_set('America/Los_Angeles');

    $orion = new OrionBulkRecord('san_francisco_hospital');
    $result = $orion->parseJSON('https://data.sfgov.org/resource/nxjg-bhem.json',
        '?reportdate=' . date('Y-m-d', time() - 60 * 60 * 24 * 5)."T00:00:00.000");

    $orion->setColumns('reportdate', 'hospital', 'dphcategory', 'covidstatus', 'patientcount');

    $count = count($result);
    for ($i = 0; $i < $count; $i++) {
        $orion->insertRow(
            substr($result[$i]->reportdate, 0, 10),
            $result[$i]->hospital,
            $result[$i]->dphcategory,
            $result[$i]->covidstatus,
            $result[$i]->patientcount
        );
    }

    $num_inserts = $orion->insertBatch('replace');

    $end = microtime(true);
    $orion->logIssue('Schedule', $orion->getTable(), basename(__FILE__) . " completed with $num_inserts inserts.", $end - $start);
})();