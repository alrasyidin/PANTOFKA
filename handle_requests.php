<?php
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
            require_once   $class_name . ".php";
        }
    } );

if (!isset($_SESSION)){
    session_start();
}

model\dao\AbstractDao::init();
$file_not_found = false;

if ( isset($_GET['target']) && isset($_GET['action'])){
    $controller_name = htmlentities($_GET['target']) ;
    $method_name = htmlentities($_GET['action']) ;


$controller_class_name = "controller\\" . ucfirst($controller_name) . "Controller";
if (class_exists($controller_class_name)) {
    $class = controller\AbstractController::createController($controller_name . 'Controller');

    if (method_exists($controller_class_name, $method_name)) {

        try{
            $class->$method_name();

        }
        catch(\PDOException $e){
            //  header("HTTP/1.1 500");
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
header("location: index.php?page=error");
die();
//echo 'target or action invalid: target = ' . $controller_name . ' and action = ' .$method_name;
}
}else{
    header('location: index.php?page=main');
    die();

}

