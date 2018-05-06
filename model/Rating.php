<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 21.4.2018 г.
 * Time: 6:39
 */

namespace model;

use model\AbstractModel;
class Rating extends AbstractModel
{

    protected $rating_value;
    protected $user_id;
    protected $product_id;

    public function __construct($json = null)
    {
        parent::__construct($json);
        $this->rating_value = intval($this->rating_value);
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param mixed $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * @return mixed
     */
    public function getProductId()
    {
        return $this->product_id;
    }

    /**
     * @param mixed $product_id
     */
    public function setProductId($product_id)
    {
        $this->product_id = $product_id;
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
    public function getRatingValue()
    {
        return $this->rating_value;
    }

    /**
     * @param mixed $rating_value
     */
    public function setRatingValue($rating_value)
    {
        $this->rating_value = $rating_value;
    }

}