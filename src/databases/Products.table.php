<?php

/**
 * Create a class and add varible with same name of the class 
*/
class Products extends DB
{
    public $Products = array(
        "productName" => "VARCHAR(100)",
        "description" => "TEXT",
        "price" => "DECIMAL(10,2)",
        "stock" => "INT"
    );
}

?>