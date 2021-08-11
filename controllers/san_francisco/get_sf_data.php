<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/models/OrionRecord.php');


(function () {
    $yesterday = 0; // check today first, then go 1 day before
    $tries = 0;

    date_default_timezone_set('America/Los_Angeles');

    $orion = new OrionRecord('san_francisco_zip_code');
    $orion->addQuery('specimen_collection_date', '=', date("Y-m-d", time() - $yesterday));
    $orion->orderBy('id', 'asc');
    $orion->query('assoc_slim', 'acs_population as ap', 'new_confirmed_cases as ncc', 'cumulative_confirmed_cases as ccc');
    $results = $orion->getResult();

    while ($orion->getRowCount() == 0 && $tries != 20) {
        $yesterday += 86400;
        $tries++;

        $orion = new OrionRecord('san_francisco_zip_code');
        $orion->addQuery('specimen_collection_date', '=', date("Y-m-d", time() - $yesterday));
        $orion->orderBy('id', 'asc');
        $orion->query('assoc_slim', 'id as zip', 'acs_population as ap', 'new_confirmed_cases as ncc', 'cumulative_confirmed_cases as ccc', 'specimen_collection_date as date');
        $results = $orion->getResult();
    }

    echo json_encode($results);
})();
