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
    public static function getProducts($pages, $entries, $category, $style, $color, $material);

    public static function getColorId($color);

    public static function getMaterialId($material);

    public static function getCategoryId($category);

    public static function productExists($product_name, $material, $category, $color);

    public static function getProductsCount($category, $style, $color, $material);

    public static function getProductById($product_id);

    public static function getCategories();



}