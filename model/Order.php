<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/15/2018
 * Time: 10:37 AM
 */

namespace model;


class Order extends AbstractModel {

    private $id;
    private $customer; // Customer customer
    private $date; // date()
    private $items;

    public function __construct($json = null){
        parent::__construct($json);
    }

}