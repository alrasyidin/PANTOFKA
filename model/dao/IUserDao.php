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
use model\PasswordData;

interface IUserDao{

    public static function register(User $new_user);

    public static function login($email , $password);

    public static function getUserId($email);

    public static function getUser($email);

    public static function editUser(User $info , $user_id);

    public static function editUserSecurity(PasswordData $passwords);

    public static function emailExists($email);

    public static function userExists($email);

    public static function userExistsId($id);

    public static function userIsValid($email , $password);

    public static function getGenderId($gender);

    public static function emailIsTakenByAnotherUser($email_wanted , $user_id);

}