<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 22.4.2018 г.
 * Time: 8:50
 */

namespace model\dao;

use model\Size;

interface ISizeDao
{
    public function saveSize($product, Size $size);
    public function getSizeId($size);
    public function getSizesAndQuantities($product_id);

}