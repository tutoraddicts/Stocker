<?php
/*  
On the first execution it will creat a object of ControllerObjects where it will store object of each controller
*/

class Controllers {
    private $Controllers = array();

    public function __construct() {
        //logconsole("Creating the Object of : ControllerObjects");
    }

    public function __destruct(){
        //logconsole("Destroying the Object of : ControllerObjects");
    }
    
     // Magic method to set dynamic variables
     public function __set($name, $value) {
        
        if (!array_key_exists($name, $this->Controllers)){
            $this->Controllers[$name] = new $value();
            //logconsole("Registering Controller : $name");
        }else {
            //logconsole("Controller is already registered : $name");
        }
        
    }

    // Magic method to get dynamic variables
    public function __get($name) {
        if (array_key_exists($name, $this->Controllers)) {
            return $this->Controllers[$name];
        }else {
            //logconsole("Onject is not there $name");
            // echo "Onject is not there $name";
            exit();
        }
    }

    /* Pass name of the controlller and the object you can access them by $controllerObjects->Controllername */
    public function RegisterController($controllerName, $controllerObject){
        $this->Controllers[$controllerName] = $controllerObject;
    }
}

