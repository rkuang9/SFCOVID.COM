<?php
include('/var/www/html/models/OrionBulkRecord.php');

/**
 * Ran as a cron job, insert today's data by country every 2 hours
 * First value of the column header has an invisible ﻿ in front, it must be included when accessing the element
 */

(function () {
    $array = ['date_reported', 'country_code', 'country', 'who_region', '@new_cases', '@cumulative_cases', '@new_deaths', '@cumulative_deaths'];
    $custom_sql = "SET new_cases = CAST(@new_cases as SIGNED),
                    cumulative_cases = CAST(@cumulative_cases as SIGNED),
                    new_deaths = CAST(@new_deaths as SIGNED),
                    cumulative_deaths = CAST(@cumulative_deaths as SIGNED)";

    $orion = new OrionBulkRecord('country_who');
    echo $orion->insertCSV($orion->downloadFile('https://covid19.who.int/WHO-COVID-19-global-data.csv'), ',', '"', '\n', 1, $array, $custom_sql);
})();


/*
(function () {
    // begin counting script duration
    $start = microtime(true);

    // download csv file and convert to an associative array
    $orion = new OrionBulkRecord('country_who');
    $csv = $orion->parseCSV('https://covid19.who.int/WHO-COVID-19-global-data.csv');

    date_default_timezone_set('America/Los_Angeles');
    $today = date("Y-m-d", time() - 86400);

    $rows_inserted = 0;

    $count = count($csv);
    for ($i = 0; $i < $count - 1; $i++) {
        if ($csv[$i]['﻿Date_reported'] == $today) {
            $orion->date_reported = $csv[$i]['﻿Date_reported'];
            $orion->country_code = $csv[$i]['Country_code'];
            $orion->country = $csv[$i]['Country'];
            $orion->who_region = $csv[$i]['WHO_region'];
            $orion->new_cases = intval($csv[$i]['New_cases']);
            $orion->cumulative_cases = intval($csv[$i]['Cumulative_cases']);
            $orion->new_deaths = intval($csv[$i]['New_deaths']);
            $orion->cumulative_deaths = intval($csv[$i]['Cumulative_deaths']);

            if ($orion->insert('ignore'))
                $rows_inserted++;
        }
    }

    $end = microtime(true);

    $orion->logIssue('Schedule', $orion->getTable(), basename(__FILE__) . " completed with $rows_inserted inserts.", $end - $start);
})();*/

