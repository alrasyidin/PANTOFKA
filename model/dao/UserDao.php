<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/14/2018
 * Time: 3:27 PM
 */

namespace model\dao;

use model\PasswordData;
use model\User;
class UserDao extends AbstractDao implements IUserDao {

    public function __construct() {
        parent::init();
    }

    /**
     * This method takes an user object and insert its data into db, After that it sets the last insert Id in users table
     * and returns the user,
     * @param User $new_user
     * @return User
     */
    public static function register(User $new_user){
        $gender = $new_user->getGender(); // Since user object holds only the value of the users gender,
        // not its id we must take it from somewhere else,
        $gender_id = self::getGenderId($gender);
        $stmt = self::$pdo->prepare(
            "INSERT INTO final_project_pantofka.users (email, first_name, last_name, password, gender_id) 
                       VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(array(
            $new_user->getEmail(),
            $new_user->getFirstName(),
            $new_user->getLastName(),
            $new_user->getPassword(),
            $gender_id,
        ));
        $new_user->setUserId(self::$pdo->lastInsertId());
        return $new_user;
    }

    /**
     *  * This method checks if a given email and password have a corresponding row in users table,
     * If the user is valid the method returns an user object with all the data
     * @param $email
     * @param $password
     * @return User
     */
    public static function login($email , $password){
        $stmt = self::$pdo->prepare(
            "SELECT count(*) as user_is_valid FROM users WHERE (email = ? AND password = ?); ");
        $stmt->execute(array($email , sha1($password)));
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($result['user_is_valid'] != 0){
            $user = self::getUser($email);
            return $user;
        }
        return false;
    }

    /**
     * This method returns an user Id by given email
     * @param $email
     * @return mixed
     */
    public static function getUserId($email){
        $stmt = self::$pdo->prepare(
            "SELECT user_id
                       FROM final_project_pantofka.users
                       WHERE email = ?");
        $stmt->execute(array($email));
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['user_id'];
    }

    /**
     * This method returns an User object with all its data by given email
     * @param $email
     * @return User
     */
    public static function getUser($email){
        $stmt = self::$pdo->prepare(
            "SELECT user_id, email, first_name, last_name, is_admin, gender ,password 
                    FROM final_project_pantofka.users as u
                    JOIN final_project_pantofka.genders as g ON u.gender_id = g.gender_id
                    WHERE email =  ?");
        $stmt->execute(array($email));
        $user_data = $stmt->fetch(\PDO::FETCH_ASSOC);
        return new User(json_encode($user_data));
    }

    /**
     * This method takes an User object and the original id of the user, that needs data update,
     * and make update statement to its corresponding row in db, defined by user_id as PK,
     * At the end, if everything went okay, the user id is set to that object and updated user is returned
     * @param User $info
     * @param $user_id
     * @return User
     */
    public static function editUser(User $info , $user_id){
        $stmt = self::$pdo->prepare("UPDATE final_project_pantofka.users 
                                            SET email = ? , first_name = ? , last_name = ? ,
                                            gender_id = (
                                                SELECT gender_id 
                                                FROM final_project_pantofka.genders
                                                WHERE  gender = ?
                                            )
                                            WHERE user_id = ? ");
        $stmt->execute(array(
            $info->getEmail(),
            $info->getFirstName(),
            $info->getLastName(),
            $info->getGender(), // Since user class do not take gender id  but only gender value
            $user_id,
        ));
        $info->setUserId($user_id);
        return $info;
    }

    /**
     * This method takes PasswordData object, that holds an information about different types of passwords
     * (like new one, repeated one, ordinary one ,,, etc,),
     * The method executes an update statement that change users password only when the user id and old password are existing data in table
     * @param PasswordData $passwords
     */
    public static function editUserSecurity(PasswordData $passwords){
        $stmt = self::$pdo->prepare("UPDATE final_project_pantofka.users 
                                            SET password = ?
                                            WHERE (user_id = ? AND password = ?)");
        $stmt->execute(array(
            sha1($passwords->getNewPassword()),
            $passwords->getOwnerId(),
            sha1($passwords->getOldPassword())
        ));

    }

    /**
     * This method checks if a given email is already saved in DB
     * @param $email
     * @return bool
     */
    public static function emailExists($email){
        $query = self::$pdo->prepare(
            "SELECT count(*) as email_exists FROM final_project_pantofka.users 
                      WHERE email = ? ");
        $query->execute(array($email));
        $result = $query->fetch(\PDO::FETCH_ASSOC);
        return boolval($result["email_exists"]);
    }

    /**
     * This method checks if a user exists by given email
     * @param $email
     * @return bool
     */
    public static function userExists($email){
        $query = self::$pdo->prepare(
            "SELECT count(*) as user_exists FROM final_project_pantofka.users 
                      WHERE email = ? ");
        $query->execute(array($email));
        $result = $query->fetch(\PDO::FETCH_ASSOC);
        return boolval($result["user_exists"]);
    }

    /**
     * This method checks if a given id exists
     * @param $id
     * @return bool
     */
    public static function userExistsId($id){
        $query = self::$pdo->prepare(
            "SELECT count(*) as id_exists FROM final_project_pantofka.users 
                      WHERE user_id = ? ");
        $query->execute(array($id));
        $result = $query->fetch(\PDO::FETCH_ASSOC);
        return boolval($result["id_exists"]);
    }

    /**
     * This method checks if the pair of a given email and password is an existing input in users table
     * @param $email
     * @param $password
     * @return mixed
     */
    public static function userIsValid($email , $password){
        $query = self::$pdo->prepare("SELECT count(*) as user_is_valid FROM final_project_pantofka.users  
                                                WHERE email = ? && password = ? ");
        $query->execute(array($email , $password));
        $count = $query->fetch(\PDO::FETCH_ASSOC);
        return boolval($count["user_is_valid"]);
    }

    /**
     * This method takes a gender value and returns its id
     * @param $gender
     * @return mixed
     */
    public static function getGenderId($gender){
        $stmt = self::$pdo->prepare(
            "SELECT gender_id 
                       FROM final_project_pantofka.genders
                       WHERE  gender = ? ");
        $stmt->execute(array($gender));
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result["gender_id"];
    }

    /**
     * This method takes an email and checks if it already belongs to another user, User id parameter is the id of the user,
     * who wants to get that email, so we exclude it from the set of possible owners
     * @param $email_wanted
     * @param $user_id
     * @return bool
     */
    public static function emailIsTakenByAnotherUser($email_wanted , $user_id){
        $query = self::$pdo->prepare(
            "SELECT count(*) as email_is_taken FROM final_project_pantofka.users 
                      WHERE email = ? AND  user_id != ? ");
        $query->execute(array( $email_wanted , $user_id));
        $result = $query->fetch(\PDO::FETCH_ASSOC);
        return boolval($result["email_is_taken"]);
    }

}