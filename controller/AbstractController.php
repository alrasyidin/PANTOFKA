<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/19/2018
 * Time: 9:33 PM
 */

namespace controller;

abstract class AbstractController{

    public static function createController($controller_name)
    {
        $controller_name = ucfirst($controller_name);
        // An Factory added just for fun :) Practice Makes Perfect!

        switch ($controller_name) {
            case 'AdminController':
                return AdminController::getInstance();

            case 'CategoryController':
                return CategoryController::getInstance();

            case 'CartController':
                return CartController::getInstance();

            case 'FavoritesController':
                return FavoritesController::getInstance();

            case 'OrderController':
                return OrderController::getInstance();

            case 'ProductController':
                return ProductController::getInstance();

            case 'UserController':
                return UserController::getInstance();

            case 'RatingController':
                return RatingController::getInstance();
            default:
                header('location: index.php?page=404.html');
                die();
        }
    }
}