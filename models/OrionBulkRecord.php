<?php

require_once "OrionRecord.php";

class OrionBulkRecord extends OrionRecord
{
    /**
     * Assumes a table with an auto incrementing primary key. Not intended for front end use
     *
     * @param $file_location ,       location of csv file on disk, use / instead of \ for Windows
     * @param $fields_terminated ,   the delimiter separating each value in a row
     * @param $enclosed ,            optional: adds the OPTIONALLY ENCLOSED BY clause in case values have quotations
     * @param $lines_terminated ,    what indicates a new row, typically a newline '\n'
     * @param $ignore_lines ,        optional: choose which line/row of the file to start inserting from
     * @param $columns ,             an array of columns mapping the csv column to the table, order matters
     * @param $custom_sql ,          optional: additional SQL code such as SET, NULLIF, CAST
     *
     * @return bool,                 return TRUE if successful or FALSE if failure
     */
    function insertCSV($file_location, $fields_terminated, $enclosed, $lines_terminated, $ignore_lines, $columns, $custom_sql = null)
    {
        // replace backwards slashes with a forward slash to not trigger SQLSTATE[HY000] General error: 1290
        $file_location = str_replace("\\", '/', $file_location);

        $this->query = "LOAD DATA INFILE '$file_location' IGNORE INTO TABLE $this->db_table ";
        $this->query .= "FIELDS TERMINATED BY '\\$fields_terminated' ";

        if ($enclosed != '') {
            $this->query .= "OPTIONALLY ENCLOSED BY '$enclosed' ";
        }

        $this->query .= "LINES TERMINATED BY '$lines_terminated' ";

        if (is_int($ignore_lines)) {
            $this->query .= "IGNORE $ignore_lines LINES ";
        }

        // add columns
        $this->query .= "(";

        $number_columns = count($columns);
        for ($i = 0; $i < $number_columns; $i++) {
            if ($i === $number_columns - 1) {
                $this->query .= "$columns[$i])";
            }
            else {
                $this->query .= "$columns[$i], ";
            }
        }

        // additional statements like SET, NULLIF
        if ($custom_sql) {
            $this->query .= " $custom_sql";
        }

        try {
            $local_stmt = $this->pdo->prepare($this->query);
            return $local_stmt->execute();
        }
        catch (PDOException $e) {
            self::console_error('Error Message: ' . $e->getMessage() . ' Error Code: ' . $e->getCode());
            return false;
        }
    }



    /**
     * Read csv file from url into an associative array
     *
     * US cases cumulative CDC:      https://data.cdc.gov/api/views/9mfq-cb36/rows.csv
     * Global cases cumulative WHO:  https://covid19.who.int/WHO-COVID-19-global-data.csv
     *
     * @param $url ,            direct link to csv file
     * @return array ,          return the csv as an associative array
     */
    function parseCSV($url) {
        $csv_file = file_get_contents($url);
        $rows = array_map('str_getcsv', explode("\n", $csv_file));

        $header = array_shift($rows);
        $csv = array();

        // ensure the number of columns for each row match the number of column keys
        $column_count = count($header);

        foreach ($rows as $row) {
            if (count($row) == $column_count) {
                $csv[] = @array_combine($header, $row);
            }
        }

        return $csv;
    }


    /**
     * Read csv file from url into an associative array
     *
     * @param $url ,                direct link to json file
     * @param null $parameters ,    filtering parameters like GET (?column_one=val_one&column_two=val_two)
     * @param bool $associative ,   if TRUE return an associative array, if FALSE return an object, default false
     * @return mixed ,              return associative array or object of the JSON
     */
    function parseJSON($url, $parameters = null, $associative = false) {
        return json_decode((file_get_contents($url.$parameters)), $associative);
    }



    /**
     * Download a file if the URL is a direct link such as the following:
     *
     * US cases cumulative CDC:      https://data.cdc.gov/api/views/9mfq-cb36/rows.csv
     * Global cases cumulative WHO:  https://covid19.who.int/WHO-COVID-19-global-data.csv
     *
     * @param $url ,            download link such as a csv file from the World Health Organization
     * @return string|false,    return full path on disk to the downloaded file, FALSE if failure
     */
    function downloadFile($url)
    {
        // get file name from url
        $filename = basename($url);

        // find index of the file's extension
        $index_file_extension = strripos($filename, '.');

        // capture extension of the downloaded file
        $extension = substr($filename, $index_file_extension, strlen($filename));

        // remove extension from file name so that we have file name and extension separated
        $filename = substr($filename, 0, $index_file_extension);

        // download the file
        $file = file_get_contents($url);

        // if download is unsuccessful, log the error and exit
        if ($file === FALSE) {
            $this->logIssue('Error', $this->db_table, "Failed to download file from $url", '');
            return false;
        }

        $default_location = $this->getSecureFilePriv();
        if ($default_location) {
            $directory = $default_location . $filename . '-' . date("Y-m-d") . $extension;
        }
        else {
            $this->logIssue('Error', $this->db_table, "Downloaded succeeded but failed to retrieve secure_file_priv from the MySQL server", '');
            return false;
        }

        // move the downloaded file to specified directory
        $save = file_put_contents($directory, $file);

        // if placing file into directory is unsuccessful, log the error and exit
        if ($save === FALSE) {
            $this->logIssue('Error', $this->db_table, "Download succeeded but failed to save downloaded file to $directory", '');
            return false;
        }

        return $directory;
    }




    /**
     * Get the name of file from a url as it would be when downloaded with downloadFile()
     *
     * @param $url ,         download link such as a csv file from the World Health Organization
     * @return string,      the file name modified to include the current date as done in downloadData()
     */
    function getFileName($url, $isJSON)
    {
        // get file name from url
        $full_filename = basename($url);

        // find index of the file's extension, last index of '.'
        $index_file_extension = strripos($full_filename, '.');

        // capture extension of the downloaded file
        $extension = substr($full_filename, $index_file_extension + 1, strlen($full_filename));

        // remove extension from file name so that we have file name and extension separated
        $filename = substr($full_filename, 0, $index_file_extension);

        $result = $filename . '-' . date("Y-m-d") . '.' . $extension;

        if ($isJSON) {
            $result = substr($result, 0, $index_file_extension + 16);
        }

        return $result;
    }



    /**
     * Get the full directory of the file as it would be when downloaded with downloadFile()
     *
     * @param $url ,         download link such as a csv file from the World Health Organization
     * @return string,      the directory of the file if it were downloaded using downloadData()
     */
    function getFileDirectory($url)
    {
        return $this->getSecureFilePriv() . $this->getFileName($url, null);
    }



    /**
     * Get location on disk that MySQL allows LOAD DATA INFILE from.
     * Used by getFileName() and getFileDirectory()
     *
     * @return string|false ,        return the secure_file_priv directory, FALSE on failure
     */
    function getSecureFilePriv()
    {
        $local_stmt = $this->pdo->prepare('SHOW VARIABLES LIKE "secure_file_priv"');
        $local_stmt->execute();
        $local_stmt->setFetchMode(PDO::FETCH_ASSOC);
        $directory = $local_stmt->fetchAll()[0]['Value'];

        if (!is_null($directory)) {
            // replace backwards slashes with a forward slash to not trigger SQLSTATE[HY000] General error: 1290
            return str_replace('\\', '/', $directory);
        }
        return false;
    }
}
