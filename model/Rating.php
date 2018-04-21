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

    private $id;
    private $rating_value;

    public function __construct($json = null)
    {
        parent::__construct($json);
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