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


    public function saveNewProduct(Product $product)
    {

        // Getting ids for details of the product
        $color_id = $this->getColorId($product->getColor());

        $material_id = $this->getMaterialId($product->getMaterial());


//            self::$pdo->beginTransaction();

        $category_id = $this->getCategoryAndStyleId($product->getStyle(), $product->getCategory());

        $stmt = self::$pdo->prepare(
            "INSERT INTO final_project_pantofka.products (product_name, price, info, promo_percentage,
                        product_image_url, color_id, material_id, category_id) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute(array(
            $product->getProductName(),
            $product->getPrice(),
            $product->getInfo(),
            $product->getPromoPercentage(),
            $product->getProductImgUrl(),
            $color_id,
            $material_id,
            $category_id,
        ));


        // We have to get the last insert product_id because we need to save sizes for the product
        $product_id = self::$pdo->lastInsertId("product_id");

        $sizes = $product->getSizes();

        //foreach size of the new product we need to make insert into DB
        /* @var $size Size */
        foreach ($sizes as $size) {
            // getting the size_id from DB
            $stmt = self::$pdo->prepare(
                "SELECT size_id
                               FROM final_project_pantofka.sizes
                               WHERE  size_number = ? ");
            $stmt->execute(array($size->getSizeNumber()));
            $size_id = $stmt->fetch(\PDO::FETCH_ASSOC);
            $size_id = $size_id["size_id"];

            //getting the quantity of this size
            $quantity = $size->getSizeQuantity();


            //insert into tabel - products_has_sizes product_id, size_id and the quantity
            $stmt = self::$pdo->prepare(
                "INSERT INTO final_project_pantofka.products_has_sizes (product_id, size_id, quantity) 
                               VALUES (?, ?, ?)");
            $stmt->execute(array($product_id, $size_id, $quantity));

//        self::$pdo->commit();
//    }catch (\PDOexeption $e)
//{
//self::$pdo->e->rollback();
//throw $e;
        }

    }


    public function changeProduct(Product $product)
    {

        // Getting ids for details of the product
        $color_id = $this->getColorId($product->getColor());

        $material_id = $this->getMaterialId($product->getMaterial());

        $product_id = $product->getProductId();


//            self::$pdo->beginTransaction();
        $stmt = self::$pdo->prepare(
            "UPDATE final_project_pantofka.products 
                  SET product_name = ?, price = ?, info = ?, promo_percentage = ?,
                  product_image_url = ?, color_id = ?, material_id = ? 
                  WHERE product_id = ?");
        $stmt->execute(array(
            $product->getProductName(),
            $product->getPrice(),
            $product->getInfo(),
            $product->getPromoPercentage(),
            $product->getProductImgUrl(),
            $color_id,
            $material_id,
            $product_id));


//    $sizes = $product->getSizes();

        //foreach size of the new product we need to make insert into DB
        /* @var $size Size */
//    foreach ($sizes as $size) {
        // getting the size_id from DB
//        $stmt = self::$pdo->prepare(
//            "SELECT size_id
//                               FROM final_project_pantofka.sizes
//                               WHERE  size_number = ? ");
//        $stmt->execute(array($size->getSizeNumber()));
//        $size_id = $stmt->fetch(\PDO::FETCH_ASSOC);
//        $size_id = $size_id["size_id"];

        //getting the quantity of this size
//        $quantity = $size->getSizeQuantity();


        //update table - products_has_sizes - quantity for each size of the product
//        $stmt = self::$pdo->prepare(
//            "UPDATE final_project_pantofka.products_has_sizes
//                        SET quantity = ?
//                        WHERE product_id = ?
//                        AND size_id = ?");
//        $stmt->execute(array($quantity, $product_id, $size_id));

//        self::$pdo->commit();
//    }catch (\PDOexeption $e)
//{
//self::$pdo->e->rollback();
//throw $e;
//    }

    }

    public function productExistsId($id)
    {
        $query = self::$pdo->prepare(
            "SELECT count(*) as product_exists FROM final_project_pantofka.products
                      WHERE product_id = ? ");
        $query->execute(array($id));
        $count = $query->fetch(\PDO::FETCH_ASSOC);
        return boolval($count["product_exists"]);
    }

    public function productExists($product_name, $material, $category, $color)
    {

        // Getting ids for details of the product
        $color_id = $this->getColorId($color);

        $material_id = $this->getMaterialId($material);

        $category_id = $this->getCategoryId($category);


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

    public function getCategoryAndStyleId($style, $category)
    {
        $parent_id = $this->getCategoryId($category);
        $stmt = self::$pdo->prepare(
            "SELECT category_id
                       FROM final_project_pantofka.categories
                       WHERE  name = ? AND parent_id = ?");
        $stmt->execute(array($style, $parent_id));
        $category_id = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $category_id["category_id"];

    }

    public function getColorId($color)
    {

        $stmt = self::$pdo->prepare(
            "SELECT color_id
                       FROM final_project_pantofka.colors
                       WHERE  color = ? ");
        $stmt->execute(array($color));
        $color_id = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $color_id["color_id"];


    }

    public
    function getMaterialId($material)
    {

        $stmt = self::$pdo->prepare(
            "SELECT material_id
                       FROM final_project_pantofka.materials
                       WHERE  material = ? ");
        $stmt->execute(array($material));
        $material_id = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $material_id["material_id"];


    }

    public
    function getCategories()
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


    public
    function getCategoryId($category)
    {

        $stmt = self::$pdo->prepare(
            "SELECT category_id
                       FROM final_project_pantofka.categories
                       WHERE  name = ? ");
        $stmt->execute(array($category));
        $category_id = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $category_id["category_id"];


    }

    public
    function getProducts($pages, $entries, $category)
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
                      JOIN categories as cat ON st.parent_id = cat.category_id";

            if ($category == "new") {
                $sql .= "  WHERE p.promo_percentage = 0 ORDER BY p.product_id DESC LIMIT 10 ";

            } elseif($category != "new") {

                if ($category != "sale" && $category != "all") { // 0 because only products which are not on sale can be shown as new
                    $params[] = $category;
                    $sql .= " WHERE cat.name = ?";
                } elseif ($category == "sale") {
                    $sql .= " WHERE p.promo_percentage > 0 "; // 0 because every product which promo percentage is more then 0 is on SALE

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


    public
    function getProductsCount($category)
    {

        $params = array();
        $sql = "SELECT count(*) as number_of_products FROM products as p
                JOIN categories as cat 
                ON p.category_id = cat.category_id
                JOIN categories as c 
                ON cat.parent_id = c.category_id";
        if ($category != "all") {
            $params[] = $category;
            $sql .= "  WHERE c.name = ?";
        }

        $stmt = self::$pdo->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row["number_of_products"];

    }


    public
    function getProductById($product_id)
    {


        $stmt = self::$pdo->prepare(
            "SELECT p.product_id, p.product_name, p.price, p.info, p.product_image_url, p.promo_percentage,
                      c.color,  m.material 
                      FROM final_project_pantofka.products as p
                      JOIN colors as c ON p.color_id = c.color_id
                      JOIN materials as m ON p.material_id = m.material_id
                      JOIN categories as cat ON p.category_id = cat.category_id 
                      WHERE p.product_id = ?");

        $stmt->execute(array($product_id));
        $product = $stmt->fetch(\PDO::FETCH_ASSOC);
        $product = new Product(json_encode($product));
        return $product;

    }


    public
    function getAllProductsByCategory($category)
    {
        $stmt = self::$pdo->prepare(
            "SELECT p.product_id, p.product_name, p.price, p.info, p.product_image_url, p.promo_percentage,
                      c.color,  m.material 
                      FROM final_project_pantofka.products as p
                      JOIN colors as c ON p.color_id = c.color_id
                      JOIN materials as m ON p.material_id = m.material_id
                      JOIN categories as cat ON p.category_id = cat.category_id 
                      WHERE cat.name = ?");
        $stmt->execute(array($category));
        $products = [];
        while ($product = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $products[] = new Product(json_encode($product));
        }
        return $products;
    }

    public
    function getStylesByParentCategory($parent_category)
    {

        $parent_id = $this->getCategoryId($parent_category);
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

    public
    function getColors()
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

    public
    function getMaterials()
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


}