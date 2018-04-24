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

    public function logout(){
        session_destroy();
        header("location: index.php?page=main");
        die();

    }

    public static function login()
    {
        if (isset($_SESSION["user"])) {
            // header('HTTP/1.1 401 Unauthorized');
            echo 'Logged user is trying to log in';
            die();
        }
        $logging_user_data = array();
        $logging_user_data['email'] = htmlentities($_POST['email']);
        $logging_user_data['password'] = htmlentities($_POST['password']);

        try {
            $user = new User(json_encode($logging_user_data));
        } catch (\RuntimeException $e) {
            echo "'error' => $e->getMessage() , 'code' => $e->getCode()))";
        }

        try {
            $user_dao = new UserDao();
            if ($user_dao->login($user) !== null) {
                // This removes password from user object. It has validation inside.
                // Throws an RuntimeException if the passed password is not the users one
                $user->__unset($user->getPassword());
                $_SESSION["user"] = $user;
                //  header('HTTP/1.1 200 OK');
                echo 'success';
            } else {
                //header('HTTP/1.1 401 Unauthorized');
                echo 'UserController , login method : Wrong username/password';
            }
        } catch (\PDOException $e) {
           // header('HTTP/1.1 500 Something went terribly wrong . . .');
            echo "Problem in DB with login: " . $e->getMessage() ."\n". $e->getCode() ."\n".$e->getTraceAsString();
        } catch (\RuntimeException $e){
            echo "Problem with validation in User class: " . $e->getMessage() ."\n".$e->getTraceAsString();
        }
    }

    public static function register(){

        if(isset($_SESSION["user"])){
            header('HTTP/1.1 401 Unauthorized');
            echo 'Logged user is trying to register';
            die();
        }

        $user_personal_data = array();
        $user_personal_data['first_name'] = htmlentities($_POST['first_name']);
        $user_personal_data['last_name'] = htmlentities($_POST['last_name']);
        $user_personal_data['email'] = htmlentities($_POST['email']);
        $user_personal_data['gender'] = htmlentities($_POST['gender']);
        $user_personal_data['password'] = htmlentities($_POST['password']);

        $user_security_data = array();
        $user_security_data['password'] = htmlentities($_POST['password']);
        $user_security_data['password_repeat'] = htmlentities($_POST['password_repeat']);

        try{
            // Create object with password data. Constructor throws exception if data is bad!
            $users_passwords = new PasswordData(json_encode($user_security_data));
            if($users_passwords->getPassword() !== $users_passwords->getPasswordRepeat()){
                echo "Passwords mismatched!";
                die();
            }
            // Data is validated in user's set-ers. They throw Runtime exceptions when data is bad.
            $new_user = new User(json_encode($user_personal_data));
        }catch (\RuntimeException $e){
            // Did not pass the validation 400 (Bad Request)
            // header('HTTP/1.1 400 Bad Request');
            echo "'error' => $e->getMessage() , 'code' => $e->getCode()))";
            die();
        }

        try {
            $dao = new UserDao();
            $user_exists = $dao->userExists($new_user->getEmail());
                if (!$user_exists) {
                    $dao->register($new_user);
                    // Everything went okay
                    echo 'Success!User is registered in DB!';
                    //header('Location: index.php?page=login');
                } else {
                    //email is taken
                    // header('HTTP/1.1 401 Unauthorized');
                    echo 'Email is already taken';
                }
            } catch (\PDOException $e) {
                // problem in DB
                //header('HTTP/1.1 500 Internal Server Booboo');
                echo $e->getMessage() ."\n". $e->getCode() ."\n".$e->getTraceAsString();
            }
        }


    public function getLoggedUser(){
        $user = $_SESSION["user"];
        echo json_encode($user);
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
                $user_in_session = new User(json_encode($_SESSION['user']));
                $request_data = file_get_contents("php://input");
                $user = new User($request_data);
                if(UserDao::emailExists($user->getEmail()) && $user->getEmail() !== $user_in_session->getEmail()){
                   echo "There is another user with that email";
                }
                UserDao::editUser($user);
                unset($_SESSION['user']);
                $_SESSION['user'] = $user ;
                header('HTTP/1.1 200 OK');
                echo "Changes were saved";
            }
            catch (Exception $e){
                echo $e->getMessage() . "\n Found in:\n" . $e->getTraceAsString();
            }
        }

    private static function editSecurity(){
        $user_in_session = new User(json_encode($_SESSION['user']));

        $email = $user_in_session->getEmail();
        $id = $user_in_session->getId();

        $request_data = file_get_contents("php://input");
        //old pass and new pass

        try{
            $data = new PasswordData($request_data);
            $data->setOwnerId($id);
            UserDao::init();
            if(UserDao::userIsValid($email , sha1($data->getOldPassword()))){
                UserDao::editUserSecurity($data);
                echo "Success!\n";
                var_dump($data);
                die();
            }else{
                echo "Wrong password! This text comes from UserController's editSecurity method\n";
                var_dump($data);

                die();
            }
        }catch (\Exception $e){
           echo $e->getMessage() . "\nFROM:\n" . $e->getTraceAsString();
        }
    }

}