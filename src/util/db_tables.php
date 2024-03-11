<?php

// Abstruct class to hold DB data and perform some basic operation like set value and get value
abstract class DBTables implements ArrayAccess
{
    public $table_datas = array();

    public function __set($name, $value)
    {
        $this->table_datas[$name] = $value;
    }

    public function &__get($name)
    {
        $return_val = false;
        if (isset($this->table_datas[$name])) {
            return $this->table_datas[$name];
        }
        return $return_val;
    }

    public function offsetExists($offset)
    {
        return isset($this->table_datas[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }

    public function offsetSet($offset, $value)
    {
        if ($offset === null) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    public function &Get_All_Data()
    {
        return $this->table_datas;
    }
    /**
     * @param array $table_content - Array of table content
    */
    public function Insert(array $table_content )
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

        if ($DB->RunQuery($query)){
            logconsole("Successfully Inserted Our Data into the table : " . static::class);
            return true;
        }
        else {
            logconsole("Failed to Insert Our Data into the table : " . static::class);
            return false;
        }
    }

}