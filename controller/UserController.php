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
use model\PasswordChange;
use model\User;

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
                    header('location: index.php?page=edit_profile');
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

    public function getLoggedUser(){
        $user = $_SESSION["user"];
        UserDao::init();
        ///$user = UserDao::getUserData($data['email']);

        /* @var $obj \JsonSerializable*/
        $obj = $user->jsonSerialize();
        echo json_encode($obj);
    }

    public function edit(){
        $tab = htmlentities($_GET["tab"]);

        if($tab === "info"){
            self::editInfo();
        }elseif ($tab === 'security'){
          return self::editSecurity();
        }
    }

    private static function editInfo(){
            $user_in_session = new User(json_encode($_SESSION['user']->jsonSerialize()));
            $request_data = file_get_contents("php://input");
            $user = new User($request_data);
            try {
               if(UserDao::emailExists($user->getEmail()) && $user->getEmail() !== $user_in_session->getEmail()){
                   echo "There is another user with that email";
                   return;
               }
               UserDao::editUser($user);
               unset($_SESSION['user']);
               $_SESSION['user'] = $user;
               echo "Changes were saved";
            }
            catch (\PDOException $e){
               echo $e->getMessage();
            }
        }

    private static function editSecurity(){
        $user_in_session = new User(json_encode($_SESSION['user']->jsonSerialize()));
        $email = $user_in_session->getEmail();
        $id = $user_in_session->getId();

        $request_data = file_get_contents("php://input");
        //old pass and new pass
        $data = new PasswordChange($request_data);
        $data->setOwnerId($id);

        try{
            UserDao::init();
            if(UserDao::userIsValid($email , sha1($data->getOldPassword()))){
                UserDao::editUserSecurity($data);
                echo "Success!";
            }else{
                echo "Wrong password! This text comes from UserController";
            }
        }catch (\PDOException $e){
           echo $e->getMessage();
        }
    }

}