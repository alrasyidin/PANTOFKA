<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/14/2018
 * Time: 3:27 PM
 */

namespace model\dao;

use model\User;
class UserDao extends AbstractDao implements IUserDao {

    public function __construct() {
        parent::init();
    }

    public function register(User $new_user){
        $stmt = self::$pdo->prepare(
            "SELECT gender_id 
                       FROM final_project_pantofka.genders
                       WHERE  gender = ? ");
        $stmt->execute(array($new_user->getGender()));
        $gender_id = $stmt->fetch(\PDO::FETCH_ASSOC);


        $stmt = self::$pdo->prepare(
            "INSERT INTO final_project_pantofka.users (email, first_name, last_name, password, gender_id) 
                       VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(array(
            $new_user->getEmail(),
            $new_user->getFirstName(),
            $new_user->getLastName(),
            $new_user->getPassword(),
            $gender_id["gender_id"],
        ));

    }

    public function userIsAdmin(){


    }

    public function getUserId($email){
        $stmt = self::$pdo->prepare(
            "SELECT user_id
                       FROM final_project_pantofka.users
                       WHERE email = ?");
        $stmt->execute(array($email));
        $user_id = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $user_id;
    }

    public function getUserData($email){
        $stmt = self::$pdo->prepare(
            "SELECT user_id, email, first_name, last_name, is_admin, gender  FROM final_project_pantofka.users as u
                    JOIN final_project_pantofka.genders as g ON u.gender_id = g.gender_id
                    WHERE email =  ?");
        $stmt->execute(array($email));
        $user_data = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $user_data;
    }


    public function getUserActiveStatus($email){

    }

    public function userExists($email){
        $query = self::$pdo->prepare(
            "SELECT count(*) as user_exists FROM final_project_pantofka.users 
                      WHERE email = ? ");
        $query->execute(array($email));
        $count = $query->fetch(\PDO::FETCH_ASSOC);
        return boolval($count["user_exists"]);
    }

    public function userIsValid($email , $password){
        $query = AbstractDao::$pdo->prepare("SELECT count(*) as user_is_valid FROM final_project_pantofka.users  WHERE email = ? && password = ? ");
        $query->execute(array($email , $password));
        $count = $query->fetch(\PDO::FETCH_ASSOC);
        return $count["user_is_valid"];
    }
}