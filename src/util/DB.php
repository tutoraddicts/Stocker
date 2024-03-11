<?php
class DB
{
    public $dbConnection = null;

    /* 
    This will hold all the Table attributes details
    in a array format
    "attributename" => "charectarestics according to SQL"
    */
    public $Tables = array(); // all the objects of the table
    public $table_datas = array(); // storing the data of the table

    // When you want to create a new array
    public function __construct()
    {
        $this->connect_to_DB();
    }

    public function __destruct()
    {
        //logconsole("Destoying DB Object");
    }

    /**
     * Used to connect to our DB server
     * @return - return true if the connection is established and false if not
     */
    private function connect_to_DB(): bool
    {
        global $config;
        // Create connection
        $this->dbConnection = new mysqli($config->database->host, $config->database->userName, $config->database->password);

        // Check connection
        if ($this->dbConnection->connect_error) {
            //logconsole("Connection failed: " . $this->dbConnection->connect_error);
            return false;
        } else {
            //logconsole("Connected successfully");
            $this->dbConnection->select_db($config->database->db);
            return true;
        }
    }

    public function check_db_connection()
    {
        if ($this->dbConnection === null) {
            logconsole("Databse Connection not established : Retring");
            if ($this->connect_to_DB()) {
                logconsole("Re-Established SQL Connection");
                return true; // not have to create a new Table
            } else {
                logconsole("Final Fail for Database Connection can not create or update any table");
                return false;
            }
        } else if ($this->dbConnection) {
            logconsole("Getting this Error while Connecting to DB " . $this->dbConnection->connect_errno . " : Retring");
            if ($this->connect_to_DB()) {
                logconsole("Re-Established SQL Connection");
                return true; // not have to create a new Table
            } else {
                logconsole("Final Fail for Database Connection can not create or update any table");
                return false;
            }
        } else {
            logconsole("Success Fully Get the Connection");
            return true;
        }
    }
    /**
     * Fetch Data of all the tables and update them accordingly
     * @param string $_table_name Pass Table name if you want to update specific table
     */
    public function Update_DB($_table_name = null)
    {

        if (!$this->check_db_connection()) {
            logconsole("Databse Connection not established While Updating the DB");
            return;
        } else {
            logconsole("Creating DB Connection");
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
                    // array ("user_name" = "array(user_name_datas)")
                    // $this->table_datas[$table_name][$row[array_key_first($colums)]] = $row;
                    $table_object->{$row[array_key_first($colums)]} = $row;
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

                        // $this->table_datas[$table_name][$row[array_key_first($colums)]] = $row;
                        $table_object->{$row[array_key_first($colums)]} = $row;
                    }
                }
            }
        }

    }

    /**
     * @param  string $table_name - name of the table you want the data
     * @param bool $update_table - send true if you want to update the data of the table from the database before fetching the data false is default
     */
    public function &get_table_datas(string $table_name, bool $update_table = false)
    {
        /**
         * @param  string $table_name - name of the table you want the data
         * @param bool $update_table - send true if you want to update the data of the table from the database before fetching the data false is default
         */
        if ($update_table) {
            $this->Update_DB($table_name);
        }

        return $this->Tables[$table_name]->Get_All_Data();
    }


    /**
     * Storing all the data from DataBase it will only happen once in server run or if we call it explicitly
     * @param string $table_name - name of the Data Base Table
     * @param stdclass $value - Object of the database Table Class
     */
    public function __set($table_name, $value)
    {
        //make our sqlQuery to get all the data
        // Storing the object of the Database Tables with there respective names
        //logconsole("Creating DB Object for $table_name");
        $this->Tables[$table_name] = $value;

        $this->Update_DB($table_name);

    }

    public function &__get($table_name)
    {
        return $this->Tables[$table_name];
    }

    /**
     * @param string $query - query to run
     * @return bool - returns true if the query ran successfully and false if not
     * */
    public function RunQuery (string $query ) {
        if ($query == null) {
            return false;
        }

        if (!$this->check_db_connection()){
            return false;
        }

        logconsole("Running this query : $query");
        $stmt = $this->dbConnection->prepare($query);
        if ( !$stmt ){
            return false;
        }

        if (!$stmt->execute()){
            return false;
        }
        return true;
    }
}

