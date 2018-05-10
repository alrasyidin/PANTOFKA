<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/14/2018
 * Time: 11:18 PM
 */

namespace controller;


use model\Cart;
use model\dao\CustomerDao;
use model\dao\ProductsDao;
use model\dao\SizeDao;
use model\Order;
use model\Product;
use model\Size;
use model\User;
use controller\CartController;
class OrderController{

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
                /* @var $cart Cart*/
                $cart = &$_SESSION['cart'];
                $products = $cart->getCartItems();

                if (!isset($products)){
                    $cart = Cart::init();
                }

                if (empty($products)){
                    header('HTTP/1.1 400 Bad Request');
                    die('You are trying to order an empty cart!');
                }
                try{
                    $order = new Order();
                    $order->setUserId($user_id); // Setters can throw Runtime exception while validating
                    $order->setProducts($products);
                    $order->setTotalPrice();
                    CustomerDao::makeOrder($order); // Can throw PDO exc also

                    /* @var $cart_item Product*/
                    foreach ($products as $cart_item ) {
                            $size_quantity = $cart_item->getSizeQuantity();
                            $size_no = array_keys($size_quantity)[0];
                            $size_id = Order::getSizeId($size_no); // This should not come from order but from Size
                            $quantity = $size_quantity[$size_no];
                            CustomerDao::decreaseQuantities($cart_item->getProductId(),
                                                            $size_id ,
                                                            $quantity);

                    }
                    $cart = Cart::init();
                    header('HTTP/1.1 200');
                    die('Successful order. For more info check out History page. Cart is now empty.');
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

    public function getOrderData(){
        if (isset($_GET['order_id'])){
            $order_id = htmlentities($_GET['order_id']);
                try{
                    $order_data = CustomerDao::getOrderData($order_id);
                    echo json_encode($order_data);
                }catch (\PDOException $e){
                    header('HTTP/1.1 500');
                    echo $e->getMessage();
                }
            }

        }

    public function getOrders(){
        if (isset($_SESSION['user'])){
            /* @var $user_in_session User */
            $user_in_session = &$_SESSION['user'];
            try{
                if (CustomerDao::userIsCustomer($user_in_session->getUserId())){
                $orders = CustomerDao::getOrders($user_in_session->getUserId());
                echo json_encode($orders);
            }else{
                die('Nothing to display');
            }
            }catch (\PDOException $e){
                header('HTTP/1.1 500');
                echo $e->getMessage();
            }catch (\RuntimeException $e){
                header('HTTP/1.1 400');
                echo $e->getMessage();
            }

        }
    }

}