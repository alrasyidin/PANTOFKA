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
                       WHERE size = ?");
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

}