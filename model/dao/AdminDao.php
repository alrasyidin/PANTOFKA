<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/14/2018
 * Time: 3:27 PM
 */

namespace model\dao;
use model\Product;
use model\Size;


class AdminDao extends CustomerDao implements IAdminDao {

    /**
     * This method receives a product id and set quantity 0 for each of its sizes.
     * @param $product_id
     */
    public static function unsetProduct($product_id){
        $stmt = self::$pdo->prepare("UPDATE final_project_pantofka.products_has_sizes 
                                            SET quantity = 0
                                            WHERE product_id = ?");
        $stmt->execute(array($product_id));

    }

    /*
 * This method receive a new Product object from the controller and save its data in DB.
 *
 * @param Product $product
 */
    public static function saveNewProduct(Product $product)
    {

        // Getting ids for details of the product
        $color_id = ProductsDao::getColorId($product->getColor());

        $material_id = ProductsDao::getMaterialId($product->getMaterial());



        $category_id = ProductsDao::getCategoryAndStyleId($product->getStyle(), $product->getCategory());
        try{
            self::$pdo->beginTransaction();

            $stmt = self::$pdo->prepare(
                "INSERT INTO final_project_pantofka.products (product_name, price, info, promo_percentage,
                        product_image_url, color_id, material_id, category_id) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute(array(
                $product->getProductName(),
                $product->getPrice(),
                $product->getInfo(),
                $product->getPromoPercentage(),
                $product->getProductImageUrl(),
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
            }

            self::$pdo->commit();
        }catch (\PDOException$e)
        {
            self::$pdo->rollback();
            throw $e;
        }

    }

    /**
     *This method receive a change data in Product object from the controller and change its data in DB.
     *
     * @param Product $product
     */
    public static function changeProduct(Product $product)
    {

        // Getting ids for details of the product
        $color_id = ProductsDao::getColorId($product->getColor());

        $material_id = ProductsDao::getMaterialId($product->getMaterial());

        $product_id = $product->getProductId();

        try{
            self::$pdo->beginTransaction();
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
                $product->getProductImageUrl(),
                $color_id,
                $material_id,
                $product_id));


            $sizes = $product->getSizes();
            var_dump($sizes);

            //foreach size of the new product we need to make insert into DB
            /* @var $size Size */
            foreach ($sizes as $size) {
                // getting the size_id from DB
                $number = $size->getSizeNumber();
                $stmt = self::$pdo->prepare(
                    "SELECT size_id
                               FROM final_project_pantofka.sizes
                               WHERE  size_number = ? ");
                $stmt->execute(array($number));
                $size_id = $stmt->fetch(\PDO::FETCH_ASSOC);
                $size_id = $size_id["size_id"];

                //getting the quantity of this size
                $quantity = $size->getSizeQuantity();


                //update table - products_has_sizes - quantity for each size of the product
                $stmt = self::$pdo->prepare(
                    "UPDATE final_project_pantofka.products_has_sizes
                        SET quantity = ?
                        WHERE product_id = ?
                        AND size_id = ?");
                $stmt->execute(array($quantity, $product_id, $size_id));
            }
            self::$pdo->commit();
        }catch (\PDOException$e)
        {
            self::$pdo->rollback();
            throw $e;
        }

    }

}