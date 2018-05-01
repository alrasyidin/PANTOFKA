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
    protected $size_id;
    protected  $size_number;
    protected  $size_quantity;

    public function __construct($json = null)
    {
        parent::__construct($json);
    }

    /**
     * @return mixed
     */
    public function getSizeId()
    {
        return $this->size_id;
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
        { if ($size_number <  20 || $size_number > 0 || !is_numeric($size_number)){
            throw new \RuntimeException("Invalid data for size");

        }

            $this->size_number = $size_number;
        }
    }

    /**
     * @param mixed $size_quantity
     */
    public function setSizeQuantity($size_quantity)
    { if ($size_quantity < 0 || $size_quantity > 9999 || !is_numeric($size_quantity)){
        throw new \RuntimeException("Invalid data for quantity");

    }
        $this->size_quantity = $size_quantity;
    }



}