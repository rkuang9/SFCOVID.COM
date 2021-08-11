<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/models/OrionRecord.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/controllers/us/state_name.php');


(function () {
    $yesterday = 0; // check today first, then go 1 day before
    $tries = 0;

    date_default_timezone_set('America/Los_Angeles');

    $orion = new OrionRecord('us_cdc');
    $orion->addQuery('submission_date', '=', date("Y-m-d", time() - $yesterday));
    $orion->orderBy('new_case', 'desc');
    $orion->query('assoc', 'new_case as nc', 'tot_cases as tc', 'state as s', 'new_death as nd', 'tot_death as td');
    $results = $orion->getResult();

    while ($orion->getRowCount() < 50 && $tries != 20) {
        $yesterday += 86400;
        $tries++;
        $orion = new OrionRecord('us_cdc');
        $orion->addQuery('submission_date', '=', date("Y-m-d", time() - $yesterday));
        $orion->orderBy('new_case', 'desc');
        $orion->query('assoc', 'new_case as nc', 'tot_cases as tc', 'state as s', 'new_death as nd', 'tot_death as td');
        $results = $orion->getResult();
    }

    $count = count($results);
    for ($i = 0; $i < $count; $i++) {
        $results[$i]['s'] = getStateName($results[$i]['s']);
    }

    echo json_encode($results);
})();
