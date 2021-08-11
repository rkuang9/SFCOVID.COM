<?php
include('/var/www/html/models/OrionBulkRecord.php');

/**
 * Ran as a cron job, insert today's data for country (more info than country_who)
 * First value of the column header has an invisible ﻿ in front, it must be included when accessing the element
 */
(function () {
    // begin counting script duration
    $start = microtime(true);

    // download csv file and convert to an associative array
    $orion = new OrionBulkRecord('country_detailed_who');
    $csv = $orion->parseCSV('https://covid19.who.int/WHO-COVID-19-global-table-data.csv');

    $rows_inserted = 0;

    $count = count($csv);
    for ($i = 0; $i < $count - 1; $i++) {
        $orion->country = $csv[$i]['﻿Name'];
        $orion->who_region = $csv[$i]['WHO Region'];
        $orion->cases_cumulative = floatval($csv[$i]['Cases - cumulative total']);
        $orion->cases_cumulative_per_hundred_thousand = floatval($csv[$i]['Cases - cumulative total per 100000 population']);
        $orion->cases_new_last_seven_days = floatval($csv[$i]['Cases - newly reported in last 7 days']);
        $orion->cases_new_last_twenty_four_hours = floatval($csv[$i]['Cases - newly reported in last 24 hours']);
        $orion->deaths_cumulative = floatval($csv[$i]['Deaths - cumulative total']);
        $orion->deaths_cumulative_per_hundred_thousand = floatval($csv[$i]['Deaths - cumulative total per 100000 population']);
        $orion->deaths_new_last_seven_days = floatval($csv[$i]['Deaths - newly reported in last 7 days']);
        $orion->deaths_new_last_twenty_four_hours = floatval($csv[$i]['Deaths - newly reported in last 24 hours']);
        $orion->transmission_classification = $csv[$i]['Transmission Classification'];

        if ($orion->insert('replace'))
            $rows_inserted++;
    }

    $end = microtime(true);

    $orion->logIssue('Schedule', $orion->getTable(), basename(__FILE__) . " completed with $rows_inserted inserts.", $end - $start);
})();
