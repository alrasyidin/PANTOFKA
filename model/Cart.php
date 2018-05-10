<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/15/2018
 * Time: 12:02 AM
 */

namespace model;

use model\AbstractModel;

class Cart{

   private $cart_items;
   private static $instance;

    private function __construct()
    {
        $this->cart_items = array();
    }

    public static function init() {
        if(self::$instance == null) {
            self::$instance = new Cart();
        }
        return self::$instance;
    }

    public function addItemToCart(Product &$product)
    {
        $this->cart_items[] = $product;
    }

    public function setCartItems($cart_items)
    {
        $this->cart_items = array();
        if (!empty($cart_items)){
            foreach($cart_items as $index=>&$cart_item) {
                if (!($cart_item instanceof Product)){
                    throw new \RuntimeException('Expected array of products but got something else!');
                }
                $this->addItemToCart($cart_item);
            }
        }

    }

    public function getCartItems(){
        return $this->cart_items;
    }

}