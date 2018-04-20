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


    public function login(){
        $email = htmlentities($_POST['email']);
        $password = htmlentities($_POST['password']);

        if(strlen($email) < 5 || strlen($password) < 5 || is_numeric($email) || is_numeric($password)){
            return " Bad input found in controller!!! ";
        }

        try{
            /* @var $dao UserDao*/
            $dao = new UserDao();
        if($dao->userExists($email)){
            if($dao->userIsValid($email , $password)){
                $dao->login($email , $password);
                echo "went fine!";
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
                if (!($dao->userExists($email))) {
                    $new_user = new User();



                } else {
                    throw new \RuntimeException("Dao problem");
                }
            } catch (\Exception $e) {
                throw $e;
            }
        }
    }
}