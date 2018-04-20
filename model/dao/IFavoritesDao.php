<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/15/2018
 * Time: 10:34 AM
 */

namespace model\dao;



interface IFavoritesDao{

    public function addToFavorites();

    public function removeFromFavorites();

}