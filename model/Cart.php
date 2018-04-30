<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/15/2018
 * Time: 12:02 AM
 */

namespace model;

use model\AbstractModel;

class Cart implements \JsonSerializable {

    // cart_id ?
   private $cartItems;
   private static $instance;

    private function __construct($cartItems = null)
    {
        $this->cartItems = array();
    }

    public static function init() {
        if(self::$instance == null) {
            self::$instance = new Cart();
        }
        return self::$instance;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }


    public function addItemToCart(Product $product)
    {
        $this->cartItems[] = $product;
    }


    public function getCartItems(){
        return $this->cartItems;
    }



}