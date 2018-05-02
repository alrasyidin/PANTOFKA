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

    private static $instance;

    /**
     * AdminController constructor.
     */
    private function __construct()
    {

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
            if (isset($_SESSION['user'])) {
                /* @var $user_in_session User */
                $user_in_session = $_SESSION['user'];
                if ($user_in_session->getisAdmin()) {
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
                    $min_size = 0;
                    $max_size = 0;
                    if ($category === "girls" || $category === "boys") {
                        $min_size = 25;
                        $max_size = 34;
                    } elseif ($category === "women") {
                        $min_size = 35;
                        $max_size = 42;
                    } elseif ($category === "men") {
                        $min_size = 40;
                        $max_size = 48;
                    }
                    for ($i = $min_size; $i <= $max_size; $i++) {
                        $quantity = htmlentities($_POST["$i"]);
                        $size_num = $i;
                        if ($quantity < 0 || $size_num < 25 || $size_num > 48) {
                            $error .= "wrong sizes ot quantity";

                        }
                        $size = [];
                        $size["size_number"] = $i;
                        $size["size_quantity"] = $quantity;

                        $sizes[] = $size;

                    }

                    $tmp_name = $_FILES["product_img_name"]["tmp_name"];
                    $orig_name = $_FILES["product_img_name"]["name"];

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


                    //validate data
                    if (empty($product_name) || empty($product_price) || empty($color) || empty($material) || empty($style) ||
                        empty($category)) {
                        $error .= "Missing info";
                    } elseif ($promo_percentage < 0 || $promo_percentage > 99 || $product_price < 0 || $product_price > 9999
                        || strlen($product_name) < 3 || strlen($product_name) > 15 || strlen($info) > 150) {
                        $error .= "Invalid input data";

                    }

                    if ($error === "") {
                        try {
                            $dao = new ProductsDao();
                            if (!($dao->productExists($product_name, $material, $category, $color))) {

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

                                $dao->saveNewProduct($new_product);

//Product is saved
                                header("location: index.php?page=show_products");
                            } else {
                                //Product is not saved
                                header("location: index.php?page=add_product");
                            }
                        } catch (\PDOException $e) {
                            var_dump($e);
                        }
                    }
                }
            }
        }

    }


    public static function editProduct()
    {
        if (isset($_POST["edit_product"])) {
            if (isset($_SESSION['user'])) {
                /* @var $user_in_session User */
                $user_in_session = $_SESSION['user'];
                if ($user_in_session->getisAdmin()) {
                    $error = "";
                    $dao = new ProductsDao();
                    $product_id = htmlentities($_POST["product_id"]);
                    /* @var $product_to_edit Product */
                    $product_to_edit = $dao->getProductById($product_id);

                    $category = $product_to_edit->getCategory();
                    $style = $product_to_edit->getStyle();
                    $product_name = htmlentities($_POST["product_name"]);
                    $color = htmlentities($_POST["color"]);
                    $material = htmlentities($_POST["material"]);
                    $promo_percentage = htmlentities($_POST["promo_percentage"]);
                    $picture_url = $product_to_edit->getProductImgUrl();
                    $product_price = htmlentities($_POST["product_price"]);
                    $info = htmlentities($_POST["info"]);

                    $sizes = [];
                    $min_size = 0;
                    $max_size = 0;
                    if ($category === "girls" || $category === "boys") {
                        $min_size = 25;
                        $max_size = 34;
                    } elseif ($category === "women") {
                        $min_size = 35;
                        $max_size = 42;
                    } elseif ($category === "men") {
                        $min_size = 40;
                        $max_size = 48;
                    }
                    for ($i = $min_size; $i <= $max_size; $i++) {
                        $quantity = htmlentities($_POST["$i"]);
                        $size_num = $i;
                        if ($quantity < 0 || $size_num < 25 || $size_num > 48) {
                            $error .= "wrong sizes ot quantity";

                        }
                        $size = [];
                        $size["size_number"] = $i;
                        $size["size_quantity"] = $quantity;

                        $sizes[] = $size;

                    }

                    $tmp_name = $_FILES["product_img_name"]["tmp_name"];
                    $orig_name = $_FILES["product_img_name"]["name"];

                    if (is_uploaded_file($tmp_name)) {
                        $product_img_name = "$product_name-" . date("Ymdhisa") . ".png";
                        $picture_url = "view/assets/products_imgs/$product_img_name";
                        if (move_uploaded_file($tmp_name, $picture_url)) {

                        } else {
                            // error The picture not moved
                        }
                    } else {
                        // error The picture is not uploaded
                    }


                    //validate data
                    if (empty($product_name) || empty($product_price) || empty($color) || empty($material) || empty($style) ||
                        empty($category)) {
                        $error .= "Missing info";
                    } elseif ($promo_percentage < 0 || $promo_percentage > 99 || $product_price < 0 || $product_price > 9999
                        || strlen($product_name) < 3 || strlen($product_name) > 15 || strlen($info) > 150) {
                        $error .= "Invalid input data";

                    }

                    if ($error === "") {
                        try {
                            $dao = new ProductsDao();

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

                                $dao->changeProduct($new_product);

//Product is saved
                                header("location: index.php?page=show_products");

                        } catch (\PDOException $e) {
                            var_dump($e);
                        }
                    }
                }
            }
        }
    }


    public function unsetProduct()
    {
        if (isset($_SESSION['user'])) {
            /* @var $user_in_session User */
            $user_in_session = $_SESSION['user'];
            if ($user_in_session->getisAdmin()) {
                if (isset($_GET['id'])) {
                    $product_id = htmlentities($_GET['id']);
                    if ($product_id < 1 || !is_numeric($product_id)) {
                        die('Bad data passed to the controller');
                    }
                    try {
                        if (AdminDao::productIsAvailable($product_id)) {
                            AdminDao::unsetProduct($product_id);
                            echo 'The product size quantities were set to zero';
                        } else {
                            echo 'There is nothing else to remove from here';
                        }
                    } catch (\PDOException $e) {
                        die($e->getMessage());
                    }

                }
            } else {
                die('This is admin feature!');
            }
        } else {
            die('This is admin feature!');
        }
    }

}