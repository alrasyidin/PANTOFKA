<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/14/2018
 * Time: 11:17 PM
 */

namespace controller;


use model\dao\FavoritesDao;
use model\dao\UserDao;
use model\User;

class FavoritesController extends AbstractController{

    private static $instance;

    /**
     * FavoritesController constructor.
     */
    private function __construct(){

    }

    public static function getInstance(){
        if(self::$instance === null){
            self::$instance = new FavoritesController();
        }
        return self::$instance;
    }


    public function addToFavorites(){

        if (isset($_GET['id'])){
            $product_id = htmlentities($_GET['id']);

            if ($product_id < 1 || !is_numeric($product_id)){
                json_encode('Bad data was passed to the controller - ' . var_dump($product_id));
            }

            if (empty($_SESSION['user'])){
                die('You must be logged in to do this ');
            }

            /* @var $user_in_session User*/
            $user_in_session = $_SESSION['user'];
            $user_id = $user_in_session->getUserId();
            try{
                if (FavoritesDao::productIsAlreadyInFavorites($product_id , $user_id)){
                    die('already added');
                }
                FavoritesDao::addToFavorites($product_id , $user_id);
                die('Success!!! Product was added to favs. Check your DB');
            }catch (\RuntimeException $e){
                die($e->getTraceAsString() . '\n' . $e->getMessage());
            }
        }
    }

    public function removeFromFavorites(){}

}