<?php

// Abstruct class to hold DB data and perform some basic operation like set value and get value
abstract class DB
{
    private $tableName = ""; // whill be holding the name of the object means the child table name
    private $table_structure_var = array();

    private $connection = null; // Hold the connection to the Database

    // When you want to create a new array
    public function __construct()
    {
        $this->tableName = get_class($this);
        $this->connect_to_DB();
        $this->table_structure_var = &$this->{$this->tableName};         // Empty the Variable Datas

        foreach ($this->table_structure_var as $index => $value) {
            $this->table_structure_var[$index] = "";
        }
    }

    public function __set($index, $value)
    {
        $this->table_structure_var[$index] = $value;
    }

    private function connect_to_DB(): bool
    {
        global $config;
        // Create connection
        $this->connection = new mysqli($config->database->host, $config->database->userName, $config->database->password);

        // Check connection
        if ($this->connection->connect_error) {
            if ($config->errorShow) {
                echo $this->connection->connect_error;
            }
            logconsole("Connection failed: " . $this->connection->connect_error);
            return false;
        } else {
            //logconsole("Connected successfully");
            $this->connection->select_db($config->database->db);
            return true;
        }
    }

    public function check_db_connection()
    {
        if ($this->connection === null) {
            logconsole("Databse Connection not established : Retring");
            if ($this->connect_to_DB()) {
                logconsole("Re-Established SQL Connection");
                return true; // not have to create a new Table
            } else {
                logconsole("Final Fail for Database Connection can not create or update any table");
                return false;
            }
            // } else if ($this->connection->) {
            //     logconsole("Getting this Error while Connecting to DB " . $this->connection->connect_errno . " : Retring");
            //     if ($this->connect_to_DB()) {
            //         logconsole("Re-Established SQL Connection");
            //         return true; // not have to create a new Table
            //     } else {
            //         logconsole("Final Fail for Database Connection can not create or update any table");
            //         return false;
            //     }
        } else {
            logconsole("Success Fully Get the Connection");
            return true;
        }
    }

    /**
     * @param array $table_content - Array of table content the attribute type = value which is needed
     * Example :  
     * array (
     *      "user_name" => array(
     *          "user1" => "=", // value and the needed operator example = for if equal < if lessthan
     *          "user2" => "="
     *      )
     *      "password" => array(
     *          "password1" => "="
     *      )
     *      "user_age" => array(
     *          
     *          "24" = ">" // only if the user is greater than 24
     *      )
     * )
     * @return bool - false if query does not run porperly and result if ran successfully
     */
    private function CreateQuery($attribute, $value): string
    {
        $query = "";
        if (gettype($value) == "string") {
            $query .= "$attribute = \"$value\" ";
            return $query;
        } elseif (gettype($value) != "array") {
            $query .= "$attribute = $value ";
            return $query;
        }

        $y = 0; //  to check if it is not the last attribute
        $count_value = count($value);

        foreach ($value as $sub_value => $operator) {
            if (gettype($operator) == "array") {
                /**
                 * "100" => array(
                 *      "OR/AND" => "<"
                 * )
                 * By default the logic will take the sub as OR if you want to specify other Conditon such as AND you can specify here like this
                 */
                foreach ($operator as $Condition => $_operator) {
                    $query .= " $Condition $attribute $_operator  \"$sub_value\" ";
                }
                ++$y;
                continue;
            }
            // checks if the sub_value type is string if string we will pass the value inside ""
            else if (gettype($sub_value) == "string") {
                $query .= "$attribute $operator \"$sub_value\" ";
            } else {
                $query .= "$attribute $operator $sub_value ";
            }

            // Add AND if it's not the last iteration
            if (++$y !== $count_value) {
                $query .= " OR ";
            }
        }

        return $query;
    }

    /**
     * @param string $query - query to run
     * @return mixed false on failure. For successful queries which produce a result set, such as SELECT, SHOW, DESCRIBE or EXPLAIN, mysqli_query() will return a mysqli_result object. For other successful queries, mysqli_query() will return true .
     */
    public function RunQuery(string $query)
    {
        if ($query == null) {
            return false;
        }

        if (!$this->check_db_connection()) {
            return false;
        }

        logconsole("Running this query: $query");
        $result = $this->connection->query($query);
        // logconsole($result);
        if ($result === false) {
            logconsole("Database query error: " . $this->connection->error);
            return false;
        }

        $data = $result->fetch_all(MYSQLI_ASSOC);
        return $data;
    }

    /**
     * @param array $table_content - Array of table content
     * example : array(
     *                   "user_name" => $username,
     *                  "password" => $password,
     *                    "email_id" => $email_id,
     *                   "first_name" => $first_name,
     *                   "last_name" => $last_name,
     *                   "secondary_email" => $secondary_email
     *               )
     * @return bool - return string if the query ran correctly
     */
    public function Insert($table_content = []): bool
    {
        if ($table_content === []) {
            $table_content = &$this->{$this->tableName};
        }

        $query = "INSERT INTO " . static::class;
        $attributes = "( ";
        $values = "( ";
        $count = count($table_content);
        $i = 0;
        foreach ($table_content as $table_attribute => $attribute_value) {
            $attributes .= $table_attribute;
            $values .= "'$attribute_value'";

            // Add comma if it's not the last iteration
            if (++$i !== $count) {
                $attributes .= ", ";
                $values .= ", ";
            }
        }
        $attributes = "$attributes )";
        $values = "$values )";

        $query .= " $attributes VALUES $values ";

        $this->connection->query($query);
        $result = $this->connection->query($query);
        logconsole($result);
        if ($result === false) {
            logconsole("Database query error: " . $this->connection->error);
            return false;
        }
        return true;
    }

    /**
     * @param array $table_content - Array of table content the attribute type = value which is needed
     * Example :  
     * array (
     *      "user_name" => array(
     *          "user1" => "=", // value and the needed operator example = for if equal < if lessthan
     *          "user2" => "="
     *      )
     *      "password" => array(
     *          "password1" => "="
     *      )
     *      "user_age" => array(
     *          "24" = ">" // only if the user is greater than 24
     *      )
     * )
     * @return bool - false if query does not run porperly and result if ran successfully
     */
    public function Get(array $table_content = [])
    {
        // if user does not send any table content we will fetch all the data and send it backww
        if ($table_content === []) {
            $query = "SELECT * FROM {$this->tableName}";
            $result = $this->RunQuery($query);
            if ($result) {
                logconsole("Successfully Fetched Our Data into the table : " . static::class);
                return $result;
            } else {
                logconsole("Failed to Fetch Our Data into the table : " . static::class);
                return false;
            }
        }

        // SELECT * FROM users WHERE id = 1;
        $tableName = static::class;
        $query = "SELECT * FROM $tableName WHERE ";

        $count = count($table_content);
        $i = 0;

        foreach ($table_content as $attribute => $value) {

            $query .= " ( {$this->CreateQuery($attribute, $value)} ) ";

            // Add AND if it's not the last iteration
            if (++$i !== $count) {
                $query .= " AND ";
            }
        }
        $query .= ";";
        $result = $this->RunQuery($query);
        if ($result) {
            logconsole("Successfully Fetched Our Data into the table : " . static::class);
            return $result;
        } else {
            logconsole("Failed to Fetch Our Data into the table : " . static::class);
            return false;
        }
    }
}