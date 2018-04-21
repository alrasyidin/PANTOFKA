<?php

// Command pattern

$file_not_found = false;

$controller_name = isset($_GET['target']) ? htmlentities($_GET['target']) : 'base';
$method_name = isset($_GET['action']) ? htmlentities($_GET['action']) : 'index';
$page_name = isset($_GET['page']) ? htmlentities($_GET['page']) : null;

$controller_class_name = "controller\\" . ucfirst($controller_name) . "Controller";
$page_path = './view/' . $page_name . ".html" ;

if (class_exists($controller_class_name)) {
    if(!empty($page_name)){
        if(file_exists($page_path)){
            require_once $page_path;
        }else{
            require_once "./view/main.html";
        }
    }

    $class = controller\AbstractController::createController($controller_name . 'Controller');

    if (method_exists($controller_class_name, $method_name)) {
        //if request is not for login or register, check for login
        if($controller_name == "user" && $method_name == "login"){
            if(isset($_SESSION["username"])){
                header("HTTP/1.1 401 Unauthorized");
                die();
            }
        }
        try{
            $class::$method_name();
        }
        catch(\PDOException $e){
            header("HTTP/1.1 500");
            echo $e->getMessage();
            die();
        }
    } else {
        $file_not_found = true;
    }
} else {
    $file_not_found = true;
}

if ($file_not_found) {
    //return header 404
    echo 'target or action invalid: target = ' . $controller_name . ' and action = ' .$method_name;
}