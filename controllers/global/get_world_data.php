<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/models/OrionRecord.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/controllers/redirect.php');


(function () {
    $yesterday = 0;
    $tries = 0;

    date_default_timezone_set('America/Los_Angeles');

    $delay = new OrionRecord('data_delay');
    $delay->get('target_table', 'country_who');

    $orion = new OrionRecord('country_who');
    $orion->joinTable('country_detailed_who', 'JOIN');
    $orion->addJoinQuery('country_who.country', '=', 'country_detailed_who.country');
    $orion->addQuery('date_reported', '=', date("Y-m-d", time() - $delay->time_delay));
    $orion->orderBy('country_who.cumulative_cases', 'desc');
    $orion->query('assoc', 'country_who.new_cases as nc', 'country_who.cumulative_cases as cc',
        'country_who.new_deaths as nd', 'country_who.cumulative_deaths as cd',
        'country_who.country as c', 'country_who.country_code as co',
        'country_detailed_who.cases_cumulative_per_hundred_thousand as cp');
    $results = $orion->getResult();

    while ($orion->getRowCount() == 0 && $tries != 20) {
        $yesterday += 86400;
        $tries++;
        $orion = new OrionRecord('country_who');
        $orion->joinTable('country_detailed_who', 'JOIN');
        $orion->addJoinQuery('country_who.country', '=', 'country_detailed_who.country');
        $orion->addQuery('date_reported', '=', date("Y-m-d", time() - $yesterday));
        $orion->orderBy('country_who.cumulative_cases', 'desc');
        $orion->query('assoc', 'country_who.new_cases as nc', 'country_who.cumulative_cases as cc',
            'country_who.new_deaths as nd', 'country_who.cumulative_deaths as cd',
            'country_who.country as c', 'country_who.country_code as co',
            'country_detailed_who.cases_cumulative_per_hundred_thousand as cp');
        $results = $orion->getResult();
    }

    echo json_encode($results);
})();
