<?php


use controller\AbstractController;
use model\dao\AbstractDao;
use model\AbstractModel;

spl_autoload_register(
        function ($class) {
            $class_name = str_replace("\\", "/", $class);

            if( strstr( $class_name , "model/dao/" ) !== false ){
                require_once   "./" . $class_name . ".php";

            }elseif (strstr( $class_name , "controller/" ) !== false ){
                require_once   "./" . $class_name . ".php";

            }elseif ( strstr( $class_name , "Controller" ) !== false ) {
                // do nothing

            }else {
                require_once   ".././" . $class_name . ".php";
            }
    } );

model\dao\AbstractDao::init();

ini_set('mbstring.internal_encoding','UTF-8');
header('Content-Type: text/html; charset=UTF-8');

session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <!-- Basic Page Needs
    –––––––––––––––––––––––––––––––––––––––––––––––––– -->
    <meta charset="utf-8">
    <title> PANTOFKA </title>
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Mobile Specific Metas
    –––––––––––––––––––––––––––––––––––––––––––––––––– -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- FONT
    –––––––––––––––––––––––––––––––––––––––––––––––––– -->
    <link href="//fonts.googleapis.com/css?family=Raleway:400,300,600" rel="stylesheet" type="text/css">

    <!-- CSS
    –––––––––––––––––––––––––––––––––––––––––––––––––– -->
    <link rel="stylesheet" href="view/assets/css/default.css">
    <link rel="stylesheet" href="view/assets/css/normalize.css">
    <link rel="stylesheet" href="view/assets/css/skeleton.css">

    <!-- JS
   –––––––––––––––––––––––––––––––––––––––––––––––––– -->
    <script src="./view/assets/js/userApp.js" type="text/javascript"></script>


    <!-- Favicon <link rel="icon" type="image/png" href="view/images/favicon.png">
    –––––––––––––––––––––––––––––––––––––––––––––––––– -->


</head>
<body>

<!-- Primary Page Layout
–––––––––––––––––––––––––––––––––––––––––––––––––– -->
<div class="container">
            <?php

            // TODO this must be one file 'navigation.html'.
            if(isset($_SESSION["user"])){
                require_once "./view/user_navigation.html";
            }else{
                require_once "./view/guest_navigation.html";
            }
            require_once "./view/header.html";

            // ==================================== Handle controller requests =========================================

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

                $class = AbstractController::createController($controller_name . 'Controller');

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
            // =========================================================================================================

            require_once "./view/footer.html";
            ?>
</div>

<!-- End Document–––––––––––––––––––––––––––––––––––––-->
</body>
</html>
