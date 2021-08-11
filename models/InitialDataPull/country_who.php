<?php
include($_SERVER['DOCUMENT_ROOT'] . '/models/OrionBulkRecord.php');

(function () {
    $array = ['date_reported', 'country_code', 'country', 'who_region', '@new_cases', '@cumulative_cases', '@new_deaths', '@cumulative_deaths'];
    $custom_sql = "SET new_cases = CAST(@new_cases as SIGNED),
                    cumulative_cases = CAST(@cumulative_cases as SIGNED),
                    new_deaths = CAST(@new_deaths as SIGNED),
                    cumulative_deaths = CAST(@cumulative_deaths as SIGNED)";

    $orion = new OrionBulkRecord('country_who');
    echo $orion->insertCSV($orion->downloadFile('https://covid19.who.int/WHO-COVID-19-global-data.csv'), ',', '"', '\n', 1, $array, $custom_sql);
})();
