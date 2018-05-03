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
use controller\CartController;
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
                $products = &$_SESSION['cart'];
                $simplified_cart_data = CartController::simplifyCart();
                $order = new Order();
                $order->setUserId($user_id);
                $order->setProducts($products);
                $order->setTotalPrice();
                try{
                    CustomerDao::makeOrder($order);
                    foreach ($simplified_cart_data as $item_id => $size_info ) {
                        foreach ($size_info as $size_id => $size_quantity) {
                            CustomerDao::decreaseQuantities($item_id, $size_id , $size_quantity); // TODO move it to Product dao without making conflicts :D
                        }
                    }
                    $_SESSION['cart'] = array();
                    header('HTTP/1.1 200');
                    die('Successful order');
                }catch (\PDOException $e){
                    header('HTTP/1.1 500');
                    echo "DB failed: " . $e->getMessage() . ' \n ' . $e->getTraceAsString();
                }catch (\RuntimeException $e){
                    header('HTTP/1.1 400');
                    echo $e->getMessage() ; // probably there are no quantities left for the given size
                    die();
                }
            }else{
                header('HTTP/1.1 401');
                die('you must be logged in to do this.');
            }
        }else{
            header('HTTP/1.1 404');
            die('Nothing to order.');
        }
    }

    public function getOrdersData(){
        if (isset($_SESSION['user'])){
            /* @var $user_in_session User */
            $user_in_session = $_SESSION['user'];
            if (CustomerDao::userIsCustomer($user_in_session->getUserId())){
                try{
                     $history_data = CustomerDao::getOrdersData($user_in_session->getUserId());
                    // echo json_encode($history_data);
                }catch (\PDOException $e){
                    echo $e->getMessage();
                }
            }else{
                die('Nothing to display');
            }

        }
    }

    public function getOrders(){
        if (isset($_SESSION['user'])){
            /* @var $user_in_session User */
            $user_in_session = $_SESSION['user'];
            if (CustomerDao::userIsCustomer($user_in_session->getUserId())){
                try{
                    $orders = CustomerDao::getOrders($user_in_session->getUserId());
                    echo var_dump($orders);
                }catch (\PDOException $e){
                    echo $e->getMessage();
                }catch (\RuntimeException $e){
                    echo $e->getMessage();
                }
            }else{
                die('Nothing to display');
            }

        }
    }

}