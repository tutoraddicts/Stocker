<?php

// Abstruct class to hold DB data and perform some basic operation like set value and get value
abstract class DBTables
{
    public $table_datas = array();

    public function __set($name, $value)
    {
        $this->table_datas[$name] = $value;
    }

    public function &__get($name)
    {
        $return_val = false;
        if (isset ($this->table_datas[$name])) {
            return $this->table_datas[$name];
        }
        return $return_val;
    }

    public function &Get_All_Data()
    {
        return $this->table_datas;
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
        }elseif (gettype($value) != "array") {
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
            }
            else {
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
    public function Insert(array $table_content)
    {
        global $DB;

        $query = "INSERT INTO " . static::class;
        $attributes = "( ";
        $values = "( ";
        $count = count($table_content);
        $i = 0;
        foreach ($table_content as $table_attribute => $attribute_value) {
            $attributes .= $table_attribute;
            $values .= "'" . $attribute_value . "'";

            // Add comma if it's not the last iteration
            if (++$i !== $count) {
                $attributes .= ", ";
                $values .= ", ";
            }
        }
        $attributes = $attributes . " )";
        $values = $values . " )";

        $query .= " " . $attributes . " VALUES " . $values;

        if ($DB->RunQuery($query)) {
            logconsole("Successfully Inserted Our Data into the table : " . static::class);
            return true;
        } else {
            logconsole("Failed to Insert Our Data into the table : " . static::class);
            return false;
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
     *          "24" = ">" // only if the user is greater than 24
     *      )
     * )
     * @return bool - false if query does not run porperly and result if ran successfully
     */
    public function Get(array $table_content)
    {
        global $DB;

        // SELECT * FROM users WHERE id = 1;
        $tableName = static::class;
        $query = "SELECT * FROM $tableName WHERE ";

        $count = count($table_content);
        $i = 0;

        foreach ($table_content as $attribute => $value) {

            $query .= " ( " . $this->CreateQuery($attribute, $value) . " ) "; 
            // foreach ($value as $sub_value => $operator) {
            //     // checks if the sub_value type is string if string we will pass the value inside ""
            //     if (gettype($sub_value) == "string") {
            //         $query .= "$attribute $operator \"$sub_value\" ";
            //     } else {
            //         $query .= "$attribute $operator $sub_value ";
            //     }
            //     //  to check if it is not the last attribute
            //     if (++$y !== $count_value) {
            //         $query .= " OR ";
            //     }
            // }
            // $query .= " ) ";

            // Add AND if it's not the last iteration
            if (++$i !== $count) {
                $query .= " AND ";
            }
        }
        $query .= ";";
        $result = $DB->RunQuery($query);
        if ($result) {
            logconsole("Successfully Inserted Our Data into the table : " . static::class);
            return $result;
        } else {
            logconsole("Failed to Insert Our Data into the table : " . static::class);
            return false;
        }
    }
}