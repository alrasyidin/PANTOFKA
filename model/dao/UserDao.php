<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/14/2018
 * Time: 3:27 PM
 */

namespace model\dao;


class UserDao extends AbstractDao implements IUserDao {

    public function __construct() {
        parent::init();
    }

    public function register(User $new_user){

    }

    public function userIsAdmin(){

    }

    public function getUserId($email){

    }


    public function getUserActiveStatus($email){

    }

    /**
     * This method check if there is a user already registered in db with the given email address (Emails in db are unique).
     * Returns boolean value.
     * @param $email
     * @return mixed
     */
    public function userExists($email){
       $query = self::$pdo->prepare("SELECT count(*) as user_exists FROM final_project_pantofka.users  ");
       $query->execute(array($email));
       $count = $query->fetch(\PDO::FETCH_ASSOC);
       return boolval($count["user_exists"]);
    }


    /**
     * This method check if user data: email with password is correct. Returns boolean value.
     * @param $email
     * @param $password
     * @return mixed
     */
    public function userIsValid($email , $password){
        $query = self::$pdo->prepare("SELECT count(*) as user_is_valid FROM final_project_pantofka.users  WHERE email = ? && password = ? ");
        $query->execute(array($email , $password));
        $count = $query->fetch(\PDO::FETCH_ASSOC);
        return boolval($count["user_is_valid"]);
    }
}