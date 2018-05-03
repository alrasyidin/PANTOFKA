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

    public function getProducts($page, $entries, $category);

    public function getColorId($color);

    public function getMaterialId($material);

    public function getCategoryId($category);

    public function productExists($product_name, $material, $category, $color);

    public function getProductsCount($category);

    public function getProductById($product_id);

    public function getCategories();



}