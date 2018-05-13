<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/14/2018
 * Time: 6:04 PM
 */

namespace controller;


use model\dao\AdminDao;
use model\dao\ProductsDao;
use model\Product;
use model\dao\SizeDao;
use model\Size;
use model\User;

class AdminController extends AbstractController
{
    const MIN_SIZE_NUMBER_KIDS = 25;
    const MAX_SIZE_NUMBER_KIDS = 34;
    const MIN_SIZE_NUMBER_WOMEN = 35;
    const MAX_SIZE_NUMBER_WOMEN = 42;
    const MIN_SIZE_NUMBER_MEN = 40;
    const MAX_SIZE_NUMBER_MEN = 48;
    const CATEGORY_MEN = "men";
    const CATEGORY_WOMEN = "women";
    const CATEGORY_BOYS = "boys";
    const CATEGORY_GIRLS = "girls";

    const MIN_PROMO_PERCENTAGE = 0;
    const MAX_PROMO_PERCENTAGE = 99;

    const MIN_PRODUCT_NAME_LENGTH = 3;
    const MAX_PRODUCT_NAME_LENGTH = 20;

    const MAX_INFO_LENGTH = 150;

    const MIN_PRODUCT_PRICE = 0;
    const MAX_PRODUCT_PRICE = 9999;

    const MIN_QUANTITY = 0;


    private static $instance;

    /**
     * @return int
     */


    /**
     * AdminController constructor.
     */
    private function __construct()
    {
        if (isset($_SESSION['user'])) {
            /* @var $user_in_session User */
            $user_in_session = &$_SESSION['user'];
            if ($user_in_session->getisAdmin()) {

            } else {
                header("location: index.php?page=error");
                die("Only admin");
            }
        }

    }


    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new AdminController();
        }
        return self::$instance;
    }


    public static function addProduct()
    {
        if (isset($_POST["add_product"])) {

            $error = "";

            $product_name = htmlentities($_POST["product_name"]);
            $color = htmlentities($_POST["product_color"]);
            $material = htmlentities($_POST["material"]);
            $style = htmlentities($_POST["style"]);
            $category = htmlentities($_POST["category"]);
            $promo_percentage = htmlentities($_POST["promo_percentage"]);
            $picture_url = "view/assets/products_imgs/no_image.jpg";
            $product_price = htmlentities($_POST["product_price"]);
            $info = htmlentities($_POST["info"]);

            $sizes = [];
            $min_size = self:: MIN_SIZE_NUMBER_KIDS;
            $max_size = self::MAX_SIZE_NUMBER_MEN;
            if ($category === "girls" || $category === "boys") {
                $min_size = self::MIN_SIZE_NUMBER_KIDS;
                $max_size = self::MAX_SIZE_NUMBER_KIDS;
            } elseif ($category === "women") {
                $min_size = self::MIN_SIZE_NUMBER_WOMEN;
                $max_size = self::MAX_SIZE_NUMBER_WOMEN;
            } elseif ($category === "men") {
                $min_size = self::MIN_SIZE_NUMBER_MEN;
                $max_size = self::MAX_SIZE_NUMBER_MEN;
            }

            for ($i = $min_size; $i <= $max_size; $i++) {
                $size_num = $i;
                if (empty($_POST["$i"])) {
                    $quantity = 0;

                } else {
                    $quantity = htmlentities($_POST["$i"]);
                }
                if ($quantity < self::MIN_QUANTITY || $size_num < self:: MIN_SIZE_NUMBER_KIDS ||
                    $size_num > self::MAX_SIZE_NUMBER_MEN) {
                    $error .= "wrong sizes ot quantity";

                }
                $size = [];
                $size["size_number"] = $size_num;
                $size["size_quantity"] = $quantity;

                $sizes[] = $size;

            }

            //validate data
            if (empty($product_name) || empty($product_price) || empty($color) || empty($material) || empty($style) ||
                empty($category)) {
                $error .= "Missing info";
            } elseif ($promo_percentage < self::MIN_PROMO_PERCENTAGE || $promo_percentage > self::MAX_PROMO_PERCENTAGE
                || $product_price < self::MIN_PRODUCT_PRICE || $product_price > self::MAX_PRODUCT_PRICE
                || strlen($product_name) < self::MIN_PRODUCT_NAME_LENGTH || strlen($product_name) > self::MAX_PRODUCT_NAME_LENGTH
                || strlen($info) > self::MAX_INFO_LENGTH || strstr($product_name, "  ")) {
                $error .= "Invalid input data";

            }
            if ($error === "") {

                $tmp_name = $_FILES["product_img_name"]["tmp_name"];
                $orig_name = $_FILES["product_img_name"]["name"];

                if (getimagesize($tmp_name)) {

                    if (is_uploaded_file($tmp_name)) {
                        $product_img_name = "$product_name-" . date("Ymdhisa") . ".png";
                        $picture_url = "view/assets/products_imgs/$product_img_name";
                        if (move_uploaded_file($tmp_name, $picture_url)) {

                        } else {
                            // error The picture not moved
                        }
                    } else {
                        // error The picture is not uploaded
                        $error .= "Picture not uploaded! ";
                    }
                } else {
                    $error .= "Trying to upload not image file!";
                }
            }


            if ($error === "") {
                try {
                    $dao = new ProductsDao();
                    $adminDao = new AdminDao();
//                            if (!($dao->productExists($product_name, $material, $category, $color))) {

                    $product = [];
                    $product["product_name"] = $product_name;
                    $product["promo_percentage"] = $promo_percentage;
                    $product["price"] = $product_price;
                    $product["product_image_url"] = $picture_url;
                    $product["info"] = $info;
                    $product["color"] = $color;
                    $product["material"] = $material;
                    $product["category"] = $category;
                    $product["style"] = $style;


                    $product = json_encode($product);
                    $new_product = new Product($product);
                    $sizes_and_numbers = [];
                    foreach ($sizes as $size) {
                        $new_size = new Size(json_encode($size));
                        $sizes_and_numbers[] = $new_size;
                    }
                    $new_product->setSizes($sizes_and_numbers);

                    $adminDao->saveNewProduct($new_product);
//Product is saved
                    header("location: index.php?page=add_product");
                    die();

//                            } else {
//                                //Product is not saved
//                                header("location: index.php?page=error");
//                                die();
//
//                            }
                } catch (\PDOException $e) {
                    header("location: index.php?page=error");
                    die();

                }
            } else {
                //Product is not saved
                header("location: index.php?page=error");
                die();

            }
        } else {
            //Product is not saved
            header("location: index.php?page=error");
            die();
        }
    }


    public static function changeProduct()
    {
        if (isset($_POST["change_product"])) {

            $error = "";
            $dao = new ProductsDao();
            $product_id = htmlentities($_POST["product_id"]);
            /* @var $product_to_edit Product */
            $product_to_edit = $dao->getProductById($product_id);
            $category = htmlentities($_POST["product_category"]);
            $style = htmlentities($_POST["product_style"]);
            $product_name = htmlentities($_POST["product_name"]);
            $color = htmlentities($_POST["product_color"]);
            $material = htmlentities($_POST["product_material"]);
            $promo_percentage = htmlentities($_POST["promo_percentage"]);
            $picture_url = $product_to_edit->getProductImageUrl();
            $product_price = htmlentities($_POST["product_price"]);
            $info = htmlentities($_POST["product_info"]);

            $sizes = [];
            $min_size = self:: MIN_SIZE_NUMBER_KIDS;
            $max_size = self::MAX_SIZE_NUMBER_MEN;
            if ($category === "girls" || $category === "boys") {
                $min_size = self::MIN_SIZE_NUMBER_KIDS;
                $max_size = self::MAX_SIZE_NUMBER_KIDS;
            } elseif ($category === "women") {
                $min_size = self::MIN_SIZE_NUMBER_WOMEN;
                $max_size = self::MAX_SIZE_NUMBER_WOMEN;
            } elseif ($category === "men") {
                $min_size = self::MIN_SIZE_NUMBER_MEN;
                $max_size = self::MAX_SIZE_NUMBER_MEN;
            }
            for ($i = $min_size; $i <= $max_size; $i++) {
                $size_num = $i;
                if (empty($_POST["$i"])) {
                    $quantity = 0;

                } else {
                    $quantity = htmlentities($_POST["$i"]);
                }
                if ($quantity < self::MIN_QUANTITY || $size_num < self:: MIN_SIZE_NUMBER_KIDS ||
                    $size_num > self::MAX_SIZE_NUMBER_MEN) {
                    $error .= "wrong sizes ot quantity";

                }
                $size = [];
                $size["size_number"] = $size_num;
                $size["size_quantity"] = $quantity;

                $sizes[] = $size;

            }

            //validate data
            if (empty($product_name) || empty($product_price) || empty($color) || empty($material) || empty($style) ||
                empty($category)) {
                $error .= "Missing info";
            } elseif ($promo_percentage < self::MIN_PROMO_PERCENTAGE || $promo_percentage > self::MAX_PROMO_PERCENTAGE
                || $product_price < self::MIN_PRODUCT_PRICE || $product_price > self::MAX_PRODUCT_PRICE
                || strlen($product_name) < self::MIN_PRODUCT_NAME_LENGTH || strlen($product_name) > self::MAX_PRODUCT_NAME_LENGTH
                || strlen($info) > self::MAX_INFO_LENGTH || strstr($product_name, "  ")) {
                $error .= "Invalid input data";
            }

            if ($error === "") {

                $tmp_name = $_FILES["product_image_url"]["tmp_name"];

                if (getimagesize($tmp_name)) {

                    if (is_uploaded_file($tmp_name)) {
                        $product_img_name = "$product_name-" . date("Ymdhisa") . ".png";
                        $picture_url = "view/assets/products_imgs/$product_img_name";
                        if (move_uploaded_file($tmp_name, $picture_url)) {

                        } else {
                            // error The picture not moved
                        }
                    } else {
                        // error The picture is not uploaded
                        $error .= "Picture not uploaded! ";
                    }
                }

            }


            if ($error === "") {
                try {
                    $adminDao = new AdminDao();

                    $product = [];
                    $product["product_id"] = $product_id;
                    $product["product_name"] = $product_name;
                    $product["promo_percentage"] = $promo_percentage;
                    $product["price"] = $product_price;
                    $product["product_image_url"] = $picture_url;
                    $product["info"] = $info;
                    $product["color"] = $color;
                    $product["material"] = $material;
                    $product["category"] = $category;
                    $product["style"] = $style;


                    $product = json_encode($product);
                    $new_product = new Product($product);
                    $sizes_and_numbers = [];
                    foreach ($sizes as $size) {
                        $size = json_encode($size);
                        $new_size = new Size($size);
                        $sizes_and_numbers[] = $new_size;
                    }
                    $new_product->setSizes($sizes_and_numbers);
                    $adminDao->changeProduct($new_product);
//Product is saved
                    $_SESSION["product_to_edit_id"] = $product_id;
                    $_SESSION["edit_product_result"] = "Product changed successfully!";
                    header("location: index.php?page=edit_product");
                    die();


                } catch (\PDOException $e) {
                    $_SESSION["product_to_edit_id"] = $product_id;
                    $_SESSION["edit_product_result"] = "Something went wrong! please try again!";
                    header("location: index.php?page=edit_product");
                    die();

                }


            } else {
                $_SESSION["product_to_edit_id"] = $product_id;
                $_SESSION["edit_product_result"] = $error;
                header("location: index.php?page=edit_product");
                die();

            }
        } else {
            //Product is not saved
            header("location: index.php?page=error");
            die();
        }
    }


    public
    function unsetProduct()
    {
        if (isset($_GET['id'])) {
            $product_id = htmlentities($_GET['id']);
            if ($product_id < 1 || !is_numeric($product_id)) {
                die('Bad data passed to the controller');
            }
            try {
                if (ProductsDao::productIsAvailable($product_id)) {
                    AdminDao::unsetProduct($product_id);
                    echo 'The product size quantities were set to zero';
                } else {
                    echo 'There is nothing else to remove from here';
                }
            } catch (\PDOException $e) {
                header("location: index.php?page=error");
                die;
            }

        } else {
            header("location: index.php?page=error");

            die('This is admin feature!');
        }

    }
}
