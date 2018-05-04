<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/15/2018
 * Time: 12:02 AM
 */

namespace model\dao;


use model\Product;

class FavoritesDao extends AbstractDao implements IFavoritesDao {

    public  function __construct() {
        parent::init();
    }


    public static function addToFavorites($product_id , $user_id){
        $query = self::$pdo->prepare(
            "INSERT INTO final_project_pantofka.users_has_favorites ( product_id , user_id) VALUES (? , ?);");
        $query->execute(array($product_id , $user_id));
        $query = self::$pdo->prepare(
            "SELECT p.product_id, p.product_name, p.price, p.info, p.product_image_url, p.promo_percentage,
                      c.color,  m.material 
                      FROM final_project_pantofka.products as p
                      JOIN colors as c ON p.color_id = c.color_id
                      JOIN materials as m ON p.material_id = m.material_id
                      JOIN categories as cat ON p.category_id = cat.category_id 
                      WHERE p.product_id = ?");
        $query->execute(array($product_id));
        $favorite_item = $query->fetch(\PDO::FETCH_ASSOC);
        return new Product(json_encode($favorite_item));
    }

    public static function productIsAlreadyInFavorites($product_id , $user_id){
        $query = self::$pdo->prepare(
            "SELECT count(*) as found_match FROM final_project_pantofka.users_has_favorites 
                        WHERE product_id = ? AND user_id = ?");
        $query->execute(array($product_id , $user_id));
        $r = $query->fetch(\PDO::FETCH_ASSOC);
        return boolval($r['found_match']);
    }

    public static function removeFromFavorites($product_id , $user_id){
        $query = self::$pdo->prepare(
            "DELETE FROM users_has_favorites WHERE product_id=? AND user_id = ?");
        $query->execute(array($product_id , $user_id));
    }

    public static function productIsAvailable($product_id , $size_id){
        $query = self::$pdo->prepare(
            "SELECT count(*) as products FROM final_project_pantofka.products_has_sizes
                      WHERE product_id = ? AND size_id = ? AND quantity > 0");
        $query->execute(array($product_id , $size_id));
        $count = $query->fetch(\PDO::FETCH_ASSOC);
        $productIsAvailable = boolval($count["products"]);
        if ($productIsAvailable){
            $get_product_stmt = self::$pdo->prepare(
                "SELECT p.product_id, p.product_name, p.price, p.info, p.product_image_url, p.promo_percentage,
                      c.color,  m.material 
                      FROM final_project_pantofka.products as p
                      JOIN colors as c ON p.color_id = c.color_id
                      JOIN materials as m ON p.material_id = m.material_id
                      JOIN categories as cat ON p.category_id = cat.category_id 
                      WHERE p.product_id = ?");
            $get_product_stmt->execute(array($product_id));
            $product = $get_product_stmt->fetch(\PDO::FETCH_ASSOC);
            return $product;
        }
    }

    public static function getFavorites($user_id){
        $query = self::$pdo->prepare(
            "SELECT p.product_id , p.product_name , p.price , p.info ,  p.product_image_url , 
                            p.promo_percentage , p.color_id , p.category_id , p.material_id , clr.color,
                            m.material , ctg.name as category
                            FROM final_project_pantofka.users_has_favorites as uhf
                            JOIN final_project_pantofka.products as p USING (product_id) 
                            JOIN final_project_pantofka.materials as m USING (material_id)
                            JOIN final_project_pantofka.colors as clr USING (color_id)
                            JOIN final_project_pantofka.categories as ctg USING (category_id) 
                            WHERE uhf.user_id = ?");
        $query->execute(array($user_id));
        $favorites = array();
        while ($product = $query->fetch(\PDO::FETCH_ASSOC)){
            $favorites[] = new Product(json_encode($product));
        }
        return $favorites;

    }

    public static function deleteFavorites($user_id){
        $query = self::$pdo->prepare(
            "DELETE FROM users_has_favorites WHERE user_id = ?");
        $query->execute(array($user_id));
    }
}