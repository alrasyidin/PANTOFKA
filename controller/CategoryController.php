<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/15/2018
 * Time: 11:51 AM
 */

namespace controller;

use model\dao\ProductsDao;
use model\Product;


class CategoryController extends AbstractController
{

    private static $instance;

    /**
     * CategoryController constructor.
     */
    private function __construct(){

    }

    public static function getInstance(){
        if(self::$instance === null){
            self::$instance = new CategoryController();
        }
        return self::$instance;
    }

    public function getStylesByParentCategory()
    {
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            if ($_GET["pc"] === "none") {
                echo json_encode(array());
            }
            else {
                try {
                    $dao = new ProductsDao();
                    $styles = $dao->getStylesByParentCategory(htmlentities($_GET["pc"]));
                    echo json_encode($styles);
                } catch (\PDOException $e) {
                    header("location: index.php?page=error");
                    die();
                }
            }
        }

    }

    public static function getCategories()
    {
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            try {

                $dao = new ProductsDao();
                $categories = $dao->getCategories();
                echo json_encode($categories);
            } catch (\PDOException $e) {
                header("location: index.php?page=error");
                die();

            }
        }

    }

    public static function getColors()
    {
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            try {

                $dao = new ProductsDao();
                $colors = $dao->getColors();
                echo json_encode($colors);
            } catch (\PDOException $e) {
                header("location: index.php?page=error");
                die();

            }
        }

    }

    public static function getMaterials()
    {
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            try {

                $dao = new ProductsDao();
                $materials = $dao->getMaterials();
                echo json_encode($materials);
            } catch (\PDOException $e) {
                echo "error in Get materials";
            }
        }

    }


}