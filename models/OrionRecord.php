<?php
/** ORM for database communication */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class OrionRecord
{
    protected PDO       $pdo;                   // PDO object representing connection between PHP and database server
    protected PDOStatement  $stmt;              // communicate with database server
    protected array     $fetch_result = [];          // array of data retrieved from database for PDO::FETCH_ASSOC fetch mode
    protected int       $row_count = 0;         // number of rows retrieved from database
    protected string    $db_table;              // table this object works with, set in constructor
    protected string    $query = '';            // SQL statement string for executing a query
    protected string    $join = '';             // join query with another table
    protected bool      $join_flag = false;     // determine if addJoinQuery has been called yet, decided to use AND or not
    protected array     $columns = [];          // array containing $db_table column names, populated in the constructor
    protected string    $primary_key;           // table's (main) primary key
    protected string    $orderby = '';          // order by clause, append to the SELECT query before execution in query()
    protected string    $limit = '';            // limit clause, append to the SELECT query before execution in query()
    protected array     $values_stack = [];     // keep track the order of values for positional binding
    protected int       $rows_per_insert = 10;  // number of rows per insert statement
    protected string    $placeholder = '';      // placeholders enclosed by parentheses

    /* database connection details */
    protected string $dbhost = 'localhost';                  // ip address of SQL server
    protected string $dbdatabase = '';                       // name of database/schema
    protected string $dbuser = '';                           // an account to use on SQl server
    protected string $dbpassword = '';                       // account password
    protected string $dbcharset = 'utf8mb4';                 // character encoding for database values
    protected array $dboptions = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,        // error reporting, throws exceptions
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,   // default fetching returns an array indexed by column name
        PDO::ATTR_EMULATE_PREPARES => true,                 // enables or disables emulation of prepared statements
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true          // LOAD DATA INFILE won't work without this
    ];


    /**
     * Constructor will create a PDO object and initialize class properties for each table column.
     *
     * @param $table ,                   database table on which this record will work on
     * @param null $primary_key,         primary key of the table, constructor will query for it if not provided
     * @param mixed ...$credentials ,    optional five parameters to set database connection details, any less are ignored
     */
    function __construct($table, $primary_key = null, ...$credentials)
    {
        $this->db_table = $table;

        // set database credentials to work on a different database if provided
        if (count($credentials) === 5) {
            $this->dbhost = $credentials[0];
            $this->dbdatabase = $credentials[1];
            $this->dbuser = $credentials[2];
            $this->dbpassword = $credentials[3];
            $this->dbcharset = $credentials[4];
        }

        try {
            $this->pdo = new PDO("mysql:host=$this->dbhost;dbname=$this->dbdatabase;charset=$this->dbcharset", $this->dbuser, $this->dbpassword, $this->dboptions);
        }
        catch (PDOException $e) {
            self::console_error('Error Message: ' . $e->getMessage() . ' Error Code: ' . $e->getCode());
        }

        // set primary key if provided, else fetch it from the database table
        if (!is_null($primary_key)) {
            $this->primary_key = $primary_key;
        }
        else {
            try {
                // Retrieve table's primary key. If two exist, use the first one due to safe update mode ON for the SQL server
                $this->manualQuery("SHOW KEYS FROM $table WHERE Key_name = 'PRIMARY'");
                $this->primary_key = $this->fetch_result[0]['Column_name'];
                $this->fetch_result = [];
                $this->row_count = 0;
            } catch (PDOException $e) {
                self::console_error('Retrieving primary key. Error Message: ' . $e->getMessage() . ' Error Code: ' . $e->getCode());
            }
        }
    }



    /**
     * Retrieves a record by primary key. Always returns one record if successful since primary keys are unique.
     *
     * @param $unique,              unique column to search by, default by primary key unless $unique_value provided
     * @param null $unique_value,   unique column's value
     * @return bool,                returns TRUE if record is retrieved or FALSE indicating record doesn't exist
     */
    function get($unique, $unique_value = null): bool
    {
        try {
            // retrieving by primary key
            if ($unique_value == null) {
                $this->query = "SELECT * from $this->db_table WHERE $this->primary_key = ?";
                $this->stmt = $this->pdo->prepare($this->query);
                $this->stmt->bindParam(1, $unique);
            }
            // retrieving by a unique key
            else {
                $this->query = "SELECT * from $this->db_table WHERE $unique = ?";
                $this->stmt = $this->pdo->prepare($this->query);
                $this->stmt->bindParam(1, $unique_value);
            }

            $this->stmt->execute();
            $this->stmt->setFetchMode(PDO::FETCH_INTO, $this);
            $this->stmt->fetch();
            $this->row_count = $this->stmt->rowCount();
            return $this->row_count > 0;
        }
        catch (PDOException $e) {
            self::console_error('Error Message: ' . $e->getMessage() . ' Error Code: ' . $e->getCode());
            return false;
        }

    }


    function __set($name, $value)
    {
        $this->$name = $value;
        //$this->$name = new ForeignKey($name, $value, $this->db_table);
        $this->columns[] = $name;
    }



    /**
     * Construct an INSERT query string using column values set by the user.
     * Null value columns and the primary key column are ignored
     * @param $insert_ignore ,   accepts 'replace' and 'ignore' and changes query into REPLACE or INSERT IGNORE
     *
     * @return bool,        return TRUE for successful insert, FALSE on failure
     */
    function insert($insert_ignore = null): bool
    {
        if (strtolower($insert_ignore) === 'replace') {
            $this->query = "REPLACE ";
        }
        else if (strtolower($insert_ignore) === 'ignore') {
            $this->query = "INSERT IGNORE ";
        }
        else if (is_null($insert_ignore)) {
            $this->query = "INSERT ";
        }
        $this->query .= "INTO $this->db_table (";

        // remove the primary key from the values being inserted
        $column_length = count($this->columns);
        for ($i = 0; $i < $column_length; $i++) {
            if (strtolower($this->columns[$i]) === strtolower($this->primary_key)) {
                unset($this->columns[$i]);
                $i++;
            }
        }
        // combine columns in to a string delimited by a comma
        $this->query .= implode(',', $this->columns) . ") VALUES (";


        $column_length = count($this->columns);
        for ($j = 0; $j < $column_length; $j++) {
            if ($j === $column_length - 1)
                $this->query .= "?)";
            else
                $this->query .= "?, ";
        }

        try {
            $local_stmt = $this->pdo->prepare($this->query);

            // $positional_column holds the the column names corresponding to the order they should be bound
            // bind all positional parameters, $i starts at 1 because bindParam starts at 1
            $num_columns = count($this->columns);
            for ($i = 1; $i < $num_columns + 1; $i++) {
                $local_stmt->bindParam($i, $this->{$this->columns[$i - 1]}); // -1 because $i starts at 1
            }

            return $local_stmt->execute();
        }
        catch (PDOException $e) {
            self::console_error('Error Message: ' . $e->getMessage() . ' Error Code: ' . $e->getCode());
            return false;
        }
    }



    /**
     * Set the columns array
     * Build placeholder values for inserts.
     *
     * @param mixed ...$columns
     */
    public function setColumns(...$columns)
    {
        $this->columns = $columns;

        $count = count($columns);
        for ($i = 0; $i < $count; $i++) {
            if ($i === 0)
                $this->placeholder .= "(?";
            else
                $this->placeholder .= ", ?";
        }

        $this->placeholder .= ") ";
    }



    /**
     * Set values to be inserted in insertBatch()
     *
     * @param mixed ...$values
     */
    public function insertRow(...$values)
    {
        $count = count($values);
        for ($i = 0; $i < $count; $i++) {
            $this->values_stack[] = $values[$i];
        }
    }



    /**
     * Insert the current batch of insert values
     * @param $insert_type ,        accepts 'replace' and 'ignore' and changes query into REPLACE or INSERT IGNORE
     * @param $amount ,             number of values to insert per query
     *
     * @return int|bool ,           return the number of successful insert statements, FALSE on failure
     */
    public function insertBatch($insert_type = null)
    {
        if (count($this->columns) === 0)
            return;

        $prefix = "INSERT ";
        if (strtolower($insert_type) === 'replace') {
            $prefix = "REPLACE ";
        }
        else if (strtolower($insert_type) === 'ignore') {
            $prefix = "INSERT IGNORE ";
        }
        else if (is_null($insert_type)) {
            $prefix = "INSERT ";
        }
        //$this->query = "$prefix INTO $this->db_table $this->query";
        $prefix .= "INTO $this->db_table (";
        $prefix .= implode(',', $this->columns);
        $prefix .= ") VALUES ";

        // number of columns
        $num_columns = count($this->columns);
        // number of insert statements based on number of rows being inserted per statement
        $num_inserts = intval((count($this->values_stack) / $num_columns) / $this->rows_per_insert);
        // number of inserts remaining if $num_inserts division isn't a whole number
        $num_inserts_remainder = intval((count($this->values_stack) / $num_columns) % $this->rows_per_insert);
        // number of placeholders for the entire single insert statement
        $num_placeholders = $num_columns * $this->rows_per_insert;
        // reverse the array to use array_pop for parameter binding since array_shift is too slow
        $this->values_stack = array_reverse($this->values_stack);
        //echo $prefix.$this->appendPlaceholders($this->rows_per_insert); return 0;
        try {
            $num_successful_inserts = 0;

            for ($i = 0; $i < $num_inserts; $i++) {
                $local_stmt = $this->pdo->prepare($prefix.$this->appendPlaceholders($this->rows_per_insert));

                // bind all positional parameters, $j starts at 1 because bindValue starts at 1
                for ($j = 1; $j < $num_placeholders + 1; $j++) {
                    $local_stmt->bindValue($j, array_pop($this->values_stack));
                }


                $num_successful_inserts += (int)$local_stmt->execute() * $this->rows_per_insert;
            }

            // insert the remaining rows that don't add up to the $this->rows_per_insert
            if ($num_inserts_remainder !== 0) {
                $local_stmt = $this->pdo->prepare($prefix.$this->appendPlaceholders($num_inserts_remainder));
                $num_placeholders = $num_columns * $num_inserts_remainder;

                for ($k = 1; $k < $num_placeholders + 1; $k++) {
                    $local_stmt->bindValue($k, array_pop($this->values_stack));
                }

                $num_successful_inserts += (int)$local_stmt->execute() * $num_inserts_remainder;
            }

            $this->query = '';
            $this->columns = [];
            $this->values_stack = [];

            return $num_successful_inserts;
        }
        catch (PDOException $e) {
            self::console_error('Error Message: ' . $e->getMessage() . ' Error Code: ' . $e->getCode());
            return false;
        }
    }



    /**
     * Add an AND clause to the query.
     * It is up to the user to keep track of enclosing the clauses using the optional fourth parameter.
     *
     * @param $column ,          column from the table being queried
     * @param $operator ,        operator such as + - * /
     * @param $value ,           queued onto $this->values_stack, popped and bound to query before execution in qery()
     * @param $enclose ,          optional: add a parenthesis for blocked OR queries or set 'IN BOOLEAN MODE'
     *
     * @return string,          return the current query string
     */
    function addQuery($column, $operator, $value, $enclose = null): string
    {
        if ($this->query != '') {
            $this->query .= "AND ";
        }

        // start an enclosed clause if required
        if ($enclose === '(') {
            $this->query .= " (";
        }

        // ? for positional placeholder
        if (strtolower($operator) === 'match') {
            $this->query .= "MATCH($column) AGAINST(?)";
        }
        else if (strtolower($operator) === 'match boolean') {
            $this->query .= "MATCH($column) AGAINST(? IN BOOLEAN MODE)";
        }
        else {
            $this->query .= "$column $operator ?";
        }

        // end the enclosed query if required
        if ($enclose === ')') {
            $this->query .= ")";
        }

        $this->query .= " ";

        // add the value to $this->values_stack for parameter binding in query()
        $this->values_stack[] = $value;

        return $this->query;
    }


    /**
     * Add an OR clause to the query. Can only be called after addQuery()
     * It is up to the user to keep track of enclosing the clauses using the optional fourth parameter.
     * This method is nearly identical to addQuery() but kept separate for clarity of use
     *
     * @param $column ,          column from the table being queried
     * @param $operator ,        operator such as + - * /
     * @param $value ,           queued onto $this->values_stack, popped and bound to query before execution in qery()
     * @param $enclose ,         optional: enclose a clause or series of clauses with parentheses, useful if using OR clauses
     *
     * @return string,           return the current query string, FALSE if this method is called without existing conditions
     */
    function addOrQuery($column, $operator, $value, $enclose = null): string
    {
        if ($this->query != '') {
            $this->query .= "OR ";
        }

        // start an enclosed clause if required
        if ($enclose === '(') {
            $this->query .= " (";
        }

        // ? for positional placeholder
        if (strtolower($operator) === 'match') {
            $this->query .= "MATCH($column) AGAINST(?)";
        }
        else if (strtolower($operator) === 'match boolean') {
            $this->query .= "MATCH($column) AGAINST(? IN BOOLEAN MODE)";
        }
        else {
            $this->query .= "$column $operator ?";
        }

        // end the enclosed query if required
        if ($enclose === ')') {
            $this->query .= ")";
        }

        $this->query .= " ";

        // add the value to $this->values_stack for parameter binding in query()
        $this->values_stack[] = $value;

        return $this->query;
    }


    /**
     * Execute query after providing conditions using addQuery()
     * Optional parameters to choose which columns to return otherwise all columns are retunred using asterisk *
     * Does not use placeholder parameters for optional parameters
     *
     * @param $style,           specify fetch style: INTO, ASSOC
     * @param mixed $columns,   specifies which columns to retrieve from the table, can accept a variable amount of parameters
     *
     * @return bool,            return TRUE on success or FALSE on failure
     */
    function query($style = null, ...$columns)
    {
        $select_query = 'SELECT ';

        if (count($columns) === 0) {
            $select_query .= '* ';
        }
        else {
            // always include primary table's primary key when selecting limited columns
            if (!in_array($this->db_table . '.' . $this->primary_key, $columns) || !in_array($this->primary_key, $columns)) {
                if ($style !== 'into_slim' && $style !== 'assoc_slim') {
                    $columns[] = $this->db_table . '.' . $this->primary_key;
                }
            }
            $select_query .= implode(', ', $columns) . ' ';
        }
        $select_query .= "FROM $this->db_table " . $this->join;

        // only add a WHERE clause if conditions are specified with addQuery() and addOrCondition()
        if ($this->query != '') {
            $select_query .= "WHERE ";
        }
        // combine all the query strings into one
        $select_query .= $this->query . $this->orderby . $this->limit;

        if (strtolower($style) === 'test') {
            return $select_query;
        }

        try {
            $this->stmt = $this->pdo->prepare($select_query);

            // bind all positional parameters, $i starts at 1 because bindParam starts at 1
            $values_count = count($this->values_stack);
            for ($i = 1; $i < $values_count + 1; $i++) {
                $this->stmt->bindParam($i, $this->values_stack[$i - 1]); // -1 because $i starts at 1
            }

            $result = $this->stmt->execute();

            // choose fetch style: associative array vs directly into this object
            if (strtolower($style) === 'assoc' || strtolower($style) === 'assoc_slim') {
                $this->stmt->setFetchMode(PDO::FETCH_ASSOC);
                $this->fetch_result = $this->stmt->fetchAll();
            }
            else {
                $this->stmt->setFetchMode(PDO::FETCH_INTO, $this);
            }

            $this->row_count = $this->stmt->rowCount();
            $this->query = '';  // reset the query so another may be called
            return $result;
        }
        catch (PDOException $e) {
            self::console_error('Error Message: ' . $e->getMessage() . ' Error Code: ' . $e->getCode());
            return false;
        }
    }


    /**
     * Called after query() to iterate through query results when using fetch mode FETCH_INTO.
     *
     * @return bool,    return TRUE and map row columns into this object, FALSE if no result or results left
     */
    function next()
    {
        if ($this->row_count > 0) {
            return $this->stmt->fetch();
        }
        return false;
    }


    /**
     * Add a join clause to the SELECT query
     *
     * @param $type ,        type of join such as JOIN, LEFT JOIN, etc
     * @param $table ,       table to join with
     *
     * @return string,      return the current join string so far
     */
    function joinTable($table, $type = "JOIN"): string
    {
        $this->join .= "$type $table ON ";
        $this->join_flag = false;
        return $this->join;
    }


    /**
     * Add the join conditions for the most recent joinTable() call.
     * If tables share common column names, the table parameters should have the table prepended
     *
     * @param $tableA ,              join condition with tableB
     * @param $operator ,            comparison operator between tableA and tableB
     * @param $tableB ,              join condition with tableA
     * @param null $parenthesis ,    optional: specify parentheses for grouping conditions
     *
     * @return string,              return the current join string so far
     */
    function addJoinQuery($tableA, $operator, $tableB, $parenthesis = null): string
    {
        if ($this->join_flag) {
            $this->join .= "AND ";
        }

        if ($parenthesis === '(') {
            $this->join .= ' (';
        }

        //$this->join .= $this->db_table.".$tableA $operator $tableB " ;

        //$this->join .= $this->db_table . ".$tableA ";
        $this->join .= $tableA . " ";

        if (strtolower($operator) === 'contains' || strtolower($operator) === 'startswith' || strtolower($operator) === 'endswith') {
            $this->join .= "LIKE ";
        }
        else {
            $this->join .= "$operator ";
        }


        // if using LIKE searching, prepend the % wildcard
        if (strtolower($operator) === 'contains' || strtolower($operator) === 'endswith') {
            $this->join .= "% ";
        }


        // if using LIKE searching, append the % wildcard
        if (strtolower($operator) === 'contains' || strtolower($operator) === 'startswith') {
            $this->query .= "%";
        }

        $this->join .= "$tableB ";

        if ($parenthesis === ')') {
            $this->join .= ') ';
        }

        $this->join_flag = true;
        return $this->join;
    }


    /**
     * Add the join conditions for the most recent joinTable() call.
     * If tables share common column names, the table parameters should have the table prepended
     *
     * @param $tableA ,             join condition with tableB
     * @param $operator ,           comparison operator between tableA and tableB
     * @param $tableB ,             join condition with tableA
     * @param null $parenthesis ,   optional: enclose a clause or series of clauses with parentheses, useful if using OR clauses
     * @return string,              return the current join string so far
     */
    function addJoinOrQuery($tableA, $operator, $tableB, $parenthesis = null): string
    {
        if ($this->join_flag) {
            $this->join .= "OR ";
        }

        if ($parenthesis === '(') {
            $this->join .= ' (';
        }

        //$this->join .= $this->db_table.".$tableA $operator $tableB " ;

        //$this->join .= $this->db_table . ".$tableA ";
        $this->join .= $tableA. " ";

        if (strtolower($operator) === 'contains' || strtolower($operator) === 'startswith' || strtolower($operator) === 'endswith') {
            $this->join .= "LIKE ";
        }
        else {
            $this->join .= "$operator ";
        }


        // if using LIKE searching, prepend the % wildcard
        if (strtolower($operator) === 'contains' || strtolower($operator) === 'endswith') {
            $this->join .= "% ";
        }


        // if using LIKE searching, append the % wildcard
        if (strtolower($operator) === 'contains' || strtolower($operator) === 'startswith') {
            $this->query .= "%";
        }

        $this->join .= "$tableB ";

        if ($parenthesis === ')') {
            $this->join .= ') ';
        }

        $this->join_flag = true;
        return $this->join;
    }


    /**
     * NOT IMPLEMENTED YET
     * @return bool,    return TRUE on success or FALSE on failure
     */
    function update(): bool
    {
        // if no primary key exists, skip action because it will fail
        if ($this->{$this->primary_key} == '' || is_null($this->{$this->primary_key})) {
            return 0;
        }

        $update_values = [];

        $this->query = '';
        $update_query = "UPDATE $this->db_table SET ";

        $count = count($this->columns);
        for ($i = 0; $i < $count; $i++) {
            if ($this->columns[$i] !== $this->primary_key) {
                if ($this->query == '') {
                    $this->query .= $this->columns[$i]. " = ?";
                }
                else {
                    $this->query .= ", " . $this->columns[$i]. " = ? ";
                }
                $update_values[] = $this->{$this->columns[$i]};
            }
        }
        // primary key will be bound last
        $update_values[] = $this->{$this->primary_key};

        $update_query .= $this->query . " WHERE $this->primary_key = ?";

        try {
            $local_stmt = $this->pdo->prepare($update_query);
            $update_count = count($update_values);

            for ($i = 1; $i < $update_count + 1; $i++) {
                $local_stmt->bindParam($i, $update_values[$i - 1]); // -1 because $i starts at 1
            }

            return $local_stmt->execute();
        }
        catch (PDOException $e) {
            self::console_error('Error Message: ' . $e->getMessage() . ' Error Code: ' . $e->getCode());
            return false;
        }

    }


    /**
     * Delete a record by primary key. Primary key is required. Safe update mode is assumed enabled on the MySQL server
     * Note: Uses a local $stmt because otherwise $this->stmt will be changed and cause wrong behavior for next()
     *
     * @return bool,        returns TRUE if delete is successful or FALSE on failure
     */
    function delete(): bool
    {
        // if no primary key exists, skip action because it will fail
        if ($this->{$this->primary_key} == '' || is_null($this->{$this->primary_key})) {
            return 0;
        }

        $this->query = "DELETE FROM $this->db_table WHERE $this->primary_key = :$this->primary_key";

        try {
            $local_stmt = $this->pdo->prepare($this->query);
            $local_stmt->bindParam(":$this->primary_key", $this->{$this->primary_key});
            return $local_stmt->execute();
        }
        catch (PDOException $e) {
            self::console_error('Error Message: ' . $e->getMessage() . ' Error Code: ' . $e->getCode());
            return false;
        }
    }


    /**
     * Add sorting to the query string. Can sort by multiple columns.
     *
     * @param $column ,          the column to sort results by
     * @param $direction ,       accepts ascending / ASC or and descending / DESC
     */
    function orderBy($column, $direction)
    {
        if ($this->orderby == '') {
            if (strtolower($direction) === 'asc' || strtolower($direction) === 'ascending') {
                $this->orderby .= " ORDER BY $column ASC"; // ascending
            }
            else if (strtolower($direction) === 'desc' || strtolower($direction) === 'descending') {
                $this->orderby .= " ORDER BY $column DESC"; // descending
            }
        }
        else {
            if (strtolower($direction) === 'asc' || strtolower($direction) === 'ascending') {
                $this->orderby .= ", $column ASC"; // ascending
            }
            else if (strtolower($direction) === 'desc' || strtolower($direction) === 'descending') {
                $this->orderby .= ", $column DESC"; // descending
            }
        }
    }


    /**
     * If one parameter provided, limit the number of rows. Example: LIMIT 5
     * If two parameters provided, skip to row $amount[0] and get $amount[1] rows. Example: LIMIT 5, 10
     *
     * @param mixed ...$amount ,     accept up to two integers
     */
    function limit(...$amount)
    {
        $count = count($amount);
        // get the first x rows
        if ($count === 1 && $amount[0] > 0) {
            $this->limit = " LIMIT  $amount[0]";
        } // start at row x, retrieve the next y row
        else if ($count === 2 && $amount[0] >= 0 && $amount[1] > 0) {
            $this->limit = " LIMIT $amount[0], $amount[1]";
        }
    }



    /**
     * Set the amount of rows to insert for insertBatch()
     *
     * @param $amount
     */
    public function insertsPerQuery($amount) {
        $this->rows_per_insert = $amount;
    }



    /**
     * Run an SQL statement as is, results returned in $this->fetch_result. Intended for retrieving records.
     * Only call this internally.
     * Results are held in $this->fetch_result
     *
     * @param $statement ,   SQL statement
     *
     * @return bool,        returns TRUE if record(s) is retrieved or else FALSE indicating record doesn't exist
     */
    protected function manualQuery($statement): bool
    {
        try {
            $local_stmt = $this->pdo->prepare($statement);
            $local_stmt->execute();
            $local_stmt->setFetchMode(PDO::FETCH_ASSOC);
            $this->fetch_result = $local_stmt->fetchAll();
            $this->row_count = $local_stmt->rowCount();

            return $this->row_count > 0;
        }
        catch (PDOException $e) {
            self::console_error('Error Message: ' . $e->getMessage() . ' Error Code: ' . $e->getCode());
            return false;
        }
    }


    /**
     * Turns off autocommit mode. Queries are stored and executed after calling commit()
     * Improves performance.
     *
     * @return bool,        returns TRUE on success, FALSE on failure
     */
    function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }



    /**
     * Commits the queries after beginTransaction() is called
     *
     * @return bool,        returns TRUE on success, FALSE on failure
     */
    function commit(): bool
    {
        return $this->pdo->commit();
    }



    /**
     * Rolls back the queries made after beginTransaction() is called
     *
     * @return bool,        returns TRUE on success, FALSE on failure
     */
    function rollBack(): bool
    {
        return $this->pdo->rollBack();
    }



    /**
     * Create a log on the logs table to log something.
     *
     * @param $type ,            ENUM database field, accepts "Error", "Information", "Warning", else defaults to "Other"
     * @param $table ,           table related to the issue
     * @param $description ,     description of the issue being logged
     * @param $duration ,        duration of action, calculated with microtime(true)
     * @return bool,             return TRUE is log has been created, FALSE on failure
     */
    static function logIssue($type, $table, $description, $duration): bool
    {
        $log = new OrionRecord('logs');
        $log->type = $type;
        $log->table_affected = $table;
        $log->description = $description;
        $log->duration = $duration;
        return $log->insert();
    }


    /**
     * Run an SQL statement as is. Does not modify class properties and returns nothing.
     *
     * @param $statement ,   SQL statement to run
     */
    function executeSQL($statement)
    {
        $this->pdo->query($statement);
    }


    /**
     * @return int,     number of rows held by this object
     */
    function getRowCount(): int
    {
        return $this->row_count;
    }


    // reset object and column values to default so it can be reused on the same table
    function reset()
    {
        $this->pdo = new PDO("mysql:host=$this->dbhost;dbname=$this->dbdatabase;charset=$this->dbcharset", $this->dbuser, $this->dbpassword, $this->dboptions);
        $this->row_count = 0;
        $this->query = '';
        $this->orderby = '';
        $this->limit = '';
        $this->join = '';
        $this->join_flag = '';
        $this->values_stack = [];
        $this->stmt = new PDOStatement();

        $columns = count($this->columns);
        for ($i = 0; $i < $columns; $i++) {
            unset($this->{$this->columns[$i]});
        }
        $this->columns = [];
    }


    // reset the object so another select query can be made
    function newQuery()
    {
        $this->pdo = new PDO("mysql:host=$this->dbhost;dbname=$this->dbdatabase;charset=$this->dbcharset", $this->dbuser, $this->dbpassword, $this->dboptions);
        $this->row_count = 0;
        $this->query = '';
        $this->orderby = '';
        $this->limit = '';
        $this->join = '';
        $this->join_flag = '';
        $this->values_stack = [];
        $this->stmt = new PDOStatement();
    }



    /**
     * Mimics JavaScript's console.log() function
     *
     * @param $data ,        information to be displayed in browser console
     */
    static function console_log($data)
    {
        echo '<script>console.log(' . json_encode($data) . ')</script>';
    }

    static function console_error($data)
    {
        echo '<script>console.error(' . json_encode($data) . ')</script>';
    }



    /**
     * @return array,       an array of this object's properties, displayed using print_r()
     */
    function var_dump(): array
    {
        return get_object_vars($this);
    }

    /**
     * @return array,       an array of the table columns
     */
    function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Return the current query string
     *
     * @return string,      the current query string
     */
    function currentQuery(): string
    {
        return $this->query;
    }

    /**
     * @return string,      the current table
     */
    function getTable(): string
    {
        return $this->db_table;
    }

    function getResult(): array
    {
        return $this->fetch_result;
    }

    /**
     * Add placeholder rows e.g (?, ?, ?) when using insertBatch()
     *
     * @param int $num_rows
     * @return string
     */
    function appendPlaceholders(int $num_rows)
    {
        $placeholder_row = "";

        for ($i = 0; $i < $num_rows; $i++) {
            if ($i === 0) {
                $placeholder_row .= $this->placeholder;
            }
            else {
                $placeholder_row .= " ,$this->placeholder";
            }
        }

        return $placeholder_row;
    }
}



/*
class ForeignKey extends OrionRecord {
    protected $name;
    protected $value;
    protected $table;
    function __constructor($name, $value, $table) {
        $this->name = $name;
        $this->value = $value;
        $this->table = $table;
    }

    function __toString() {
        return $this->value;
    }

    function __get($name) {
        //$this->manualQuery("SELECT * from INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE table_name = $this->table and column_name = $name");
        echo "SELECT * from INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE table_name = $this->table and column_name = $name";
    }
}*/