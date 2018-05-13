<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/14/2018
 * Time: 4:12 PM
 */
namespace controller;

use model\dao\UserDao;
use model\PasswordData;
use model\User;

class UserController{

    const IS_ADMIN = '0';
    const IS_NOT_ADMIN = '1';
    const SUCCESSFUL_LOGIN_LOCATION = "location: index.php?page=edit_profile";
    const SUCCESSFUL_LOGOUT_LOCATION = "location: index.php?page=login";
    const FAILED_LOGIN_LOCATION = "location: index.php?page=failed_login";
    const UNAUTHORIZED_LOGIN_LOCATION = "location: index.php?page=main";
    const UNAUTHORIZED_REGISTER_LOCATION = "location: index.php?page=email_exists";

    const SUCCESSFUL_REGISTER_LOCATION = "location: index.php?page=login";
    const MIN_EMAIL_LENGTH = 5;
    const MAX_EMAIL_LENGTH = 45;
    const MIN_PASSWORD_LENGTH = 5;
    const MAX_PASSWORD_LENGTH = 45;
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

    public static function logout(){
        unset($_SESSION['user']);
        header("HTTP/1.1 200 OK");
        header(self::SUCCESSFUL_LOGOUT_LOCATION);
        die();
    }

    public static function getLoggedUserAsJson(){
        /* @var $user User*/
        $user = $_SESSION["user"];
        echo json_encode($user);
    }

    public static function userIsAdmin(){
        /* @var $user User*/
        $user = $_SESSION["user"];
        $userIsAdmin = $user->getisAdmin();
        if ($userIsAdmin) {
            echo self::IS_ADMIN;
        }else{
            echo self::IS_NOT_ADMIN;
        }
    }

    public static function login(){
        if (isset($_SESSION["user"])) {
            header('HTTP/1.1 401 Unauthorized');
            header(self::UNAUTHORIZED_LOGIN_LOCATION);
            die();
        }
        $email = htmlentities($_POST['email']);
        $password = htmlentities($_POST['password']);
        if (strlen($email) < self::MIN_EMAIL_LENGTH || strlen($email) > self::MAX_EMAIL_LENGTH ||
            strlen($password) < self::MIN_PASSWORD_LENGTH|| strlen($password) > self::MAX_PASSWORD_LENGTH ){
            header('HTTP/1.1 400 Bad Request');
            header(self::FAILED_LOGIN_LOCATION);
            die(json_encode(array($email , $password)));
        }
        try{

            $user_dao = new UserDao();
            if($user_dao->login($email , $password) instanceof User){ // This means the dao found a match for that data and returns an user object
                $logged_user = $user_dao->login($email , $password);
                $logged_user->__unset($logged_user->getPassword());
                $_SESSION["user"] = $logged_user;
                header('HTTP/1.1 200 OK');
                header(self::SUCCESSFUL_LOGIN_LOCATION);
                die();
            }else {
                header('HTTP/1.1 401 Unauthorized');
                header(self::FAILED_LOGIN_LOCATION);
                die(json_encode(array($email, $password)));
            }
        } catch (\PDOException $e) {

            header('HTTP/1.1 500');
            header(self::FAILED_LOGIN_LOCATION);
            die(json_encode(array($email , $password)));
        } catch (\RuntimeException $e){
            header('HTTP/1.1 400 Bad Request');
            header(self::FAILED_LOGIN_LOCATION);
            die(json_encode(array($email , $password)));
        }
    }

    public static function register()
    {
        $first_name = htmlentities($_POST["first_name"]);
        $last_name = htmlentities($_POST["last_name"]);
        $gender = htmlentities($_POST["gender"]);
        $email = htmlentities($_POST["email"]);
        $password = htmlentities($_POST["password"]);
        $password_repeat = htmlentities($_POST["password_repeat"]);
        try {
            if ($password !== $password_repeat) {
                header('HTTP/1.1 400 Bad Request');
                header(self::FAILED_LOGIN_LOCATION);
                die("Passwords mismatched");
            }
            // Data is validated in user's set-ers. They throw Runtime exceptions when data is bad.
            $new_user = new User();
            $new_user->setFirstName($first_name);
            $new_user->setLastName($last_name);
            $new_user->setGender($gender);
            $new_user->setEmail($email);
            $new_user->setPassword($password);
        } catch (\RuntimeException $e) {
            header('HTTP/1.1 400 Bad Request');
            header(self::FAILED_LOGIN_LOCATION);
            die("Bad data was passed");
        }
        try {
            if (!(UserDao::userExists($email))) {
                    UserDao::register($new_user);
                    header('HTTP/1.1 200 OK');
                    header(self::SUCCESSFUL_REGISTER_LOCATION);
                    die();
            }
        }catch
            (\PDOException $e) {
                header('HTTP/1.1 500');
                header(self::FAILED_LOGIN_LOCATION);
                die();
            } catch (\RuntimeException $e){
                header('HTTP/1.1 400');
                header(self::FAILED_LOGIN_LOCATION);
                die();
            }
    }


    public function edit(){
        $tab = htmlentities($_GET["tab"]);
        if($tab === "info"){
            self::editInfo();
        }elseif ($tab === 'security'){
            self::editSecurity();
        }
    }

    private static function editInfo(){

        // Get the resource with new data send from ajax request
        $new_data = file_get_contents("php://input");
        // create user object with it
        $changed_user = new User(json_decode(json_encode($new_data)));

        // Take user from session
        /* @var $user_in_session User*/
        $user_in_session = &$_SESSION['user'];
        // and take its Id

        $user_id = $user_in_session->getUserId();
        try {
            if (UserDao::emailIsTakenByAnotherUser( $changed_user->getEmail() , $user_id)){
                header('HTTP/1.1 401 Unauthorized');
                die('This email is already taken! Choose another one :) ');
            }

            // Edit user by given User object and id from session
            UserDao::editUser($changed_user ,$user_id);
            // Unset object saved in session
            unset($_SESSION['user']);
            // Remove password data from new User since we are going to store that information in Session
            $changed_user->__unset($changed_user->getPassword());
            // Set id
            $changed_user->setUserId($user_id);
            // Save changed user in session
            $_SESSION['user'] = $changed_user ;
            header('HTTP/1.1 200 OK');
            echo "Changes were saved";
        }catch (\RuntimeException $e){
            header('HTTP/1.1 400 Bad Request');

            die("Bad data was passed");
        }catch (\PDOException $e){
            header('HTTP/1.1 500');

            die( $e->getMessage());
        }
    }

    private static function editSecurity(){

        /* @var $user_in_session User */
        $user_in_session = &$_SESSION['user'];
        $email = $user_in_session->getEmail();
        $id = $user_in_session->getUserId();
        $request_data = file_get_contents("php://input"); //old pass and new pass

        try{
            $data = new PasswordData($request_data);
            $data->setOwnerId($id);
            if(UserDao::userIsValid($email , sha1($data->getOldPassword()))){
                UserDao::editUserSecurity($data);
                header('HTTP/1.1 200');
                die("Success!");
            }else{
                header('HTTP/1.1 401');
                die("Wrong password!");
            }
        }catch (\Exception $e){
            header('HTTP/1.1 500');
            die($e->getTraceAsString());
        }
    }

    /**
     * @return User
     */
    public static function getLoggedUser(){
        if (isset($_SESSION['user'])){
            /* @var $user_in_session User*/
            $user_in_session = &$_SESSION['user'];
            return $user_in_session;
        }
    }

}