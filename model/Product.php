<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/14/2018
 * Time: 3:30 PM
 */

namespace model;


class Product extends AbstractModel {

    private $id;


    public function __construct($json = null){
        parent::__construct($json);
    }


}