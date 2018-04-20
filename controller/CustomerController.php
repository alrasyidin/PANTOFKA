<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/14/2018
 * Time: 6:03 PM
 */

namespace controller;


class CustomerController extends UserController {

    private static $instance;

    /**
     * CustomerController constructor.
     */
    private function __construct(){

    }

    public static function getInstance(){
        if(self::$instance === null){
            self::$instance = new CustomerController();
        }
        return self::$instance;
    }


    public final function register(){

    }

    public function editProfile(){

    }
}