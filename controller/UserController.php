<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/14/2018
 * Time: 4:12 PM
 */
namespace controller;

use model\dao\AbstractDao;
use model\dao\UserDao;
class UserController extends AbstractController {

    private static $instance;

    /**
     * UserController constructor.
     */
    private function __construct(){
        // it is empty because we can not initialise the class static instance in here
    }

    public static function getInstance(){
        if(self::$instance === null){
            self::$instance = new UserController();
        }
        return self::$instance;
    }


    public function logout(){
        session_destroy();
        header("location: index.php?page=main");
    }

    public function login(){
        $email = htmlentities($_POST['email']);
        $password = htmlentities($_POST['password']);

        if(strlen($email) < 5 || strlen($password) < 5 || strlen($email)>45 || strlen($password)>15){
            return " Bad input found in controller!!! ";
        }

        try{
            $dao = new UserDao();
            if($dao->userExists($email)){
                if($dao->userIsValid($email , sha1($password))){
                    $user =$dao->getUserData($email);
                    $user = json_encode($user);
                    $_SESSION["user"] = new User($user);
                    die();
                }else{
                    throw new \RuntimeException("!!!!!!!!! Invalid username or password. !!!!!!!!!!!! This comes from userController");
                }
            }else{
                throw new \RuntimeException("!!!!!! Invalid username or password. !!!!!!!!!!! This comes from userController");
            }
        }catch (\Exception $e){
            throw $e;
        }

    }


    public function register()
    {
        $first_name = htmlentities($_POST["first_name"]);
        $last_name = htmlentities($_POST["last_name"]);
        $gender = htmlentities($_POST["gender"]);
        $email = htmlentities($_POST["email"]);
        $password = htmlentities($_POST["password"]);
        $password_repeat = htmlentities($_POST["password_repeat"]);

        //validate data
        if (empty($first_name) || empty($last_name) || empty($gender) || empty($email) || empty($password) ||
            empty($password_repeat) && strlen($first_name) > 45 || strlen($last_name) > 45 || strlen($email) > 45 ||
            strlen($password) > 45 || (strlen($password_repeat) > 45) && (strpos($email, "@") === false) &&
            $password !== $password_repeat && strlen($password) < 5 || strlen($password_repeat) < 5) {
            throw new \RuntimeException("Validation problem");
        } else {
            try {
                $dao = new UserDao();
                $user_exists = $dao->userExists($email);
                if (!$user_exists) {
                    $user = [];
                    $user["email"] = $email;
                    $user["first_name"] = $first_name;
                    $user["last_name"]  = $last_name;
                    $user["gender"] = $gender;
                    $user["password"] = sha1($password);
                    $user = json_encode($user);
                    $new_user = new User($user);
                    $dao->register($new_user);

                } else {
                    throw new \RuntimeException("This email is already registered");
                }
            } catch (\Exception $e) {
                throw $e;
            }
        }
    }
}