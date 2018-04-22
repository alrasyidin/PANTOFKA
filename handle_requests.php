<?php

// Command pattern

$file_not_found = false;

$controller_name = isset($_GET['target']) ? htmlentities($_GET['target']) : 'base';
$method_name = isset($_GET['action']) ? htmlentities($_GET['action']) : 'index';
$page_name = isset($_GET['page']) ? htmlentities($_GET['page']) : null;

if($page_name === 'login' || $page_name === 'register'){
    if(isset($_SESSION['user'])){
       // header('HTTP/1.1 401 Unauthorized');
        $page_name = 'error';
        echo 'Logged user is trying to register or log in';
        //die();
    }
}

if($page_name !== 'login' && $page_name !== 'register' && $page_name !== 'cart' && $page_name !== 'favorites' && $page_name !== 'main' && $page_name !== null){
    if(!isset($_SESSION['user'])){
        //header('HTTP/1.1 401 Unauthorized');
        //die();
        echo 'Guest user is trying to access private data in ' . $page_name;
        $page_name = 'error';

    }
}

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