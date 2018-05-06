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

    public static function makeOrder(Order $order){
        self::$pdo->beginTransaction();
        try{
            $stmt = self::$pdo->prepare(
            "INSERT INTO final_project_pantofka.orders ( total_price , user_id , date ) 
                       VALUES (?, ?, ? )");
            $stmt->execute(array( $order->getTotalPrice() , $order->getUserId() , $order->getDate()));
            $order_id = self::$pdo->lastInsertId();
            $order->setOrderId($order_id);

        $stmt = self::$pdo->prepare(
            "INSERT INTO final_project_pantofka.orders_has_products 
                                    ( product_id , order_id , quantity , size_id ) 
                                 VALUES (?, ?, ? , ? ) ");
        /* @var $product_to_buy Product*/
        $size_dao = new SizeDao();
        foreach ($order->getProducts() as $product_to_buy){
            $sizes = array_unique($product_to_buy->getSizes());
            foreach ($sizes as $index=>$size) {
                $size_id = $size_dao->getSizeId($size);

                $stmt->execute(array(
                    $product_to_buy->getProductId(),
                    $order->getOrderId(),
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
              throw new \RuntimeException('Abort order! Only ' . $db_quantity . 'x[size with id '.$size_id.'] pairs of item with id' . $item_id . ' are left');
            }

            $stmt = self::$pdo->prepare("
                      UPDATE final_project_pantofka.products_has_sizes 
                      SET quantity = $quantity
                      WHERE (product_id = ? AND size_id = ?)");
            $stmt->execute(array($item_id , $size_id ));


    }

    public static function getOrderData($order_id){

        $stmt = self::$pdo->prepare("SELECT  o.total_price , o.date , p.product_name , p.product_image_url, 
                                              p.product_id, s.size_number , ohp.quantity , ohp.size_id, ohp.product_id
                                              FROM final_project_pantofka.orders as o
                                              LEFT JOIN final_project_pantofka.orders_has_products as ohp USING (order_id)
                                              JOIN final_project_pantofka.sizes as s USING (size_id)
                                              JOIN final_project_pantofka.products as p USING (product_id)
                                              WHERE o.order_id = ? ORDER BY DATE DESC");
        $stmt->execute(array($order_id));
        $orders = array();
        while ($result = $stmt->fetch(\PDO::FETCH_ASSOC)){
                $orders[] = $result;
        }
        return $orders;
    }

    public static function getOrders($user_id){

        $stmt = self::$pdo->prepare("SELECT order_id , total_price , date 
                                              FROM final_project_pantofka.orders
                                              WHERE user_id = ? ORDER BY DATE DESC");
        $stmt->execute(array($user_id));
        $orders = array();
        while ($result = $stmt->fetch(\PDO::FETCH_ASSOC)){
            $orders[] = $result;
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

    public static function getOrdersCsv($user_id){
        $stmt = self::$pdo->prepare("   SELECT o.date , o.order_id ,o.total_price , 
                                                group_concat(p.product_name) as all_purchased_products_csv,
                                                group_concat(s.size_number) as all_purchased_sizes_csv
                                                FROM orders_has_products as ohp
                                                JOIN orders as o USING (order_id)
                                                JOIN sizes as s USING (size_id)
                                                JOIN products as p USING (product_id)
                                                WHERE user_id = ? ORDER BY date DESC;");
        $stmt->execute(array($user_id));
        $orders = array();
        while ($result = $stmt->fetch(\PDO::FETCH_ASSOC)){
            $products = array( str_getcsv ( $result['all_purchased_products_csv'] , ',' ));
            $sizes = array(str_getcsv ( $result['all_purchased_sizes_csv'] , ',' ));
            $orders[] =[$result];
        }
        return $orders;
    }

}