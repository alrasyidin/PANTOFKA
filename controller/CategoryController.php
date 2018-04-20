<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/15/2018
 * Time: 11:51 AM
 */

namespace controller;

class CategoryController extends AbstractController{

    private static $instance;

    /**
     * CategoryController constructor.
     */
    private function __construct(){

    }

    public static function getInstance(){
        if(self::$instance === null){
            self::$instance = new CategoryController();
        }
        return self::$instance;
    }


    public function show(){
       echo "Here must be the ". $_GET['subcategory'] ." section. This message comes from from CategoryController's show method";
    }
}