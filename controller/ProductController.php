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
use model\dao\RatingDao;
use model\Rating;

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
                $daoRating = new RatingDao();

                $allProducts = [];
                /* @var $product Product */
                foreach ($products as $product) {
                    $sizes = $daoSize->getSizesAndQuantities($product->getProductId());
                    $ratings=$daoRating->getRatings($product->getProductId());
                    $product->setSizes($sizes);
                    $product->setRatings($ratings);
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