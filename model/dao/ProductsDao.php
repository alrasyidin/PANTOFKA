<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/19/2018
 * Time: 9:19 PM
 */

namespace model\dao;

use model\Product;

class ProductsDao extends AbstractDao implements IProductsDao
{

    public function __construct()
    {
        parent::init();
    }

    public function getProductId(Product $product)
    {

        $color_id = $this->getColorId($product->getColor());
        $material_id = $this->getMaterialId($product->getMaterial());
        $category_id = $this->getCategoryId($product->getColor());


        $stmt = self::$pdo->prepare(
            "SELECT product_id 
                       FROM final_project_pantofka.products
                       WHERE  product_name = ? AND price=? AND color_id = ? AND material_id = ? AND category_id = ?  ");
        $stmt->execute(array($product->getProductName(),
            $product->getPrice(),
            $color_id,
            $material_id,
            $category_id,


        ));
        $product_id = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $product_id["product_id"];

    }

    public function saveNewProduct(Product $product)
    {
        $color_id = $this->getColorId($product->getColor());

        $material_id = $this->getMaterialId($product->getMaterial());

        $category_id = $this->getCategoryId($product->getColor());


//try{
//    self::$pdo->beginTransaction();
        $stmt = self::$pdo->prepare(
            "INSERT INTO final_project_pantofka.products (product_name, price, info, promo_percantage, 
                        color_id, material_id, category_id) 
                       VALUES (?, ?, ?, ?, ?, ?, ? )");
        $stmt->execute(array(
            $product->getProductName(),
            $product->getPrice(),
            $product->getInfo(),
            $product->getPromoPercantage(),
            $color_id,
            $material_id,
            $category_id,
        ));

//        self::$pdo->commit();
//    }catch (\PDOexeption $e)
//{
//self::$pdo->e->rollback();
//throw $e;

    }

    public function getColorId($color)
    {

        $stmt = self::$pdo->prepare(
            "SELECT color_id
                       FROM final_project_pantofka.colors
                       WHERE  color = ? ");
        $stmt->execute(array($color));
        $color_id = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $color_id["color_id"];


    }

    public function getMaterialId($material)
    {

        $stmt = self::$pdo->prepare(
            "SELECT material_id
                       FROM final_project_pantofka.materials
                       WHERE  material = ? ");
        $stmt->execute(array($material));
        $material_id = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $material_id["material_id"];


    }


    public function getCategoryId($category)
    {

        $stmt = self::$pdo->prepare(
            "SELECT category_id
                       FROM final_project_pantofka.categories
                       WHERE  category = ? ");
        $stmt->execute(array($category));
        $material_id = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $material_id["material_id"];


    }

    public
    function getProducts()
    {

        $stmt = self::$pdo->query(
            "SELECT p.product_id, p.product_name, p.price, p.info, p.product_image_url, p.promo_percentage,
                      c.color,  m.material,  cat.name as category,  cat.parent_id
                      FROM final_project_pantofka.products as p
                      JOIN colors as c ON p.color_id = c.color_id
                      JOIN materials as m ON p.material_id = m.material_id
                      JOIN categories as cat ON p.category_id = cat.category_id
                       ");
        $products = [];
        While ($query_result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $products[] = $query_result;
        }
        return $products;

    }
    public function productIdExists($product_id){
        $query = self::$pdo->prepare(
            "SELECT count(*) as product_id_exists FROM final_project_pantofka.products 
                      WHERE product_id = ? ");
        $query->execute(array($product_id));
        $count = $query->fetch(\PDO::FETCH_ASSOC);
        return boolval($count["product_id_exists"]);
    }


}