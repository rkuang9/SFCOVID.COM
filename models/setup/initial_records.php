<?php
/**
 * Populate tables view_count and data_delay with initial records
 */
include_once($_SERVER['DOCUMENT_ROOT'] . '/models/OrionRecord.php');

// populate view_count
(function () {
    $page = ['sanfrancisco', 'us', 'statedetail', 'global', 'login', 'registration'];
    $pages = count($page);

    $orion = new OrionRecord('view_count', 'record_id');
    for ($i = 0; $i < $pages; $i++) {
        $orion->page = $page[$i];
        $orion->views = 0;
        $orion->insert('ignore');
    }

    unset($orion);
})();


// populate data_delay, time_delayed is a generated column based off days_delayed
(function () {
    $table = ['us_cdc', 'country_who', 'country_detailed_who',
        'san_francisco_case_count', 'san_francisco_hospital', 'san_francisco_zip_code'];
    $days_delayed = [1, null, null, 3, 2, 3];
    $tables = count($table);

    $orion = new OrionRecord('data_delay', 'record_id');
    for ($i = 0; $i < $tables; $i++) {
        $orion->target_table = $table[$i];
        $orion->days_delayed = $days_delay[$i];
        $orion->insert('ignore');
    }

    unset($orion);
})();

