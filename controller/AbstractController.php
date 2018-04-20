<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/19/2018
 * Time: 9:33 PM
 */

namespace controller;

abstract class AbstractController implements \JsonSerializable{

    public function jsonSerialize() {
        return get_object_vars($this);
    }

    public static function createController($controller_name){
        $controller_name = ucfirst($controller_name);

        if($controller_name === 'AdminController'){
            return AdminController::getInstance();

        }elseif($controller_name === 'BaseController'){
            return BaseController::getInstance();

        }elseif($controller_name === 'CartController'){
            return CartController::getInstance();

        }elseif($controller_name === 'CategoryController'){
            return CategoryController::getInstance();

        }elseif($controller_name === 'CustomerController'){
            return CustomerController::getInstance();

        }elseif($controller_name === 'FavoritesController'){
            return FavoritesController::getInstance();

        }elseif($controller_name === 'FilterController'){
            return FilterController::getInstance();

        }elseif($controller_name === 'OrderController'){
            return OrderController::getInstance();

        }elseif($controller_name === 'ProductController'){
            return ProductController::getInstance();

        }elseif($controller_name === 'SearchController'){
            return SearchController::getInstance();

        }elseif($controller_name === 'UserController'){
            return UserController::getInstance();

        }else{
            return BaseController::getInstance();

        }

    }

}