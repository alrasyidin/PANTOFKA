<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/14/2018
 * Time: 6:25 PM
 */

namespace model\dao;


class CustomerDao extends UserDao implements ICustomerDao {

    public function __construct() {
        parent::init();
    }

    public function makeOrder(){

    }


}