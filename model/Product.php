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

    protected $id;
    protected $product_name;
    protected $price;
    protected $info;
    protected $product_image_url;
    protected $promo_percentage;
    protected $color;
    protected $material;
    protected $category;
    protected $category_parent;

    protected $sizes = []; // Array of sizes
    protected $ratings = []; // Array of ratings


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
        //$this->setSizes(ProductsDao::getSizes($this->id));
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        if ($id<0){

                throw new \RuntimeException("the id can not be < 0");


        }
        $this->id = $id;
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
    public function getPromoPercantage()
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
        $this->price = $price;
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
    public function setPromoPercantage($promo_percantage)
    {
        if ($promo_percantage <0 || $promo_percantage > 99){
            throw new \RuntimeException("The promo percentage must be from 0 to 99 !");
        }
        $this->promo_percentage = $promo_percantage;
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

    /**
     * @return mixed
     */
    public function getCategoryParent()
    {
        return $this->category_parent;
    }

    /**
     * @param mixed $category_parent
     */
    public function setCategoryParent($category_parent)
    {
        if (is_numeric($category_parent) || strlen($category_parent) > 30){
            throw new \RuntimeException("Invalid data for parent category");
        }
        $this->category_parent = $category_parent;
    }








}