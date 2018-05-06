<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 21.4.2018 г.
 * Time: 6:56
 */

namespace model\dao;
use model\Rating;
use model\Size;

class RatingDao extends AbstractDao
{
    public function __construct() {
        parent::init();
    }

    public static function giveRating(Rating $rating){

        $stmt = self::$pdo->prepare(
            "INSERT INTO final_project_pantofka.ratings (product_id, user_id, rating_value) 
                       VALUES (?, ?, ?)");
        $stmt->execute(array(
            $rating->getProductId(),
            $rating->getUserId(),
            $rating->getRatingValue()

        ));
    }

    public static function changeRating(Rating $rating){
        $stmt = self::$pdo->prepare(
            "UPDATE final_project_pantofka.ratings SET rating_value = ? 
                      WHERE product_id =? AND  user_id = ?");
        $stmt->execute(array(
            $rating->getRatingValue(),
               $rating->getProductId(),
            $rating->getUserId()

        ));
    }

        public function getRatings($product_id){

        $stmt = self::$pdo->prepare(
            "SELECT  user_id, rating_value FROM final_project_pantofka.ratings
                      WHERE product_id = ?");
        $stmt->execute(array($product_id));
        $ratings = [];
        While ($query_result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $rating = new Rating(json_encode($query_result));
            $ratings[]=$rating;
        }
        return $ratings;
    }

    public static function getRatingOfUser($user_id, $product_id){
        $stmt = self::$pdo->prepare(
            "SELECT user_id, product_id, rating_value FROM final_project_pantofka.ratings
                      WHERE user_id = ? AND product_id = ? ");
        $stmt->execute(array($user_id, $product_id));

        $query_result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $has_rate = boolval($query_result);
        if ($has_rate) {
            $rating = new Rating(json_encode($query_result));
            return $rating;
        }
        else {
            return false;
        }
    }
}