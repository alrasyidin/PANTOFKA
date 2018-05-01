<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/14/2018
 * Time: 6:25 PM
 */

namespace model\dao;


use model\Order;
use model\Product;

class CustomerDao extends UserDao implements ICustomerDao {

    public static function makeOrder(Order $order){ // CHANGE size_id to be a PK in orders has product !!!!
        self::$pdo->beginTransaction();
        try{
            $stmt = self::$pdo->prepare(
            "INSERT INTO final_project_pantofka.orders ( total_price , user_id , date ) 
                       VALUES (?, ?, ? )");
            $stmt->execute(array( $order->getTotalPrice() , $order->getCustomerId() , $order->getDate()));
            $order_id = self::$pdo->lastInsertId();
            $order->setId($order_id);

        $stmt = self::$pdo->prepare(
            "INSERT INTO final_project_pantofka.orders_has_products ( product_id , order_id , quantity , size_id ) 
                                 VALUES (?, ?, ? , ? ) ");
        /* @var $product_to_buy Product*/
        $size_dao = new SizeDao();
        foreach ($order->getItems() as $product_to_buy){
            $sizes = array_unique($product_to_buy->getSizes());
            foreach ($sizes as $index=>$size) {
                $size_id = $size_dao->getSizeId($size);

                $stmt->execute(array(
                    $product_to_buy->getProductId(),
                    $order->getId(),
                    $order->getProductSizeQuantity($product_to_buy, $size),
                    $size_id));
            }
        }

        self::$pdo->commit();
        }catch (\PDOException $e){
            self::$pdo->rollBack();
            throw $e;
        }catch (\RuntimeException $e){
            throw $e;
        }
    }

    public static function cartOrderIsValid($simplified_cart){


    }

    public static function decreaseQuantities($item_id , $size_id , $size_quantity){
            $stmt = self::$pdo->prepare("SELECT quantity FROM final_project_pantofka.products_has_sizes 
                                              WHERE product_id = ? AND size_id = ?");

            $stmt->execute(array($item_id , $size_id ));
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            $db_quantity = $result['quantity'];
            $quantity = $db_quantity - $size_quantity;

            if ($quantity < 0 && $db_quantity !== null){
              throw new \RuntimeException('Abort order! Only ' . $db_quantity . 'x[size with id'.$size_id.'] pairs of item with id' . $item_id . ' are left');
            }

            $stmt = self::$pdo->prepare("
                      UPDATE final_project_pantofka.products_has_sizes 
                      SET quantity = $quantity
                      WHERE (product_id = ? AND size_id = ?)");
            $stmt->execute(array($item_id , $size_id ));


    }

}