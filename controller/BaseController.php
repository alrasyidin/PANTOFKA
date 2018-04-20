<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/18/2018
 * Time: 11:10 AM
 */

namespace controller;


class BaseController extends AbstractController {

    private static $instance;

    /**
     * BaseController constructor.
     */
    private function __construct(){

    }

    public static function getInstance(){
        if(self::$instance === null){
            self::$instance = new BaseController();
        }
        return self::$instance;
    }

    public static function index(){
        if(isset($_GET['page'])){

        }else{
            echo "Here should be an default page. This comes from BaseController's index method";
        }
    }

}