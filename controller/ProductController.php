<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/14/2018
 * Time: 11:18 PM
 */

namespace controller;

use model\dao\ProductsDao;
use model\dao\UserDao;
use model\Product;
use model\dao\SizeDao;
use model\Size;
use model\dao\RatingDao;
use model\Rating;
use model\User;

class ProductController extends AbstractController
{
    const MIN_SIZE_NUMBER_KIDS = 25;
    const MAX_SIZE_NUMBER_KIDS = 34;
    const MIN_SIZE_NUMBER_WOMEN = 35;
    const MAX_SIZE_NUMBER_WOMEN = 42;
    const MIN_SIZE_NUMBER_MEN = 40;
    const MAX_SIZE_NUMBER_MEN = 48;
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
                $daoRating = new RatingDao();
                if (isset($_SESSION["user"])){
                    /* @var $user User*/
                    $user = $_SESSION["user"];
                    $user_is_admin = $user->getisAdmin();
                }
                else{
                    $user_is_admin = false;
                }

                $product = $dao->getProductById($_GET["id"]);
                $sizes = $daoSize->getSizesAndQuantities($product->getProductId());
                $ratings=$daoRating->getRatings($product->getProductId());
                $product->setSizes($sizes);
                $product->setRatings($ratings);
                $product ->setShowToAdmin($user_is_admin);

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

                // check if user is admin to be added another admin buttons to the view
                if (isset($_SESSION["user"])){
                    /* @var $user User*/
                    $user = $_SESSION["user"];
                    $user_is_admin = $user->getisAdmin();
                }
                else{
                    $user_is_admin = false;
                }
                $allProducts = [];
                /* @var $product Product */
                foreach ($products as $product) {
                    $sizes = $daoSize->getSizesAndQuantities($product->getProductId());
                    $ratings=$daoRating->getRatings($product->getProductId());
                    $product->setSizes($sizes);
                    $product->setRatings($ratings);
                    $product ->setShowToAdmin($user_is_admin);
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

}