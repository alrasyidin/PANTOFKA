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
            if ($product_id < 1 || !is_numeric($product_id) || $size_no < 25 || $size_no > 47 || !is_numeric($size_no)){
                json_encode('Bad data was passed in controller - ' . var_dump($product_id) . ' or ' . var_dump($size_no));
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

    // NOT TESTED YET TODO
    public function removeProduct(){
        if (isset($_GET['cartItemId']) && isset($_GET['cartIdemNo'])){

            $cart_item_id = htmlentities($_GET['cartItemId']);
            $cart_item_size_no = htmlentities($_GET['cartItemSizeNo']);

            $cart = &$_SESSION['cart'];
            /* @var $item Product */
            foreach ($cart as &$item){
                if ($item->getProductId() == $cart_item_id){
                    $sizes = $item->getSizes();
                    foreach ($sizes as $index=>&$size){
                        if ($size == $cart_item_size_no){
                            unset($sizes[$index]);
                            break;
                        }
                    }
                }
            }
        }
    }

    public function unsetCart(){
        self::__unsetCart();
    }

    private static function __unsetCart(){
        unset($_SESSION['cart']);
    }

}