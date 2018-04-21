<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/19/2018
 * Time: 9:19 PM
 */

namespace model\dao;
use model\Product;
class ProductsDao extends AbstractDao implements IProductsDao {

    public function __construct() {
        parent::init();
    }

    public function saveNewProduct(Product $product){

        $stmt = self::$pdo->prepare(
            "SELECT color_id 
                       FROM final_project_pantofka.colors
                       WHERE  color = ? ");
        $stmt->execute(array( $product->getColor()));
        $color_id = $stmt->fetch(\PDO::FETCH_ASSOC);

        $stmt = self::$pdo->prepare(
            "SELECT material_id 
                       FROM final_project_pantofka.materials
                       WHERE  material = ? ");
        $stmt->execute(array($product->getMaterial()));
        $material_id = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt = self::$pdo->prepare(
            "SELECT material_id 
                       FROM final_project_pantofka.categories
                       WHERE  category = ? ");
        $stmt->execute(array($product->getCategory()));
        $category_id = $stmt->fetch(\PDO::FETCH_ASSOC);

        $stmt = self::$pdo->prepare(
            "INSERT INTO final_project_pantofka.products (product_name, price, info, promo_percantage, 
                        color_id, material_id, category_id) 
                       VALUES (?, ?, ?, ?, ?, ?, ? )");
        $stmt->execute(array(
            $product->getProductName(),
            $product->getPrice(),
            $product->getInfo(),
            $product->getPromoPercantage(),
            $color_id["color_id"],
            $material_id["material_id"],
            $category_id["category_id"],

        ));
    }

//    public function getColorId($color){
//
//        $stmt = self::$pdo->prepare(
//            "SELECT color_id
//                       FROM final_project_pantofka.colors
//                       WHERE  color = ? ");
//        $stmt->execute(array($color));
//        $color_id = $stmt->fetch(\PDO::FETCH_ASSOC);
//        return $color_id["color_id"];
//
//
//    }

//    public function getMaterialId($material){
//
//        $stmt = self::$pdo->prepare(
//            "SELECT material_id
//                       FROM final_project_pantofka.materials
//                       WHERE  material = ? ");
//        $stmt->execute(array($material));
//        $material_id = $stmt->fetch(\PDO::FETCH_ASSOC);
//        return $material_id["material_id"];
//
//
//    }

    public function getProducts(){


            $stmt = self::$pdo->prepare(
                "");


    }


}