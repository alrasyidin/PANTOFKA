<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/15/2018
 * Time: 10:32 AM
 */

namespace model\dao;


interface IAdminDao{

    public function changeQuantity();

    public function removeProduct();

    public function addProduct();

    public function changeProduct();

    public function addDiscount();

}