<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/19/2018
 * Time: 9:19 PM
 */

namespace model\dao;

use model\Product;
use model\Size;

class ProductsDao extends AbstractDao implements IProductsDao
{

    public function __construct()
    {
        parent::init();
    }

    /**
     *This method receive id of product and checks if this exists in DB.
     *
     * @param $id
     * @return bool
     */
    public static function productExistsId($id)
    {
        $query = self::$pdo->prepare(
            "SELECT count(*) as product_exists FROM final_project_pantofka.products
                      WHERE product_id = ? ");
        $query->execute(array($id));
        $count = $query->fetch(\PDO::FETCH_ASSOC);
        return boolval($count["product_exists"]);
    }

    /**
     * This method receives details which makes one product unique and checks in DB if product with this details is
     *already saved.
     *
     * @param $product_name
     * @param $material
     * @param $category
     * @param $color
     * @return bool
     */
    public static function productExists($product_name, $material, $category, $color)
    {

        // Getting ids for details of the product
        $color_id = ProductsDao::getColorId($color);

        $material_id = ProductsDao::getMaterialId($material);

        $category_id = ProductsDao::getCategoryId($category);


        //  Checking if product with this name and details already exists
        $query = self::$pdo->prepare(
            "SELECT count(*) as product_exists FROM final_project_pantofka.products as p
                JOIN categories as c ON p.category_id = c.category_id
                      WHERE p.product_name = ?
                       AND c.parent_id = ?
                       AND p.color_id = ?
                       AND p.material_id = ?");
        $query->execute(array($product_name, $category_id, $color_id, $material_id));
        $count = $query->fetch(\PDO::FETCH_ASSOC);
        return boolval($count["product_exists"]);
    }

    /**
     * This method receives am style and category of the product and returns the id for the style of the chosen
     * category. Some styles can exists in more than one category!
     *
     * @param $style
     * @param $category
     * @return mixed
     */
    public static function getCategoryAndStyleId($style, $category)
    {
        $parent_id = ProductsDao::getCategoryId($category);
        $stmt = self::$pdo->prepare(
            "SELECT category_id
                       FROM final_project_pantofka.categories
                       WHERE  name = ? AND parent_id = ?");
        $stmt->execute(array($style, $parent_id));
        $category_id = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $category_id["category_id"];

    }

    /**
     * This method receives a color and return its id in the DB.
     * @param $color
     * @return mixed
     */
    public static function getColorId($color)
    {

        $stmt = self::$pdo->prepare(
            "SELECT color_id
                       FROM final_project_pantofka.colors
                       WHERE  color = ? ");
        $stmt->execute(array($color));
        $color_id = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $color_id["color_id"];


    }


    /**
     *This method receives a material and return its id in the DB.
     * @param $material
     * @return mixed
     */
    public static function getMaterialId($material)
    {

        $stmt = self::$pdo->prepare(
            "SELECT material_id
                       FROM final_project_pantofka.materials
                       WHERE  material = ? ");
        $stmt->execute(array($material));
        $material_id = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $material_id["material_id"];


    }

    /**
     * This method returns all parent categories from DB.
     * @return array
     */
    public static  function getCategories()
    {
        $stmt = self::$pdo->prepare("SELECT DISTINCT cat.name as category 
                                  FROM final_project_pantofka.categories as cat
                                  WHERE parent_id IS NULL  ");
        $stmt->execute(array());
        $category = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $category[] = $row["category"];
        }
        return $category;
    }

    /**
     * This method receive category and returns its id from DB.
     * @param $category
     * @return mixed
     */
    public static  function getCategoryId($category)
    {

        $stmt = self::$pdo->prepare(
            "SELECT category_id
                       FROM final_project_pantofka.categories
                       WHERE  name = ? ");
        $stmt->execute(array($category));
        $category_id = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $category_id["category_id"];


    }

    /**
     *This method returns an array of product objects with  products details depends of the entered quantity as entries
     * from the page entered using LIMIT and OFFSET
     * @param $pages
     * @param $entries
     * @param $category
     * @return array
     */
    public static function getProducts($pages, $entries, $category, $style, $color, $material)
    {
        try {
            $offset = intval(($pages - 1) * $entries);
            $limit = intval($entries);
            $products = [];
            $params = [];
            $sql = "SELECT p.product_id, p.product_name, p.price, p.info, p.product_image_url, p.promo_percentage,
                      c.color,  m.material, st.name as style, cat.name as category
                      FROM final_project_pantofka.products as p
                      JOIN colors as c ON p.color_id = c.color_id
                      JOIN materials as m ON p.material_id = m.material_id
                      JOIN categories as st ON p.category_id = st.category_id
                      JOIN categories as cat ON st.parent_id = cat.category_id
                      WHERE p.product_id > 0 ";

            if ($category == "new") {
                $sql .= "  AND p.promo_percentage = 0 ORDER BY p.product_id DESC LIMIT 10 ";

            } elseif($category != "new") {

                if ($category != "sale" && $category != "all") { // 0 because only products which are not on sale can be shown as new
                    $params[] = $category;
                    $sql .= " AND cat.name = ?";
                    if($style != "all"){
                        $params[] = $style;
                        $sql .= " AND st.name = ?";
                    }
                    if($color != "all"){
                        $params[] = $color;
                        $sql .= " AND c.color = ?";
                    }
                    if($material != "all"){
                        $params[] = $material;
                        $sql .= " AND m.material = ?";
                    }
                } elseif ($category == "sale") {
                    $sql .= " AND p.promo_percentage > 0 "; // 0 because every product which promo percentage is more then 0 is on SALE
                    if($style != "all"){
                        $params[] = $style;
                        $sql .= " AND st.name = ?";
                    }
                    if($color != "all"){
                        $params[] = $color;
                        $sql .= " AND c.color = ?";
                    }
                    if($material != "all"){
                        $params[] = $material;
                        $sql .= " AND m.material = ?";
                    }
                }
                $sql .= " LIMIT $limit OFFSET $offset";
            }

            $stmt = self::$pdo->prepare($sql);

            $stmt->execute($params);
            While ($query_result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $product = json_encode($query_result);
                $products[] = new Product($product);
            }

            return $products;

        } catch (\PDOException $e) {
            throw $e;
        }
    }


    /**
     * This method receives a category and returns the number of the products for this category.
     * @param $category
     * @return mixed
     */
    public static  function getProductsCount($category, $style, $color, $material)
    {

        $params = array();
        $sql = "SELECT count(*) as number_of_products 
                FROM final_project_pantofka.products as p
                      JOIN colors as c ON p.color_id = c.color_id
                      JOIN materials as m ON p.material_id = m.material_id
                      JOIN categories as st ON p.category_id = st.category_id
                      JOIN categories as cat ON st.parent_id = cat.category_id
                      WHERE p.product_id > 0";
        if ($category != "all" && $category != "new" && $category != "sale") {
            $params[] = $category;
            $sql .= "  AND cat.name = ?";

            if($style != "all"){
                $params[] = $style;
                $sql .= " AND st.name = ?";
            }
            if($color != "all"){
                $params[] = $color;
                $sql .= " AND c.color = ?";
            }
            if($material != "all"){
                $params[] = $material;
                $sql .= " AND m.material = ?";
            }
        }
        elseif ($category == "sale"){
            $sql .= " AND p.promo_percentage > 0";

            if($style != "all"){
                $params[] = $style;
                $sql .= " AND st.name = ?";
            }
            if($color != "all"){
                $params[] = $color;
                $sql .= " AND c.color = ?";
            }
            if($material != "all"){
                $params[] = $material;
                $sql .= " AND m.material = ?";
            }
        }

        $stmt = self::$pdo->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row["number_of_products"];

    }


    /**
     * This method receives product id and returns a Products object with this id from DB.
     * @param $product_id
     * @return mixed|Product
     */
    public static function getProductById($product_id)
    {


        $stmt = self::$pdo->prepare(
            "SELECT p.product_id, p.product_name, p.price, p.info, p.product_image_url, p.promo_percentage,
                      c.color,  m.material, cat.name as style, parent.name as category
                      FROM final_project_pantofka.products as p
                      JOIN colors as c ON p.color_id = c.color_id
                      JOIN materials as m ON p.material_id = m.material_id
                      JOIN categories as cat ON p.category_id = cat.category_id 
                      JOIN categories as parent ON cat.parent_id = parent.category_id 

                      WHERE p.product_id = ?");

        $stmt->execute(array($product_id));
        $product = $stmt->fetch(\PDO::FETCH_ASSOC);
        $product = new Product(json_encode($product));
        return $product;

    }

    /**
     * This method receives parent category and returns in array all the styles for this category from DB
     * @param $parent_category
     * @return array
     */
    public  static function getStylesByParentCategory($parent_category)
    {

        $parent_id = ProductsDao::getCategoryId($parent_category);
        $styles = [];

        $stmt = self::$pdo->prepare(
            "SELECT s.name as style
                      FROM categories as s
                      JOIN categories as c on s.parent_id = c.category_id
                      WHERE s.parent_id = ? ");
        $stmt->execute(array($parent_id));
        While ($style = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $styles[] = $style["style"];
        }
        return $styles;
    }

    /**
     * This method returns an array of all different colors from DB.
     * @return array
     */
    public static function getColors()
    {

        $stmt = self::$pdo->prepare(
            "SELECT DISTINCT color
                      FROM colors");
        $stmt->execute();
        $colors = [];
        While ($color = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $colors[] = $color["color"];
        }
        return $colors;
    }

    /**
     * This method returns an array of all different materials from the DB.
     * @return array
     */
    public static function getMaterials()
    {

        $stmt = self::$pdo->prepare(
            "SELECT DISTINCT material
                      FROM materials ");
        $stmt->execute();
        $materials = [];
        While ($material = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $materials[] = $material["material"];
        }
        return $materials;
    }

    /**
     * This method receives product_id and reurns if the product has sizes.
     * @param $product_id
     * @return bool
     */
    public static function productIsAvailable($product_id){
        $stmt = self::$pdo->prepare("SELECT count(*) as is_available FROM final_project_pantofka.products_has_sizes 
                                            WHERE (product_id = ? AND quantity > 0)");
        $stmt->execute(array($product_id));
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return boolval($result["is_available"]);
    }


}