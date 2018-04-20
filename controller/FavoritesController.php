<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/14/2018
 * Time: 11:17 PM
 */

namespace controller;


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


    public function addToFavorites(){}

    public function removeFromFavorites(){}

}