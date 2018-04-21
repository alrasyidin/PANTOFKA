<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/14/2018
 * Time: 11:18 PM
 */

namespace controller;

use model\dao\ProductsDao;
use model\Product;
use model\dao\RatingDao;
use model\Rating;
use model\dao\SizeDao;
use model\Size;

class ProductController extends AbstractController
{

    private static $instance;

    /**
     * ProductController constructor.
     */
    private function __construct()
    {

    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new ProductController();
        }
        return self::$instance;
    }

    public function saveNewProduct()
    {
        if (isset($_POST["add_product"])) {
            $error = "";

            $product_name = htmlentities($_POST["product_name"]);
            $color = htmlentities($_POST["product_color"]);
            $material = htmlentities($_POST["material"]);
            $style = htmlentities($_POST["style"]);
            $category = htmlentities($_POST["category"]);
            $category_parent = htmlentities($_POST["category_parent"]);
            $sale_percentage = htmlentities($_POST["sale_percentage"]);
            $picture_url = "view/assets/products_imgs/no_image.jpg";
            $product_price = htmlentities($_POST["product_price"]);
            $info = htmlentities($_POST["info"]);

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
            } elseif ($sale_percentage < 0 || $sale_percentage > 99 || $product_price < 0 || $product_price > 9999
                || strlen($product_name) < 3 || strlen($product_name) > 15 || strlen($info) > 150) {
                $error .= "Invalid input data";

            }

            if ($error === "") {
                try {
                    $dao = new ProductsDao();

                    $product = [];
                    $product["product_name"] = $product_name;
                    $product["sale_percentage"] = $sale_percentage;
                    $product["price"] = $product_price;
                    $product["product_img_url"] = $picture_url;
                    $product["info"] = $info;
                    $product["color"] = $color;
                    $product["material"] = $material;
                    $product["category"] = $category;
                    $product["category_parent"] = $category_parent;


                    $product = json_encode($product);
                    $new_product = new Product($product);
                    $dao->saveNewProduct($new_product);

                } catch (\Exception $e) {
                    throw $e;
                }

            }
        }
    }
}