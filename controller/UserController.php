<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/14/2018
 * Time: 4:12 PM
 */
namespace controller;

use model\dao\UserDao;
use model\PasswordComparison;
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
        die();
    }

    public function login(){
      /**  $email = htmlentities($_POST['email']);
           $password = htmlentities($_POST['password']);
        */

        if(isset($_SESSION["user"])){

            header('HTTP/1.1 401 Unauthorized');
            echo 'Logged user is trying to log in';
            die();
        }
        $data = file_get_contents("php://input");
        try{

            $user = new User($data);
        }catch (\RuntimeException $e){
           // header('HTTP/1.1 500');
            echo $e->getMessage() . $e->getTraceAsString();
            die();
        }
        /**  if(strlen($email) < 5 || strlen($password) < 5 || strlen($email)>45 || strlen($password)>15){
            return " Bad input found in controller!!! ";
        }
         */
        if(strlen($user->getEmail()) < 5 || strlen($user->getPassword()) < 5 ||
            strlen($user->getEmail())>45 || strlen($user->getPassword())>15) {
            return " Bad input found in controller!!! ";
        }

            try{
            $dao = new UserDao();
            if($dao->userExists($user->getEmail())){
                if($dao->userIsValid($user->getEmail() , sha1($user->getPassword()))){
                    $user =$dao->getUserData($user->getEmail());
                    $_SESSION["user"] = $user;
                    header('HTTP/1.1 200 OK');
                    die();
                }else{

                    header('HTTP/1.1 401 Unauthorized');
                    header('Content-Type: application/json; charset=UTF-8');
                    die(json_encode(array('error' => 'UserController , login method : Wrong username/password')));
                }
            }else{

                header('HTTP/1.1 401 Unauthorized');
                header('Content-Type: application/json; charset=UTF-8');
                die(json_encode(array('error' => 'UserController , login method : Wrong username/password')));
            }
        }catch (\Exception $e){

                header('HTTP/1.1 500 Something went terribly wrong . . .');
                header('Content-Type: application/json; charset=UTF-8');
                die(json_encode(array('error' => 'UserController' . $e->getMessage())));
        }

    }

    public function register(){

        if(isset($_SESSION["user"])){
            header('HTTP/1.1 401 Unauthorized');
            echo 'Logged user is trying to register';
            die();
        }

        $data = file_get_contents("php://input");

        try{
            $new_user = new User(json_encode(json_decode($data)->personal_data));
            $security_data = new PasswordComparison(json_encode(json_decode($data)->security_data));

        }catch (\RuntimeException $e){

            header('HTTP/1.1 401 Unauthorized');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(array('error' => $e->getMessage() , 'code' => $e->getCode())));
        }

        //validate data
        if (empty($new_user->getFirstName()) || empty($new_user->getLastName()) ||empty($new_user->getEmail()) ||
            empty($new_user->getGender()) || empty($new_user->getPassword())  || empty($security_data->getPassword()) ||
            empty($security_data->getPasswordRepeat()) || strlen($new_user->getFirstName()) > 45 ||
            strlen($new_user->getLastName()) > 45 || strlen($new_user->getEmail()) > 45 || strlen($new_user->getPassword()) > 45 ||
            strlen($security_data->getPassword()) > 45 || strlen($security_data->getPasswordRepeat()) > 45 ||
            strpos($new_user->getEmail() , "@") === false || $security_data->getPassword() !== $new_user->getPassword() ||
            $security_data->getPassword() !== $security_data->getPasswordRepeat() ||
            strlen($security_data->getPassword()) < 5 || strlen($security_data->getPasswordRepeat()) < 5 ){

            // Did not pass the validation 400 (Bad Request)
            header('HTTP/1.1 400 Bad Request');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(array('message' => 'Wrong input data!Nice try,though .. ;)')));

        } else {
            try {
                $dao = new UserDao();
                $user_exists = $dao->userExists($new_user->getEmail());
                if (!$user_exists) {
                    $new_user->setPassword($security_data->getPassword());
                    $dao->register($new_user);

                    // Everything went okay
                    header('Content-Type: application/json');
                    echo json_encode('Success!User is registered in DB!');

                } else {
                    //email is taken
                     header('HTTP/1.1 401 Unauthorized');
                    header('Content-Type: application/json; charset=UTF-8');
                    die(json_encode(array('error' => 'Email is already taken')));
                }
            } catch (\PDOException $e) {
                // problem in DB
                header('HTTP/1.1 500 Internal Server Booboo');
                header('Content-Type: application/json; charset=UTF-8');
                die(json_encode(array('error' => $e->getMessage() , 'code' => $e->getCode())));

            }catch (\RuntimeException $e){

                header('HTTP/1.1 401 Unauthorized');
                header('Content-Type: application/json; charset=UTF-8');
                die(json_encode(array('error' => $e->getMessage() , 'code' => $e->getCode())));
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
        $data = new PasswordComparison($request_data);
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