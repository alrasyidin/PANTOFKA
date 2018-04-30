<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/14/2018
 * Time: 6:04 PM
 */

namespace controller;


use model\dao\AdminDao;
use model\dao\ProductsDao;
use model\Product;

class AdminController extends AbstractController {

    private static $instance;

    /**
     * AdminController constructor.
     */
    private function __construct(){

    }

    public static function getInstance(){
        if(self::$instance === null){
            self::$instance = new AdminController();
        }
        return self::$instance;
    }

    public function addProduct(Product $product){

    }

    public function editProduct(Product $product){

    }

    public function unsetProduct(){
        if (isset($_GET['id'])){
            $product_id = htmlentities($_GET['id']);
            if ($product_id < 1 || !is_numeric($product_id)){
                die('Bad data passed to the controller');
            }
            try{
                if (AdminDao::productIsAvailable($product_id)){
                    AdminDao::unsetProduct($product_id);
                    echo 'The product size quantities were set to zero';
                }else{
                    echo 'There is nothing else to remove from here';
                }

            }catch (\PDOException $e){
                die($e->getMessage());
            }

        }
    }
}