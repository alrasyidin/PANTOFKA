<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/15/2018
 * Time: 11:50 AM
 */

namespace controller;


use model\Cart;
use model\dao\FavoritesDao;
use model\dao\SizeDao;
use model\Product;
use model\User;

class CartController extends AbstractController{

    const MIN_SIZE_NUMBER = 25;
    const MAX_SIZE_NUMBER = 48;

    private static $instance;

    /**
     * CartController constructor.
     */
    private function __construct(){

    }

    public static function getInstance(){
        if(self::$instance === null){
            self::$instance = new CartController();
        }
        return self::$instance;
    }


    public function addToCart(){

        if (isset($_GET['product_id']) && isset($_GET['size_no'])) {
            $product_id = htmlentities($_GET['product_id']);
            $size_no = htmlentities($_GET['size_no']);

            if (empty($_SESSION['cart'])) {
                // If this is the first time the user with that session approach the cart we will need an instance of it
                // Cart is a singleton class
                // That's because we associate it with the users session
                $_SESSION['cart'] = Cart::init();
            }

            // Basic validation
            if ($product_id < 1 || !is_numeric($product_id) || !is_numeric($size_no) ||
                $size_no < self::MIN_SIZE_NUMBER || $size_no > self::MAX_SIZE_NUMBER) {
                echo json_encode('Bad data was passed to the controller! ');
            }
            try {
                $size_dao = new SizeDao();
                $size_id = $size_dao->getSizeId($size_no);

                if (FavoritesDao::productIsAvailable($product_id, $size_id)) {
                    if (self::productAlreadyInCart($product_id)) {
                        /* @var $product Product */
                        $product = self::productAlreadyInCart($product_id);
                        $sizes = $product->getSizes();
                        $sizes[] = $size_no;
                        sort($sizes);
                        $product->setSizes($sizes);
                        header('HTTP/1.1 200 OK');
                        die('Another size was added in cart');
                    } else {
                        $cart_item = FavoritesDao::productIsAvailable($product_id, $size_id);
                        $new_cart_item = new Product(json_encode($cart_item));
                        $new_cart_item->setSizes(array($size_no));
                        /* @var $cart Cart */
                        $cart = &$_SESSION['cart'];
                        $cart->addItemToCart($new_cart_item);
                        header('HTTP/1.1 200 OK');
                        die('Product was added in cart');
                    }
                }
            } catch (\PDOException $e) {
                die($e->getTraceAsString() . '\n' . $e->getMessage());
            } catch (\RuntimeException $e) {
                die($e->getTraceAsString() . '\n' . $e->getMessage());

            }
        }
    }

    public function productAlreadyInCart($product_id){
        if (!isset($_SESSION['cart'])){
            return false;
        }

        /* @var $cart Cart*/
        $cart = &$_SESSION['cart'];
        if (empty($_SESSION['cart'])){
            return false;
        }
        $cart_items = $cart->getCartItems();
        if (!empty($cart_items)){
            /* @var $item Product */
            foreach ($cart_items as $index=>&$item){
                if ($item->getProductId() == $product_id){
                    return $cart_items[$index];
                }
            }
        }
        return false;
    }

    public function unsetCart(){
        if (isset($_SESSION['cart'])){
            /* @var $cart Cart*/
            $cart = &$_SESSION['cart'];
            $cart = Cart::init();
            echo 'Cart was unset!';
        }
    }

    public function getCartItems(){
        if (isset($_SESSION['cart'])){
            /* @var $cart Cart*/
            $cart = &$_SESSION['cart'];
            $items = $cart->getCartItems();
            /* @var $item Product*/
            foreach ($items as $index=>&$item){
                json_encode($item->getSizes());
            }
            echo json_encode($items);
        }
    }

    public function removeItemSize(){
        if (isset($_GET['product_id']) && isset($_GET['size_no'])) {
            $product_id = htmlentities($_GET['product_id']);
            $product_size = htmlentities($_GET['size_no']);

            if ($product_id < 0 || !is_numeric($product_id) || $product_size < self::MIN_SIZE_NUMBER ||
                $product_size > self::MAX_SIZE_NUMBER || !is_numeric($product_size)) {
                die('bad data passed to controller');
            }
            /* @var $cart Cart*/
            $cart = &$_SESSION['cart'];
            $items = $cart->getCartItems();
            /* @var $item Product */
            foreach ($items as $item_index=>&$item) {
                if ($item->getProductId() == $product_id) {
                    $sizes = $item->getSizes();
                    foreach ($sizes as $size_index => &$size) {
                        if ($size == $product_size) {
                            unset($sizes[$size_index]);
                            sort($sizes);
                            $item->setSizes($sizes);
                            if (count($sizes) === 0){
                                unset($items[$item_index]);
                                if (count($items) === 0){
                                    Cart::init();
                                    die('Last item was removed!');
                                }
                                try{
                                    $cart->setCartItems($items);
                                    die('Last size was removed!');
                                }catch (\RuntimeException $e){
                                    die($e->getMessage());
                                }
                                break;
                            }
                            break;
                        }
                    }
                    $items[$item_index] = $item;
                    die('Size No.'. $size .' of product "'. $item->getProductName()  .'" was successfully removed from the cart!');
                }
            }
        }
    }

    public function removeItem(){
        if (isset($_GET['product_id'])){
            $product_id = htmlentities($_GET['product_id']);
            if ($product_id < 0 || !is_numeric($product_id)){
                die('Bad data was passed to the controller!!');
            }
            /* @var $cart Cart*/
            $cart = &$_SESSION['cart'];
            $items = $cart->getCartItems();
            /* @var $cart_item Product */
            foreach ($items as $index=>&$cart_item) {
                if ($cart_item->getProductId() == $product_id){
                    unset($items[$index]);
                    if (count($items) === 0){
                        $cart = Cart::init();
                        die('Last item in cart was removed!');
                    }
                    try{
                        $cart->setCartItems($items);
                        die('Product named "'.$cart_item->getProductName().'" was removed successfully from the cart! "');
                    }catch (\RuntimeException $e){
                        die($e->getMessage());
                    }
                }
            }
        }
    }

    public function getCartTotalPrice(){
        if (isset($_SESSION['cart'])){
            /* @var $cart Cart*/
            $cart = &$_SESSION['cart'];
            $items = $cart->getCartItems();
            $total = 0;
            /* @var $item Product*/
            foreach ($items as $index=>$item){
                $price = $item->getPriceOnPromotion();
                $quantity = count($item->getSizes());
                $total += $price*$quantity;
            }
            echo $total;
        }
    }

    /** Format returned : cart[  product id ] = [  'size' => quantity  ] */
    public static function simplifyCart(){
        if (isset($_SESSION['cart'])){
            /* @var $cart Cart*/
            $cart = &$_SESSION['cart'];
            $items = $cart->getCartItems();
            $simplified_cart = array();
            $size_dao = new SizeDao();
            /* @var $item Product*/
            foreach ($items as $index=>$item) {
                $sizes =  array_count_values($item->getSizes());
                foreach ($sizes as $size_no=>&$quantity){
                    $size_no = $size_dao->getSizeId($size_no);
                    $sizes[$size_no] = $quantity;
                }
                $simplified_cart[$item->getProductId()] = $sizes;
            }
            return $simplified_cart;
        }
    }

}