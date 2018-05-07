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
use model\dao\CustomerDao;

use model\Rating;
use model\User;

class RatingController extends AbstractController
{

    const MIN_PRODUCT_ID = 1;
    const MIN_USER_ID = 1;
    const DEFAULT_RATING_VALUE = 0;


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


    public static function giveRating()
    {
        if (isset($_SESSION['user'])) {
            /* @var $user_in_session User */
            $user_in_session = $_SESSION['user'];
            // Get the user_id
            $user_id = $user_in_session->getUserId();
            try {
                if (isset($_GET['pId']) && isset($_GET["rate"])) {
                    $product_id = htmlentities($_GET['pId']);
                    $value = htmlentities($_GET["rate"]);
                    if ($product_id < self::MIN_PRODUCT_ID || !is_numeric($product_id) || $user_id < self::MIN_USER_ID || !is_numeric($user_id)) {
                        echo json_encode('Bad data was passed in controller - ' . var_dump($product_id) .
                            ' or ' . var_dump($user_id));
                    }
                    $daoUser = new UserDao();
                    $daoProduct = new ProductsDao();
                    $daoRating = new RatingDao();
                    if ($daoUser::userExistsId($user_id) && $daoProduct->productExistsId($product_id)) {

                        $rating = [];
                        $rating["user_id"] = $user_id;
                        $rating["product_id"] = $product_id;
                        $rating["rating_value"] = $value;

                        $rating = json_encode($rating);
                        $rating = new Rating($rating);
                        $daoRating->giveRating($rating);

                    }
                }
            } catch
            (\PDOException $e) {
                echo $e->getMessage();
            } catch (\RuntimeException $e) {
                echo $e->getMessage();
            }
        }
    }


    public static function changeRating()
    {
        if (isset($_SESSION['user'])) {
            /* @var $user_in_session User */
            $user_in_session = $_SESSION['user'];
            $user_id = $user_in_session->getUserId();
            try {
                if (isset($_GET['pId']) && isset($_GET["rate"])) {
                    $product_id = htmlentities($_GET['pId']);
                    $value = htmlentities($_GET["rate"]);
                    if ($product_id < self::MIN_PRODUCT_ID || !is_numeric($product_id) || $user_id < self::MIN_USER_ID || !is_numeric($user_id)) {
                        echo json_encode('Bad data was passed in controller - ' . var_dump($product_id) .
                            ' or ' . var_dump($user_id));
                    }
                    $daoUser = new UserDao();
                    $daoProduct = new ProductsDao();
                    $daoRating = new RatingDao();
                    if ($daoUser::userExistsId($user_id) && $daoProduct->productExistsId($product_id)) {

                        $rating = [];
                        $rating["user_id"] = $user_id;
                        $rating["product_id"] = $product_id;
                        $rating["rating_value"] = $value;

                        $rating = json_encode($rating);
                        $rating = new Rating($rating);
                        $daoRating->changeRating($rating);

                    }
                }
            } catch
            (\PDOException $e) {
                echo $e->getMessage();
            } catch (\RuntimeException $e) {
                echo $e->getMessage();
            }
        }
    }

    public static function getRatings($product_id)
    {
        try {
            $dao = new RatingDao();
            $daoProduct = new ProductsDao();
            $ratings = [];
            if ($daoProduct->productExistsId($product_id)) {
                $ratings = $dao->getRatings($product_id);
            }
            return $ratings;
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }


    public static function getRatingFromUser()
    {
        if (isset($_SESSION['user'])) {
            /* @var $user_in_session User */
            $user_in_session = $_SESSION['user'];
            try {
                if (isset($_GET["product_id"])) {
                    $product_id = $_GET["product_id"];

                    if (CustomerDao::userIsCustomer($user_in_session->getUserId())) {
                        $rating = RatingDao::getRatingOfUser($user_in_session->getUserId(), $product_id);
                        if ($rating === false) {
                            $rating = [];
                            $rating["product_id"] = $product_id;
                            $rating["user_id"] = $user_in_session->getUserId();
                            $rating["rating_value"] = self::DEFAULT_RATING_VALUE;
                            $rating = new Rating(json_encode($rating));
                        }
                        echo (json_encode($rating));

                    } else {
                        die('no ratings to show');
                    }
                }
            } catch (\PDOException $e) {
                echo $e->getMessage();
            } catch (\RuntimeException $e) {
                echo $e->getMessage();
            }
        }
    }

}
