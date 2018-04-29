<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 21.4.2018 г.
 * Time: 6:57
 */

namespace model\dao;


use model\AbstractModel;
use model\Size;

class SizeDao extends AbstractDao implements ISizeDao
{
    public function __construct() {
        parent::init();
    }

    public function getSizeId($size){
        $stmt = self::$pdo->prepare(
            "SELECT size_id
                       FROM final_project_pantofka.sizes
                       WHERE size_number = ?");
        $stmt->execute(array($size));
        $size_id = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $size_id['size_id'];
    }


    public function saveSize($product_id, Size $size){
            $size_id = $this->getSizeId($size->getSizeNumber());

                  $stmt = self::$pdo->prepare(
            "INSERT INTO final_project_pantofka.products_has_sizes (product_id, size_id, quantity) 
                       VALUES (?, ?, ?)");
        $stmt->execute(array(
            $product_id,
            $size_id,
            $size->getSizeQuantity()
        ));

    }

    public  function getSizesAndQuantities($product_id)
    {
       $stmt = self::$pdo->prepare(
           "SELECT s.size_id, s.size_number, ps.quantity as size_quantity FROM final_project_pantofka.sizes as s
                      JOIN products_has_sizes as ps ON s.size_id = ps.size_id
                      JOIN products as p ON ps.product_id = p.product_id
                      WHERE p.product_id = ?");
       $stmt->execute(array($product_id));
       $sizes = [];
        While ($query_result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $size = new Size(json_encode($query_result));
            $sizes[]=$size;
        }
        return $sizes;
    }
}