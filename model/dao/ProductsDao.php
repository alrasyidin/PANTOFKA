<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/19/2018
 * Time: 9:19 PM
 */

namespace model\dao;


class ProductsDao extends AbstractDao implements IProductsDao {

    public function __construct() {
        parent::init();
    }

}