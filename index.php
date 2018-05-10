<?php


use controller\AbstractController;
use model\dao\AbstractDao;
use model\AbstractModel;

spl_autoload_register(
    function ($class) {
        $class_name = str_replace("\\", "/", $class);

        if (strstr($class_name, "model/dao/") !== false) {
            require_once "./" . $class_name . ".php";

        } elseif (strstr($class_name, "controller/") !== false) {
            require_once "./" . $class_name . ".php";

        } elseif (strstr($class_name, "Controller") !== false) {
            // do nothing

        } else {
            require_once $class_name . ".php";
        }
    });

ini_set('mbstring.internal_encoding', 'UTF-8');
header('Content-Type: text/html; charset=UTF-8');

const UNAUTHORIZED_PAGE_NAME = 'unauthorized';
const NOT_FOUND_PAGE_NAME = 'not_found';
const FAILED_LOGIN_PAGE_NAME = 'failed_login';
const LOGIN_PAGE_NAME = 'login';
const ERROR_PAGE_NAME = 'error';
const REGISTER_PAGE_NAME = 'register';
const CART_PAGE_NAME = 'cart';
const FAVORITES_PAGE_NAME = 'favorites';
const MAIN_PAGE_NAME = 'main';
const ADD_PRODUCT_PAGE_NAME = 'add_product';
const EDIT_PRODUCT_PAGE_NAME = 'edit_product';
const EMAIL_EXISTS_PAGE_NAME = 'email_exists';

session_start();

if (isset($_SESSION['user'])) {
    /* @var $user_in_session \model\User */
    $user_in_session = &$_SESSION['user'];
}
if (isset($_SESSION['cart'])) {
    /* @var $user_in_session \model\User */
    $cart = &$_SESSION['cart'];
}

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
    <link rel="stylesheet" href="view/assets/css/custom.css">
    <link rel="stylesheet" href="view/assets/css/navBar.css">


    <!-- JS
   –––––––––––––––––––––––––––––––––––––––––––––––––– -->
    <script src="./view/assets/js/favoritesAndCartFunctions.js" type="text/javascript"></script>
    <script src="./view/assets/js/product.js" type="text/javascript"></script>
    <script src="./view/assets/js/showProducts.js" type="text/javascript"></script>
    <script src="./view/assets/js/Pagination.js" type="text/javascript"></script>
    <script src="./view/assets/js/getProductCharacteristics.js" type="text/javascript"></script>
    <script src="./view/assets/js/register.js" type="text/javascript"></script>
    <script src="./view/assets/js/login.js" type="text/javascript"></script>
    <script src="./view/assets/js/validation.js" type="text/javascript"></script>
    <script src="./view/assets/js/editProduct.js" type="text/javascript"></script>
    <script src="./view/assets/js/history.js" type="text/javascript"></script>
    <script src="./view/assets/js/editProfile.js" type="text/javascript"></script>
    <script src="./view/assets/js/favoritesAndCartFunctions.js" type="text/javascript"></script>
    <script src="./view/assets/js/filter.js" type="text/javascript"></script>



    <!-- Favicon <link rel="icon" type="image/png" href="view/images/favicon.png">
    –––––––––––––––––––––––––––––––––––––––––––––––––– -->


</head>
<body>

<!-- Primary Page Layout
–––––––––––––––––––––––––––––––––––––––––––––––––– -->
<div class="container">
    <?php
    $page = "";
    if (isset($_GET["page"])) {
        $page = $_GET["page"];
    }

    if (isset($user_in_session)) {
        if ($user_in_session->getisAdmin()) {
            require_once "./view/admin_navigation.html";

        } else {
            require_once "./view/user_navigation.html";
        }
    } else {
        require_once "./view/guest_navigation.html";
    }
    require_once "./view/header.html";


    // ==================================== Front controller =========================================
    ?>
    <div id="main" style="display:block">

        <?php

        if (isset($_GET['page'])) {
            $page_name = $_GET['page'];

            if ($page_name === ADD_PRODUCT_PAGE_NAME || $page_name === EDIT_PRODUCT_PAGE_NAME) {
                if (isset($_SESSION['user'])) {
                    if ($user_in_session->getisAdmin() != 1) {
                        $page_name = UNAUTHORIZED_PAGE_NAME;
                    }
                }
                else {
                    $page_name = UNAUTHORIZED_PAGE_NAME;
                }
            }

            if ($page_name === LOGIN_PAGE_NAME || $page_name === REGISTER_PAGE_NAME) {
                if (isset($_SESSION['user'])) {
                    $page_name = UNAUTHORIZED_PAGE_NAME;
                }
            }

            if ($page_name !== LOGIN_PAGE_NAME && $page_name !== REGISTER_PAGE_NAME &&
                $page_name !== CART_PAGE_NAME && $page_name !== FAILED_LOGIN_PAGE_NAME &&
                $page_name !== MAIN_PAGE_NAME && $page_name !== EDIT_PRODUCT_PAGE_NAME &&
                $page_name !== EMAIL_EXISTS_PAGE_NAME && $page_name !== ADD_PRODUCT_PAGE_NAME
                && $page_name!== ERROR_PAGE_NAME && $page_name !== null) {
                if (!isset($user_in_session)) {
                    $page_name = UNAUTHORIZED_PAGE_NAME;

                }
            }


        } else {
            $page_name = MAIN_PAGE_NAME;
        }
        $page_path = __DIR__ . "\\view\\" . $page_name . ".html";
        if ($page_name === EDIT_PRODUCT_PAGE_NAME) {
            $page_path = __DIR__ . "\\view\\" . $page_name . ".php";
        }

        if (file_exists($page_path)) {
            require_once $page_path;
        } else {
            require_once __DIR__ . "\\view\\" . NOT_FOUND_PAGE_NAME . ".html";
        }


        // =========================================================================================================
        ?>
    </div>
    <?php

    require_once "./view/display_products.html";
    require_once "./view/footer.html";
    ?>
</div>

<!-- End Document–––––––––––––––––––––––––––––––––––––-->
<script src="./view/assets/js/validation.js" type="text/javascript"></script>
<script src="./view/assets/js/productsNavigation.js" type="text/javascript"></script>
<script src="./view/assets/js/editProfile.js" type="text/javascript"></script>
<script src="./view/assets/js/favoritesAndCartFunctions.js" type="text/javascript"></script>

</body>

</html>
