<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/15/2018
 * Time: 10:34 AM
 */

namespace model\dao;

use model\dao\AbstractDao;
use model\dao\UserDao;
use model\User;
use model\PasswordChange;

interface IUserDao{


    public static function getUserId($email);

    public function getUserData($email);

    public function register(User $new_user);

    public static function editUser(User $user);

    public static function emailExists($email);

    public function userExists($email);

    public static function userIsValid($email, $password);

    public static function editUserSecurity(PasswordChange $info);

}