<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/15/2018
 * Time: 12:02 AM
 */

namespace model;


class Cart extends AbstractModel {

   private $cart = array();

   public function __construct($json = null){
       parent::__construct($json);
   }

    public function getCart(){
        return $this->cart;
    }

}