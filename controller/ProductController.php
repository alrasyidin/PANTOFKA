<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/14/2018
 * Time: 11:18 PM
 */

namespace controller;


class ProductController extends AbstractController{

    private static $instance;

    /**
     * ProductController constructor.
     */
    private function __construct(){

    }

    public static function getInstance(){
        if(self::$instance === null){
            self::$instance = new ProductController();
        }
        return self::$instance;
    }
}