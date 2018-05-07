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

    /**
     * The method receives a size number and returns the size_id of the size.
     * @param $size
     * @return mixed
     */
    public function getSizeId($size){
        $stmt = self::$pdo->prepare(
            "SELECT size_id
                       FROM final_project_pantofka.sizes
                       WHERE size_number = ?");
        $stmt->execute(array($size));
        $size_id = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $size_id['size_id'];
    }

    /**
     * The method receives product id and Size object an saves the size for this product in DB.
     * @param $product_id
     * @param Size $size
     */
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


    /**
     * This method receives product id and returns an array of Size objects of the product with this id.
     * @param $product_id
     * @return array
     */
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

    /**
     * This method receives product id and returns an array of Size objects of the product with this id
     * which quantity is more than 0.
     * @param $product_id
     * @return array
     */
    public static function getAvailableSizes($product_id){
        $stmt = self::$pdo->prepare(
            "SELECT size_number FROM final_project_pantofka.sizes as s
                      JOIN products_has_sizes as ps ON s.size_id = ps.size_id
                      JOIN products as p ON ps.product_id = p.product_id
                      WHERE p.product_id = ? AND ps.quantity > 0");
        $stmt->execute(array($product_id));
        $sizes = array();
        while ($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $sizes[] = $result['size_number'];
        }
        return $sizes;
    }
}