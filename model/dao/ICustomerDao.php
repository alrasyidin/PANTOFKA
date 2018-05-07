<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/15/2018
 * Time: 10:33 AM
 */

namespace model\dao;

use model\Order;

interface ICustomerDao{

    public static function makeOrder(Order $order);

    public static function decreaseQuantities($item_id , $size_id , $size_quantity);

    public static function getOrderData($order_id);

    public static function getOrders($user_id);

    public static function userIsCustomer($user_id);
}
