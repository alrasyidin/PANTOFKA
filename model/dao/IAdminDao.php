<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/15/2018
 * Time: 10:32 AM
 */

namespace model\dao;


interface IAdminDao{

    public static  function changeQuantity();

    public static  function removeProduct();

    public  static function addProduct();

    public static  function unsetProduct($product_id);

    public  static function addDiscount();

}