<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/14/2018
 * Time: 3:27 PM
 */

namespace model\dao;


class AdminDao extends CustomerDao implements IAdminDao {

    public static  function changeQuantity(){

    }

    public static function removeProduct(){

    }

    public static function addProduct(){

    }

    public static function unsetProduct($product_id){
        $stmt = self::$pdo->prepare("UPDATE final_project_pantofka.products_has_sizes 
                                            SET quantity = 0
                                            WHERE product_id = ?");
        $stmt->execute(array($product_id));

    }

    public  static  function productIsAvailable($product_id){
        $stmt = self::$pdo->prepare("SELECT count(*) as isAvailable FROM final_project_pantofka.products_has_sizes 
                                            WHERE (product_id = ? AND quantity > 0)");
        $stmt->execute(array($product_id));
        $r = $stmt->fetch(\PDO::FETCH_ASSOC);
        return boolval($r["isAvailable"]);

    }

    public  static  function addDiscount(){

    }

}