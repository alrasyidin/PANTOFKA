<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/15/2018
 * Time: 10:34 AM
 */

namespace model\dao;


interface IUserDao{


    public function getUserId($email);

    public function getUserActiveStatus($email);

    public function userExists($email);

    public function userIsValid($email, $password);
}