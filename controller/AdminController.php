<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/14/2018
 * Time: 6:04 PM
 */

namespace controller;


use model\Product;

class AdminController extends CustomerController {

    private static $instance;

    /**
     * AdminController constructor.
     */
    private function __construct(){

    }

    public static function getInstance(){
        if(self::$instance === null){
            self::$instance = new AdminController();
        }
        return self::$instance;
    }

    public function addProduct(Product $product){

    }

    public function editProduct(Product $product){

    }

    public function deleteProduct(Product $product){

    }
}