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

    public function makeOrder(Order $data);
}
