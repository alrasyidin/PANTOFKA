<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/14/2018
 * Time: 3:30 PM
 */

namespace model;



use model\dao\ProductsDao;

class Product extends AbstractModel
{

    protected $product_id;
    protected $product_name;
    protected $price;
    protected $info;
    protected $product_image_url;
    protected $promo_percentage;
    protected $price_on_promotion;
    protected $color;
    protected $material;
    protected $category;
    protected $style;
    protected $show_to_admin;

    protected $sizes = []; // Array of sizes
    protected $ratings = [];

    /**
     * @return mixed
     */
    public function getShowToAdmin()
    {
        return $this->show_to_admin;
    }

    /**
     * @param mixed $show_to_admin
     */
    public function setShowToAdmin($show_to_admin)
    {
        $this->show_to_admin = $show_to_admin;
    }

    /**
     * @return float
     */
    public function getPriceOnPromotion()
    {
        return $this->price_on_promotion;
    }




    /**
     * @return mixed
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * @param mixed $style
     */
    public function setStyle($style)
    {
        $this->style = $style;
    } // Array of ratings


    public function jsonSerialize() {
        return get_object_vars($this);
    }

    public function addToSizes(Size $s){
        $this->sizes[] = $s;
    }


    /**
     * @param array $sizes
     */
    public function setSizes($sizes)
    {
        if (!is_array($sizes)){
            throw new \RuntimeException("You have to set array of sizes");

        }
        $this->sizes = $sizes;
    }

    /**
     * @param mixed $product_id
     */
    public function setProductId($product_id)
    {
        $this->product_id = $product_id;
    }

    /**
     * @param array $ratings
     */
    public function setRatings($ratings)
    {
        if (!is_array($ratings)){
            throw new \RuntimeException("You have to set array of ratings");

        }
        $this->ratings = $ratings;
    }

    public function addToRatings(Rating $r){
        $this->ratings[] = $r;
    }
    public function __construct($json = null)
    {
        parent::__construct($json);
        $this->price_on_promotion = round(($this->price -($this->price * $this->promo_percentage)/100), 2);
        //$this->setSizes(ProductsDao::getSizes($this->id));

        if (isset($this->price)){ // Petra butna tuk
            $this->setPrice($this->price);

        }

    }

    /**
     * @return mixed
     */
    public function getProductId()
    {
        return $this->product_id;
    }



    /**
     * @param mixed $product_image_url
     */
    public function setProductImageUrl($product_image_url)
    {
        if ($product_image_url === null || strlen($product_image_url) < 10|| strlen($product_image_url) > 100 ){
            throw new \RuntimeException("Bad input for image url");
        }
        $this->product_image_url = $product_image_url;
    }

    /**
     * @return mixed
     */
    public function getProductName()
    {
        return $this->product_name;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return mixed
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @return mixed
     */
    public function getProductImgUrl()
    {
        return $this->product_image_url;
    }

    /**
     * @return mixed
     */
    public function getPromoPercentage()
    {
        return $this->promo_percentage;
    }

    /**
     * @return array
     */
    public function getSizes()
    {
        return $this->sizes;
    }

    /**
     * @return array
     */
    public function getRatings()
    {
        return $this->ratings;
    }

    /**
     * @param mixed $product_name
     */
    public function setProductName($product_name)
    {
        if (strlen($product_name) < 3 || strlen($product_name) >  20 || $product_name===null || empty($product_name)){
            throw new \RuntimeException("Bad input for product name");

        }
        $this->product_name = $product_name;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price)
    {
        if ($price < 0 || $price === null || empty($price)){
            throw new \RuntimeException("Bad input for product price");

        }
        $this->price = intval($price);
    }

    /**
     * @param mixed $info
     */
    public function setInfo($info)
    {
        if (strlen($info) > 150){
            throw new \RuntimeException("The info is too long");
        }
        $this->info = $info;
    }



    /**
     * @param mixed $promo_percantage
     */
    public function setPromoPercentage($promo_percentage)
    {
        if ($promo_percentage <0 || $promo_percentage > 99){
            throw new \RuntimeException("The promo percentage must be from 0 to 99 !");
        }
        $this->promo_percentage = $promo_percentage;
    }

    /**
     * @return mixed
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param mixed $color
     */
    public function setColor($color)
    {
        if (is_numeric($color) || strlen($color) > 30){
            throw new \RuntimeException("Invalid data for color");
        }
        $this->color = $color;
    }

    /**
     * @return mixed
     */
    public function getMaterial()
    {
        return $this->material;
    }

    /**
     * @param mixed $material
     */
    public function setMaterial($material)
    {
        if (is_numeric($material) || strlen($material) > 30){
            throw new \RuntimeException("Invalid data for material");
        }
        $this->material = $material;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category)
    {
        if (is_numeric($category) || strlen($category) > 30){
            throw new \RuntimeException("Invalid data for category");
        }

        $this->category = $category;
    }











}