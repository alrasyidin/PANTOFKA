<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/18/2018
 * Time: 11:10 AM
 */

namespace controller;


class BaseController extends AbstractController {

    private static $instance;

    /**
     * BaseController constructor.
     */
    private function __construct(){

    }

    public static function getInstance(){
        if(self::$instance === null){
            self::$instance = new BaseController();
        }
        return self::$instance;
    }

    public static function index(){
        if(isset($_GET['page'])){
            $page_name = $_GET['page'];
            if ($page_name === 'login' || $page_name === 'register') {
                if (isset($_SESSION['user'])) {
                    // header('HTTP/1.1 401 Unauthorized');
                    $page_name = 'error';
                    echo 'Logged user is trying to register or log in';
                    //die();
                }
            }

            if ($page_name !== 'login' && $page_name !== 'register' && $page_name !== 'cart' && $page_name !== 'favorites' && $page_name !== 'main' && $page_name !== 'show_products' && $page_name !== null) {
                //if (!isset($_SESSION['user'])) {
                //  header('HTTP/1.1 401 Unauthorized');
                //  die('Guest user is trying to access private data in ' . $page_name);
                // }
            }
            $page_path = __DIR__ . "\\..\\view\\" . $page_name . ".html";

            if (!empty($page_name)) {
                if (file_exists($page_path)) {
                    require_once $page_path;
                } else {
                    echo "File " . $page_path . "do not exists";
                }
            }
        }else{
            echo "Here should be an default page. This comes from BaseController's index method";
        }
    }

}