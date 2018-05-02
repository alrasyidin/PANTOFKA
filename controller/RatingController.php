<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2.5.2018 г.
 * Time: 7:01
 */

namespace controller;

use model\dao\ProductsDao;
use model\dao\RatingDao;
use model\Product;

use model\dao\UserDao;
use model\Rating;
use model\User;

class RatingController extends AbstractController
{
    private static $instance;

    /**
     * RatingController constructor.
     */
    private function __construct()
    {

    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new RatingController();
        }
        return self::$instance;
    }


    public static function addRating()
    {

        if (isset($_GET['pId']) && isset($_GET['userId']) && isset($_GET["value"])) {
            $product_id = htmlentities($_GET['pId']);
            $user_id = htmlentities($_GET['userId']);
            $value = htmlentities($_GET["value"]);
            if ($product_id < 1 || !is_numeric($product_id) || $user_id < 1 || !is_numeric($user_id)) {
                echo json_encode('Bad data was passed in controller - ' . var_dump($product_id) .
                    ' or ' . var_dump($user_id));
            }
            $daoUser = new UserDao();
            $daoProduct = new ProductsDao();
            $daoRating = new RatingDao();
            if ($daoUser->userExistsId($user_id) && $daoProduct->productExistsId($product_id)) {

                $rating = [];
                $rating["user_id"] = $user_id;
                $rating["product_id"] = $product_id;
                $rating["rating_value"] = $value;

                $rating = json_encode($rating);
                $rating = new Rating($rating);
                $daoRating->addRating($rating);

            }
        }
    }

    public static function getRatings($product_id)
    {
        $dao = new RatingDao();
        $daoProduct = new ProductsDao();
        $ratings=[];
        if ($daoProduct->productExistsId($product_id)) {
            $ratings = $dao->getRatings($product_id);
        }
        return $ratings;
    }
}
