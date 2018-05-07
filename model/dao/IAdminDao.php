<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/15/2018
 * Time: 10:32 AM
 */

namespace model\dao;


use model\Product;

interface IAdminDao{

    public static function unsetProduct($product_id);

    public static function saveNewProduct(Product $product);

    public static function changeProduct(Product $product);

}