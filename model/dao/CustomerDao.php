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

    public static function getOrdersData($user_id){

        $stmt = self::$pdo->prepare("SELECT order_id , total_price , date , product_name , size_number , quantity , size_id, product_id
                                              FROM final_project_pantofka.orders 
                                              LEFT JOIN final_project_pantofka.orders_has_products USING (order_id)
                                              JOIN final_project_pantofka.sizes USING (size_id)
                                              JOIN final_project_pantofka.products USING (product_id)
                                              WHERE user_id = ? ORDER BY DATE DESC");
        $stmt->execute(array($user_id));
        $orders = array();
        while ($result = $stmt->fetch(\PDO::FETCH_ASSOC)){
                $orders[] = [
                    'orderId' => $result['order_id'] ,
                    'totalPrice' =>$result['total_price'] ,
                    'date' => $result['date'] ,
                    'productId' => $result['product_id'],
                    'productName' => $result['product_name'],
                    'sizeNumber' => $result['size_number'],
                    'sizeId' => $result['size_id'],
                    'quantity' => $result['quantity']
                ];
        }
        return $orders;
    }

    public static function userIsCustomer($user_id){
        $stmt = self::$pdo->prepare("SELECT count(*) as user_is_customer 
                                            FROM final_project_pantofka.orders 
                                            WHERE user_id");
        $stmt->execute(array($user_id));
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return boolval($result["user_is_customer"]);
    }

    public static function getOrders($user_id){
        $stmt = self::$pdo->prepare("SELECT order_id , total_price , date
                                              FROM final_project_pantofka.orders
                                              WHERE user_id = ? ORDER BY DATE DESC");
        $stmt->execute(array($user_id));
        $orders = array();
        while ($result = $stmt->fetch(\PDO::FETCH_ASSOC)){
            $orders[] = [
                'orderId' => $result['order_id'] ,
                'totalPrice' =>$result['total_price'] ,
                'date' => $result['date'] ,
            ];
        }
        return $orders;
    }

}