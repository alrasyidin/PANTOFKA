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
            require_once   $class_name . ".php";
        }
    } );

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

    <!-- JS
   –––––––––––––––––––––––––––––––––––––––––––––––––– -->
    <script src="./view/assets/js/app.js" type="text/javascript"></script>

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
        <link rel="stylesheet" href="view/assets/css/custom.css">
        <link rel="stylesheet" href="view/assets/css/navBar.css">



        <!-- JS
       –––––––––––––––––––––––––––––––––––––––––––––––––– -->
        <script src= "./view/assets/js/favoritesAndCartFunctions.js" type="text/javascript"></script>
        <script src= "./view/assets/js/product.js" type="text/javascript"></script>
        <script src="./view/assets/js/showProducts.js" type="text/javascript"></script>
        <script src="./view/assets/js/Pagination.js" type="text/javascript"></script>
        <script src="./view/assets/js/getProductCharacteristics.js" type="text/javascript"></script>
        <script src= "./view/assets/js/register.js" type="text/javascript"></script>
        <script src= "./view/assets/js/login.js" type="text/javascript"></script>
        <script src= "./view/assets/js/validation.js" type="text/javascript"></script>
        <script src= "./view/assets/js/admin.js" type="text/javascript"></script>
        <script src= "./view/assets/js/editProduct.js" type="text/javascript"></script>
        <script src= "./view/assets/js/history.js" type="text/javascript"></script>




        <!-- Favicon <link rel="icon" type="image/png" href="view/images/favicon.png">
        –––––––––––––––––––––––––––––––––––––––––––––––––– -->


    </head>
    <body >

    <!-- Primary Page Layout
    –––––––––––––––––––––––––––––––––––––––––––––––––– -->
    <div class="container" >
                <?php

                // TODO this must be one file 'navigation.html'.
                if(isset($_SESSION["user"])){
                    require_once "./view/user_navigation.html";
                }else{
                    require_once "./view/guest_navigation.html";
                }
                require_once "./view/header.html";

                // ==================================== Handle controller requests =========================================

                require_once "./handle_requests.php";

                // =========================================================================================================
                 ?>

        <?php
                require_once "./view/footer.html";
                ?>
    </div>

    <!-- End Document–––––––––––––––––––––––––––––––––––––-->
    <script src="./view/assets/js/editProfile.js" type="text/javascript"></script>
    <script src= "./view/assets/js/favoritesAndCartFunctions.js" type="text/javascript"></script>

    </body>

</html>
