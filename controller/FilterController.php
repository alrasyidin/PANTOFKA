<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/15/2018
 * Time: 11:24 AM
 */

namespace controller;


class FilterController extends AbstractController {

    private static $instance;

    /**
     * FilterController constructor.
     */
    private function __construct(){

    }

    public static function getInstance(){
        if(self::$instance === null){
            self::$instance = new FilterController();
        }
        return self::$instance;
    }


}