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

    /**
     * This method takes an Order object and insert its data to DB, It have two insert statements
     * First one inserts the data, that defines the specific order, like date,total price and user
     * Second statement inserts the concrete information about what it is it in that order, like product, size and quantity
     * If there is an exception, the transaction will prevent the possible problems
     *  @throws \PDOException
     *  @throws \RuntimeException if the validation in setters was not passed
     * @param Order $order
     */
    public static function makeOrder(Order $order){
        self::$pdo->beginTransaction();
        try{
            // 1, Insert orders basic info
            $stmt = self::$pdo->prepare(
            "INSERT INTO final_project_pantofka.orders ( total_price , user_id , date ) 
                       VALUES (?, ?, ? )");
            $stmt->execute(array( $order->getTotalPrice() , $order->getUserId() , $order->getDate()));
            $order_id = self::$pdo->lastInsertId();
            $order->setOrderId($order_id);

            //2, Insert the data for each product inside order
        $stmt = self::$pdo->prepare(
            "INSERT INTO final_project_pantofka.orders_has_products 
                                    ( product_id , order_id , quantity , size_id ) 
                                 VALUES (?, ?, ? , ? ) ");
        /* @var $product_to_buy Product*/
        $size_dao = new SizeDao();
        //Get the products
        $products = $order->getProducts();
        foreach ($products as $product_to_buy){
            // Pick only the unique values of sizes for the specific product
            $sizes = array_unique($product_to_buy->getSizes());
            foreach ($sizes as $index=>$size) {
                $size_id = $size_dao->getSizeId($size);

                $stmt->execute(array(
                    $product_to_buy->getProductId(),
                    $order->getOrderId(),
                    $product_to_buy->getSizeQuantity($size),
                    $size_id));
            }
        }

        self::$pdo->commit();
        }catch (\PDOException $e){
            self::$pdo->rollBack();
            throw $e;
        }catch (\RuntimeException $e){
            self::$pdo->rollBack();
            throw $e;
        }
    }

    /**
     * This method takes an product and the wanted size and quantity of it, It is used mainly when an order is made,
     * It inserts the difference of the real size count and wanted count of the size
     * @throws \PDOException
     * @throws \RuntimeException if the data passed in was not valid
     * @param $item_id
     * @param $size_id
     * @param $size_quantity
     */
    public static function decreaseQuantities($item_id , $size_id , $size_quantity){

            $stmt = self::$pdo->prepare("SELECT quantity FROM final_project_pantofka.products_has_sizes 
                                              WHERE product_id = ? AND size_id = ?");

            $stmt->execute(array($item_id , $size_id ));
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            /*  $db_quantity defines the current quantity of a size of a given product */
            $db_quantity = $result['quantity'];
            /*  $quantity defines the remaining count of that specific size if an order with that data is made */
            $quantity = $db_quantity - $size_quantity;

            // If there are not enough sizes or the passed data was incorrect an Runtime ex is thrown to kill the execution
            if ($quantity < 0 && $db_quantity !== null){
              throw new \RuntimeException('Abort order! Only ' . $db_quantity . 'x[size with id '.$size_id.'] pairs of item with id' . $item_id . ' are left');
            }
            // If the passed data was adequate we can make an update statement
            // that change the quantity of the size with the value,calculated above
            $stmt = self::$pdo->prepare("
                      UPDATE final_project_pantofka.products_has_sizes 
                      SET quantity = $quantity
                      WHERE (product_id = ? AND size_id = ?)");
            $stmt->execute(array($item_id , $size_id ));
    }

    /**
     * This method takes an order id and returns an array with items,that are Order objects, containing the data for the concrete
     * id given as parameter, This order objects differ from each other by product , size and quantity of the size/
     * @param $order_id
     * @return array
     */
    public static function getOrderData($order_id){

        $stmt = self::$pdo->prepare("SELECT  o.total_price , o.date , p.product_name , p.product_image_url, 
                                              p.product_id, s.size_number , ohp.quantity , ohp.size_id, ohp.product_id
                                              FROM final_project_pantofka.orders as o
                                              LEFT JOIN final_project_pantofka.orders_has_products as ohp USING (order_id)
                                              JOIN final_project_pantofka.sizes as s USING (size_id)
                                              JOIN final_project_pantofka.products as p USING (product_id)
                                              WHERE o.order_id = ? ORDER BY product_name ASC ");
        $stmt->execute(array($order_id));
        $order_items = array();
        /* @var $order_item Order*/
        while ($order_item = $stmt->fetch(\PDO::FETCH_OBJ)){
                $order_items[] = $order_item;
        }
        return $order_items;
    }

    /**
     * This method returns all orders made by an user, where an order is defined by price,date and id,
     * It returns an array of orders, ordered by the date of ordering
     * @param $user_id
     * @return array
     */
    public static function getOrders($user_id){

        $stmt = self::$pdo->prepare("SELECT order_id , total_price , date 
                                              FROM final_project_pantofka.orders
                                              WHERE user_id = ? ORDER BY DATE DESC");
        $stmt->execute(array($user_id));
        $orders = array();
        /* @var $order Order*/
        while ($order = $stmt->fetch(\PDO::FETCH_ASSOC)){
            $orders[] = $order;
        }
        return $orders;
    }

    /**
     * This method check if the user,by given id, have had any orders made, Returns a boolean value
     * @param $user_id
     * @return bool
     */
    public static function userIsCustomer($user_id){
        $stmt = self::$pdo->prepare("SELECT count(*) as user_is_customer 
                                            FROM final_project_pantofka.orders 
                                            WHERE user_id");
        $stmt->execute(array($user_id));
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return boolval($result["user_is_customer"]);
    }

}