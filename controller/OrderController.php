<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/14/2018
 * Time: 11:18 PM
 */

namespace controller;


use model\dao\CustomerDao;
use model\dao\ProductsDao;
use model\Order;
use model\User;
class OrderController extends AbstractController {

    private static $instance;

    /**
     * OrderController constructor.
     */
    private function __construct(){

    }

    public static function getInstance(){
        if(self::$instance === null){
            self::$instance = new OrderController();
        }
        return self::$instance;
    }

    public function orderCart(){
        if (isset($_SESSION['cart'])){
            if(isset($_SESSION['user'])){
                /* @var $user_in_session User */
                $user_in_session = &$_SESSION['user'];
                $user_id = $user_in_session->getUserId();
                $cart = $_SESSION['cart'];
                try{
                    die('In progress...');
                    if(CustomerDao::orderIsValid($user_id , $cart)){
                        $order = CustomerDao::orderIsValid($user_id , $cart);
                        CustomerDao::makeOrder($order);
                    }

                }catch (\RuntimeException $e){
                    echo "HERE" . $e->getMessage();
                }
            }else{
                die('you must be logged in to do this.');

            }
        }else{
            die('Nothing to order.');
        }
    }
}