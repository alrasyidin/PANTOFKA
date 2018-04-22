<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 21.4.2018 г.
 * Time: 6:39
 */

namespace model;


use model\dao\SizeDao;

class Size extends AbstractModel
{
    private  $id;
    private  $size_number;
    private  $size_quantity;

    public function __construct($json = null)
    {
        parent::__construct($json);
    }

    public function jsonSerialize() {
        return get_object_vars($this);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getSizeNumber()
    {
        return $this->size_number;
    }

    /**
     * @return mixed
     */
    public function getSizeQuantity()
    {
        return $this->size_quantity;
    }

    /**
     * @param mixed $size_number
     */
    public function setSizeNumber($size_number)
    {
        $this->size_number = $size_number;
    }

    /**
     * @param mixed $size_quantity
     */
    public function setSizeQuantity($size_quantity)
    {
        $this->size_quantity = $size_quantity;
    }



}