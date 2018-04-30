<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/15/2018
 * Time: 11:50 AM
 */

namespace controller;


use model\dao\FavoritesDao;
use model\dao\SizeDao;
use model\Product;
use model\User;

class CartController extends AbstractController{

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

        if (isset($_GET['id']) && isset($_GET['size'])){
            $product_id = htmlentities($_GET['id']);
            $size_no = htmlentities($_GET['size']);
            if ($product_id < 1 || !is_numeric($product_id) || $size_no < 25 || $size_no > 48 || !is_numeric($size_no)){
               echo json_encode('Bad data was passed in controller - ' . var_dump($product_id) . ' or ' . var_dump($size_no));
            }
            try{
                $size_dao = new SizeDao();
                $size_id = $size_dao->getSizeId($size_no); // TODO make everything static in DAO's ~!
                if(FavoritesDao::productIsAvailable($product_id , $size_id)){
                    if (self::productAlreadyInCart($product_id)){
                        /* @var $product Product */
                        $product = self::productAlreadyInCart($product_id);
                        $sizes = $product->getSizes();
                        $sizes[] = $size_no;
                        $product->setSizes($sizes);
                        echo 'Another size was added in cart';
                    }else{
                        $cart_item = FavoritesDao::productIsAvailable($product_id , $size_id);
                        $new_cart_item = new Product(json_encode($cart_item));
                        $new_cart_item->setSizes(array($size_no));
                        if (isset($_SESSION['user'])){
                            $user_in_session = &$_SESSION['user'];
                            /* @var $user_in_session User */
                            $user_in_session->addToCart($new_cart_item);
                        }else{
                           // $guest = new User(json_encode(array("first_name"=>'Guest')));
                          //  $guest->addToCart($new_cart_item);
                         //   $_SESSION['guest'] = $guest;
                        }
                        $_SESSION['cart'][] = $new_cart_item;
                        echo 'Product was added in cart';
                    }
                    header('HTTP/1.1 200 OK');
                    die();
                }
            }catch (\PDOException $e){
                die($e->getTraceAsString() . '\n' . $e->getMessage());
            }catch (\RuntimeException $e){
                die($e->getTraceAsString() . '\n' . $e->getMessage());

            }
        }
    }

    public static function productAlreadyInCart($product_id){
        /* @var $cart \ArrayIterator*/
        $cart = &$_SESSION['cart'];
        if (!empty($cart)){
            /* @var $item Product */
            foreach ($cart as &$item){
                if ($item->getProductId() == $product_id){
                    return $item;
                }
            }
        }
        return false;
    }

    public function unsetCart(){
        if (isset($_SESSION['cart'])){
            $cart_in_session = &$_SESSION['cart'];
            if (isset($_SESSION['user'])){
                /* @var $user_in_session User */
                $user_in_session = $_SESSION['user'];
                $user_in_session->unsetCart();
                echo 'Logged user no longer has cart items in session!';
            }
            $cart_in_session = array();
            echo 'Cart was unset!';
        }
    }

    public function getCartItems(){
      if (isset($_SESSION['cart'])){
        $cart = $_SESSION['cart'];
        echo json_encode($cart);
      }
    }

    public function removeItemSize()
    {
        if (isset($_GET['productId']) && isset($_GET['sizeNo'])) {
            $product_id = htmlentities($_GET['productId']);
            $product_size = htmlentities($_GET['sizeNo']);
            if ($product_id < 0 || !is_numeric($product_id) || $product_size < 25 || $product_size > 48 || !is_numeric($product_size)) {
                die('bad data passed to controller');
            }
            $cart = &$_SESSION['cart'];
            /* @var $item Product */
            foreach ($cart as $item_index=>&$item) {

                if ($item->getProductId() == $product_id) {
                    $sizes = $item->getSizes();
                    foreach ($sizes as $size_index => &$size) {
                        if ($size == $product_size) {
                            unset($sizes[$size_index]);
                            $sizes = array_values($sizes);
                            $item->setSizes($sizes);
                            if (count($sizes) === 0){
                                unset($cart[$item_index]);
                                if (isset($_SESSION['user'])){
                                    $user_in_session = &$_SESSION['user'];
                                    /* @var $user_in_session User */
                                    $user_in_session->removeCartItem($item_index);
                                }
                                $cart = array_values($cart);
                                die('last size was removed');
                                break;
                            }
                            break;
                        }
                    }
                    $cart[$item_index] = $item;
                    echo var_dump($item->getSizes());
                }
            }
        }
    }

    public function removeItem(){
       if (isset($_GET['productId'])){
           $product_id = htmlentities($_GET['productId']);
           if ($product_id < 0 || !is_numeric($product_id)){
               die('bad data passed to controller');
           }

           $cart = &$_SESSION['cart'];
           /* @var $cart_item Product */
           foreach ($cart as $index=>&$cart_item) {
               if ($cart_item->getProductId() === $product_id){
                   unset($cart[$index]);
                   $cart = array_values($cart);
                   die('success!');
               }
           }
       }
    }

    public function getCartTotalPrice(){
        if (isset($_SESSION['cart'])){
            $cart = $_SESSION['cart'];
            $total = 0;
            /* @var $item Product*/
            foreach ($cart as $item_index=>$item){
                $price = $item->getPriceOnPromotion();
                $quantity = count($item->getSizes());
                $total += $price*$quantity;
            }
            echo $total;
        }
    }

}