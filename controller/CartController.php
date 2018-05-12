<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/15/2018
 * Time: 11:50 AM
 */

namespace controller;


use model\Cart;
use model\dao\CustomerDao;
use model\dao\FavoritesDao;
use model\dao\ProductsDao;
use model\dao\SizeDao;
use model\Product;
use model\Size;
use model\User;

class CartController{

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

    public function addToCart()
    {

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
                /* @var $cart Cart */
                $cart = &$_SESSION['cart'];
                /* @var $user User */
                $user = &$_SESSION['user'];


                $size_dao = new SizeDao();
                $size_id = $size_dao->getSizeId($size_no);

                if (FavoritesDao::productIsAvailable($product_id , $size_id)) { // Petra 06.05
                    if (self::productAlreadyInCart($product_id)) {
                        /* @var $product_to_increase_sizes_to Product */
                        $product_to_increase_sizes_to = self::productSizeAlreadyInCart($product_id , $size_no);
                        // If the size is added for the first time
                        // for the product we need new Product in Cart
                        if ($product_to_increase_sizes_to === false){
                            /* @var $new_product Product*/
                            $new_product = clone self::productAlreadyInCart($product_id);
                            $new_product->unsetSizeQuantity();
                            $new_product->setSizeQuantity($size_no , "asc");
                            $new_product->setSizes(array());
                            $new_product->addToSizes($size_no);

                            $cart->addItemToCart($new_product);

                            header('HTTP/1.1 200 OK');
                            die('Another product size was added to cart');
                        }
                        $product_to_increase_sizes_to->addToSizes($size_no);
                        $product_to_increase_sizes_to->setSizeQuantity($size_no , 'asc'); // The method separates sizes and quantities
                        header('HTTP/1.1 200 OK');
                        die('Another size quantity was added to cart');
                    }
                        /* @var $cart_item Product */
                        $cart_item = FavoritesDao::productIsAvailable($product_id, $size_id);
                        $cart_item->setSizeQuantity($size_no , 'asc');
                        $cart_item->addToSizes($size_no);
                        $cart->addItemToCart($cart_item);
                        header('HTTP/1.1 200 OK');
                        die('Product was added in cart');
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
        $cart_items = $cart->getCartItems();
        if (empty($_SESSION['cart'])){
            return false;
        }
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

    public function productSizeAlreadyInCart($product_id , $size_no){
        if (!isset($_SESSION['cart'])){
            return false;
        }

        /* @var $cart Cart*/
        $cart = &$_SESSION['cart'];
        $cart_items = $cart->getCartItems();
        if (empty($_SESSION['cart'])){
            return false;
        }
        if (!empty($cart_items)){
            /* @var $item Product */
            foreach ($cart_items as $index=>&$item){
                if ($item->getProductId() == $product_id && $item->getSizeQuantity($size_no) > -1 ){
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
            echo json_encode($items);
        }
    }

    public function removeItemSize(){
        if (isset($_GET['product_id']) && isset($_GET['size_no'])){
            $product_id = htmlentities($_GET['product_id']);
            $size_no = htmlentities($_GET['size_no']);

            if ($product_id < 0 || !is_numeric($product_id)){
                die('Bad data was passed to the controller!!');
            }
            /* @var $cart Cart*/
            $cart = &$_SESSION['cart'];
            $items = $cart->getCartItems();

            if (self::productSizeAlreadyInCart($product_id , $size_no)) {
                /* @var $product_to_remove Product */
                $product_to_remove = self::productSizeAlreadyInCart($product_id , $size_no);
                $product_name = $product_to_remove->getProductName();
                /* @var $cart_item Product */
                foreach ($items as $index=>&$cart_item) {
                    $size = $cart_item->getSizes()[0]; // since product in cart is defined by size

                    if ($cart_item->getProductId() === $product_id && $size === $size_no){
                        unset($items[$index]);
                    }
                }
                if (count($items) === 0){
                    $cart = Cart::init();
                    die('Last item in cart was removed!');
                }
                try{
                    $cart->setCartItems($items);
                    die('Product named "'.$product_name.'" was removed successfully from the cart! "');
                }catch (\RuntimeException $e){
                    die($e->getMessage());
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
                $size_quantity = $item->getSizeQuantity();
                $size = array_keys($size_quantity)[0];
                $quantity = $size_quantity[$size];
                $total += $price*$quantity;
            }
            echo $total;
        }
    }

    private function changeItemQuantityFromCart($quantity_change_type){
        if (isset($_GET['product_id']) && isset($_GET['size_no'])){
            $product_id = htmlentities($_GET['product_id']);
            $size_no = htmlentities($_GET['size_no']);

            if ($product_id < 0 || !is_numeric($product_id) || $size_no < self::MIN_SIZE_NUMBER || $size_no > self::MAX_SIZE_NUMBER){
                die('Bad data was passed to the controller!!');
            }
            /* @var $product Product */
            $product = self::getCartItem($product_id , $size_no);
            if ($product === null){
                try{
                    $this->removeCartItem($product_id , $size_no);
                }catch (\PDOException $e){
                    echo $e->getMessage();
                }
                die("Nothing else left to remove from the product");
            }
            $size_quantity = $product->getSizeQuantity($size_no);
            if ($size_quantity == 1 && $quantity_change_type == 'desc'){
                die($this->removeCartItem($product_id , $size_no));
            }

            // TODO
            //A way to optimize this is to have an attribute in product class
            // holding the number of available size and only when an order is made or the admin make changes on product
            // we will change that Product attribute

            $available_quantity = ProductsDao::getAvailableSizeQuantity($product_id , $size_no);
            if ($size_quantity >= $available_quantity){
                die("There aren't any more sizes to buy. Sorry-motori " . $available_quantity);
            }
            $product->setSizeQuantity($size_no , $quantity_change_type);
        }
    }

    public function decreaseItemQuantityFromCart(){
         $this->changeItemQuantityFromCart("desc");
         die("Size quantity was decreased");
    }

    public function increaseItemQuantityFromCart(){
         $this->changeItemQuantityFromCart("asc");
         die("Size quantity was increased");

    }

    public function getCartItem($product_id , $size_no){
        /* @var $cart Cart*/
        $cart = &$_SESSION['cart'];
        $items = $cart->getCartItems();
        /* @var $cart_item Product */
        foreach ($items as $index=>&$cart_item) {
            if ($cart_item->getProductId() === $product_id && $cart_item->getSizeQuantity($size_no) > 0){
               return $cart_item;
            }
        }
    }

    private function removeCartItem($product_id , $size_no){
        if (isset($_SESSION['cart'])){
            /* @var $cart Cart */
            $cart = &$_SESSION['cart'];
            $items = $cart->getCartItems();
        /* @var $cart_item Product */
        foreach ($items as $index=>&$cart_item) {
            $size = $cart_item->getSizes()[0]; // since product in cart is defined by size

            if ($cart_item->getProductId() === $product_id && $size === $size_no){
                unset($items[$index]);
            }
        }
        if (count($items) === 0){
            $cart = Cart::init();
            echo 'Last item in cart was removed!';
        }
        try{
            $cart->setCartItems($items);
            echo  'Product No."'.$product_id.'" was removed successfully from the cart! "';
        }catch (\RuntimeException $e){
            die($e->getMessage());
        }
     }
    }
}