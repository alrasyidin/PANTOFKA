<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/15/2018
 * Time: 11:50 AM
 */

namespace controller;


class CartController extends AbstractController{

    private static $instance;

    /**
     * CartController constructor.
     */
    private function __construct(){

    }

    public static function getInstance(){
        if(self::$instance === null){
            self::$instance = new CartController();
        }
        return self::$instance;
    }

}