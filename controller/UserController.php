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

    public static function logout(){
        unset($_SESSION['user']);
        header('HTTP/1.1 200 OK');
        header("location: index.php?page=login");
        die("Went fine!");
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
            echo "1";
        }else{
            echo "0";
        }
    }

    public static function login(){
        if (isset($_SESSION["user"])) {
            header('HTTP/1.1 401 Unauthorized');
            header("location: index.php?page=main");
            die();
        }
        $email = htmlentities($_POST['email']);
        $password = htmlentities($_POST['password']);
        try{
            $user_dao = new UserDao();
            if($user_dao->login($email , $password) instanceof User){
                $logged_user = $user_dao->login($email , $password);
                $logged_user->__unset($logged_user->getPassword());
                $_SESSION["user"] = $logged_user;
                header('HTTP/1.1 200 OK');
                header("location: index.php?page=edit_profile");
                die();
            }else {
                header('HTTP/1.1 401 Unauthorized');
                header("location: index.php?page=failed_login");
                die();
            }
        } catch (\PDOException $e) {
            // header('HTTP/1.1 500 Something went terribly wrong . . .');
            // header("location: index.php?page=failed_login");
            echo "Problem in DB with login: " . $e->getMessage() ."\n". $e->getCode() ."\n".$e->getTraceAsString();
            die();
        } catch (\RuntimeException $e){
            //header('HTTP/1.1 401 Unauthorized');
            //header("location: index.php?page=failed_login");
            echo "Problem with validation in User class: " . $e->getMessage() ."\n".$e->getTraceAsString();
            die();
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
        try{
            if($password !== $password_repeat){
                echo "Passwords mismatched!";
                die();
            }
            // Data is validated in user's set-ers. They throw Runtime exceptions when data is bad.
            $new_user = new User();
            $new_user->setFirstName($first_name);
            $new_user->setLastName($last_name);
            $new_user->setGender($gender);
            $new_user->setEmail($email);
            $new_user->setPassword($password);
        }catch (\RuntimeException $e){
            // Did not pass the validation 400 (Bad Request)
            // header('HTTP/1.1 400 Bad Request');
            echo $e->getMessage() . '-> ' .  $e->getCode();
            die();
        }
        try {
            if (!(UserDao::userExists($email))) {

                if (UserDao::register($new_user) instanceof User){
                    header('HTTP/1.1 200 OK');
                    header("location: index.php?page=login");
                    die();
                }else{
                    echo "Problem in Dao:\n";
                }
            } else {
                header('HTTP/1.1 401 Unauthorized');
                header("location: index.php?page=email_taken");
                die();
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
            die();
        }
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
        try {
            // Get the resource with new data send from ajax request
            $new_data = file_get_contents("php://input");
            // create user object with it
            $changed_user = new User(json_decode(json_encode($new_data)));

            // Take user from session
            /* @var $user_in_session User*/
            $user_in_session = &$_SESSION['user'];
            // and take its Id
            $user_id = $user_in_session->getUserId();

            if (UserDao::emailIsTakenByAnotherUser( $changed_user->getEmail() , $user_id)){
                die('This email is already taken! Choose another one :) ');
            }

            // Edit user by given User object and id from session
            UserDao::editUser($changed_user ,$user_id);
            // Unset object saved in session
            unset($_SESSION['user']);
            // Remove password data from new User
            $changed_user->__unset($changed_user->getPassword());
            // Set id
            $changed_user->setUserId($user_id);
            // Save changed user in session
            $_SESSION['user'] = $changed_user ;
            header('HTTP/1.1 200 OK');
            echo "Changes were saved";
        }catch (\RuntimeException $e){
            echo "Bad data: " . $e->getMessage() . "\n Found in:\n" . $e->getTraceAsString();
        }catch (\PDOException $e){
            echo "500: " . $e->getMessage() . "\n Found in:\n" . $e->getTraceAsString();
        }catch (\Exception $e){
            echo "Random exception: " . $e->getMessage() . "\n Found in:\n" . $e->getTraceAsString();

        }
    }

    private static function editSecurity(){


        /* @var $user_in_session User */
        $user_in_session = $_SESSION['user'];
        $email = $user_in_session->getEmail();
        $id = $user_in_session->getUserId();

        $request_data = file_get_contents("php://input");
        //old pass and new pass

        try{
            $data = new PasswordData($request_data);
            $data->setOwnerId($id);
            if(UserDao::userIsValid($email , sha1($data->getOldPassword()))){
                UserDao::editUserSecurity($data);
                header('HTTP/1.1 200');
                echo "Success!\n";
            }else{
                header('HTTP/1.1 401');
                echo "Wrong password! This text comes from UserController's editSecurity method\n";
            }
        }catch (\Exception $e){
            header('HTTP/1.1 500');
            echo $e->getMessage() . "\n FROM:\n" . $e->getTraceAsString();
        }
    }

    public static function getLoggedUser(){
        if (isset($_SESSION['user'])){
            /* @var $user_in_session User*/
            $user_in_session = &$_SESSION['user'];
            return $user_in_session;
        }
    }

}