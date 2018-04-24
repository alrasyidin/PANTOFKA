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

    public static function getProductById()
    {
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            try {
                $dao = new ProductsDao();
                $product = $dao->getProductById($_GET["id"]);
                echo json_encode($product);
            } catch (\PDOException $e) {
                echo "error in Get product by ID";
            }
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new ProductController();
        }
        return self::$instance;
    }


    public static function getProducts()
    {


        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            try {
                $dao = new ProductsDao();
                $products = $dao->getProducts($_GET["pages"], $_GET["entries"], $_GET["category"]);
//                $daoSize = new SizeDao();
//
                $allProducts = [];
//                foreach ($products as $product) {
//                    $product_id = $product["product_id"];
//                    $sizes = $daoSize->getSizesAndQuantities($product_id);
//                    $product = json_encode($product);
//                    $product = new Product($product);
//                    $product->setSizes($sizes);
//                    $allProducts[] = $product;
//                }
//                /* @var $allProducts \JsonSerializable*/
//                $allProducts= $allProducts->jsonSerialize();
//                echo json_encode($allProducts);
                /* @var $products \JsonSerializable */
                $products->jsonSerialize();
                echo json_encode($products);
            } catch (\PDOException $e) {
                echo "error in Get products";
            }
        }

    }

    public static function getCategories()
    {
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            try {

                $dao = new ProductsDao();
                $categories = $dao->getCategories();
//            /* @var $categories \JsonSerializable*/
//            $categories= $categories->jsonSerialize();
                echo $categories;
            } catch (\PDOException $e) {
                echo "error in Get categories";
            }
        }

    }


    public static function saveNewProduct()
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
                $size = [];
                $size["size_number"] = $i;
                $size["size_quantity"] = htmlentities($_POST["$i"]);
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
                    $product_exists = $dao->getProductId($new_product);
                    if (!$product_exists) {

                        $dao->saveNewProduct($new_product);
                        $product_id = $dao->getProductId($new_product);

                        if ($dao->productIdExists($product_id)) {
                            $daoSize = new SizeDao();
                            foreach ($sizes as $size) {
                                $size = json_encode($size);
                                $new_size = new Size($size);
                                $daoSize->saveSize($product_id, $new_size);
                            }
                        }
                    } else {
                        $error .= "Product already exists";
                    }
                    if ($error !== "") {
                        return $error;
                    } else {
                       //"Product added successfully";
                    }


                } catch (\Exception $e) {
                    throw $e;
                }

            }
        }
    }

    public static function numberOfProducts()
    {
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            $dao = new ProductsDao();
            echo $dao->getProductsCount($_GET["category"]);
        }
    }
}