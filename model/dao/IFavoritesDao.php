<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/15/2018
 * Time: 10:34 AM
 */

namespace model\dao;



interface IFavoritesDao{

    public static function addToFavorites($product_id , $user_id);

    public static function productIsAlreadyInFavorites($product_id , $user_id);

    public static function removeFromFavorites($product_id , $user_id);

    public static function productIsAvailable($product_id , $size_id);

    public static function getFavorites($user_id);

    public static function deleteFavorites($user_id);

}