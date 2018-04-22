<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/19/2018
 * Time: 9:19 PM
 */

namespace model\dao;


use model\Product;

interface IProductsDao
{
    public function saveNewProduct(Product $product);

    public function getProducts();

    public function getColorId($color);

    public function getMaterialId($material);

    public function getCategoryId($category);

    public function productIdExists($product_id);


}