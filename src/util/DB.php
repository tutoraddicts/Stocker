<?php
class DB
{
    private $dbConnection = null;

    /* 
    This will hold all the Table attributes details
    in a array format
    "attributename" => "charectarestics according to SQL"
    */
    public $Tables = array();
    public $table_datas = array(); // storing the data of the table

    // When you want to create a new array
    public function __construct()
    {
        $this->connect_to_DB();
    }

    public function __destruct()
    {
        logConsole("Destoying DB Object");
    }

    /* 
    Used to connect to our DB server
    @Return - return true if the connection is established and false if not
    */
    private function connect_to_DB(): bool
    {
        global $config;
        // Create connection
        $this->dbConnection = new mysqli($config->database->host, $config->database->userName, $config->database->password);

        // Check connection
        if ($this->dbConnection->connect_error) {
            logConsole("Connection failed: " . $this->dbConnection->connect_error);
            return false;
        } else {
            logConsole("Connected successfully");
            $this->dbConnection->select_db($config->database->db);
            return true;
        }
    }

    /**
     * Fetch Data of all the tables and update them accordingly
     */
    public function Update_DB(&$_table_name = null)
    {

        if (!$this->check_db_connection()) {
            logConsole("Databse Connection not established");
            return;
        }

        if ($_table_name != null) {
            // then only update for that perticular table
            $table_name = &$_table_name;
            $table_object = $this->Tables[$table_name];

            $colums = &$table_object->{$table_name};
            $sql_query = "SELECT * FROM $table_name";

            $result = $this->dbConnection->query($sql_query);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {

                    $this->table_datas[$table_name][$row[array_key_first($colums)]] = $row;
                }

            }
        } else {
            // do for all
            foreach ($this->Tables as $table_name => $table_object) {
                $colums = &$table_object->{$table_name};
                $sql_query = "SELECT * FROM $table_name";

                $result = $this->dbConnection->query($sql_query);
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {

                        $this->table_datas[$table_name][$row[array_key_first($colums)]] = $row;
                    }
                }
                // $this->table_datas[$table_name] = ;
            }
        }

    }

    /**
     * @param  string $table_name - name of the table you want the data
     * @param bool $update_table - send true if you want to update the data of the table from the database before fetching the data false is default
     */
    public function &get_table_datas(string &$table_name, bool $update_table = false)
    {
        /**
         * @param  string $table_name - name of the table you want the data
         * @param bool $update_table - send true if you want to update the data of the table from the database before fetching the data false is default
         */
        if ($update_table) {
            $this->Update_DB($table_name);
        }

        return $this->table_datas[$table_name];
    }

    public function check_db_connection()
    {
        if ($this->dbConnection === null || $this->dbConnection->connect_errno) {
            logConsole("Databse Connection not established : Retring");
            if ($this->connect_to_DB()) {
                logConsole("Re-Established SQL Connection");
                return true; // not have to create a new Table
            }else {
                logConsole("Final Fail for Database Connection can not create or update any table");
                return false;
            }
        }        
    }
    /**
     * Storing all the data from DataBase it will only happen once in server run or if we call it explicitly
     * @param string $table_name - name of the Data Base Table
     * @param stdclass $value - Object of the database Table Class
     */
    public function __set(string $table_name, $value)
    {
        if (!$this->check_db_connection()) {
            logConsole("Databse Connection not established");
            return;
        }

        //make our sqlQuery to get all the data
        // Storing the object of the Database Tables with there respective names
        logConsole("Creating DB Object for $table_name\n");
        $this->Tables[$table_name] = $value;

        $this->Update_DB($table_name);

    }

    public function &__get($table_name)
    {
        return $this->Tables[$table_name];
    }

}

