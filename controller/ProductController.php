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
    public static function getProductById()
    {
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            try {
                $daoSize = new SizeDao();
                $dao = new ProductsDao();
                $product = $dao->getProductById($_GET["id"]);
                $sizes = $daoSize->getSizesAndQuantities($product->getProductId());
                $product->setSizes($sizes);
                echo json_encode($product);

            } catch (\PDOException $e) {
                echo "error in Get product by ID - $e";
            }
        }
    }



    public static function getProducts()
    {


        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            try {
                $dao = new ProductsDao();
                $products = $dao->getProducts($_GET["pages"], $_GET["entries"], $_GET["category"]);
                $daoSize = new SizeDao();

                $allProducts = [];
                /* @var $product Product */
                foreach ($products as $product) {
                    $sizes = $daoSize->getSizesAndQuantities($product->getProductId());
                    $product->setSizes($sizes);
                    $allProducts[] = $product;
                }


                echo json_encode($products);
            } catch (\PDOException $e) {
                echo "error in Get products";
            }
        }

    }


    public static function getSizesByParentCategory(){
        $category=$_GET["pc"];

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
            $sizes[] = $i;

        }

        echo json_encode($sizes);

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

                   $result = $dao->saveNewProduct($new_product);
                   if ($result ===1) {
//Product is saved

                       header("location: index.php?page=show_products");
                   }
                   else{
                       //Product is not saved
                       header("location: index.php?page=add_product");
                   }
                } catch (\PDOException $e) {
                    var_dump($e);
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

    public function getStylesByParentCategory()
    {
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            if ($_GET["pc"] === "none") {
                echo json_encode(array());
            }
            else {
                try {
                    $dao = new ProductsDao();
                    $styles = $dao->getStylesByParentCategory($_GET["pc"]);
                    echo json_encode($styles);
                } catch (\PDOException $e) {
                    echo "error in getStylesByParentCategory";
                }
            }
        }

    }

    public static function getCategories()
    {
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            try {

                $dao = new ProductsDao();
                $categories = $dao->getCategories();
                echo json_encode($categories);
            } catch (\PDOException $e) {
                echo "error in Get categories";
            }
        }

    }

    public static function getColors()
    {
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            try {

                $dao = new ProductsDao();
                $colors = $dao->getColors();
                echo json_encode($colors);
            } catch (\PDOException $e) {
                echo "error in Get colors";
            }
        }

    }


    public static function getMaterials()
    {
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            try {

                $dao = new ProductsDao();
                $materials = $dao->getMaterials();
                echo json_encode($materials);
            } catch (\PDOException $e) {
                echo "error in Get materials";
            }
        }

    }

    public function getAllProducts()
    {
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            try {

                $dao = new ProductsDao();
                $products = $dao->getAllProducts();

                $daoSize = new SizeDao();

                $allProducts = [];
                /* @var $product Product */
                foreach ($products as $product) {
                    $sizes = $daoSize->getSizesAndQuantities($product->getProductId());
                    $product->setSizes($sizes);
                    $allProducts[] = $product;
                }

                echo json_encode($allProducts);
            } catch (\PDOException $e) {
                echo "error in getAllProducts";
            }
        }
    }

    public function show()
    {

        $category = htmlentities($_GET["tab"]);
        self::showCategory($category);

    }

    private static function showCategory($category)
    {
        try {
            $dao = new ProductsDao();
            $products = $dao->getAllProductsByCategory($category);

            $daoSize = new SizeDao();

            $allProducts = [];
            /* @var $product Product */
            foreach ($products as $product) {
                $sizes = $daoSize->getSizesAndQuantities($product->getProductId());
                $product->setSizes($sizes);
                $allProducts[] = $product;
            }
            echo json_encode($products);
        } catch (\PDOException $e) {
            echo "error in getAllProductsByCategory";
        }
    }
}