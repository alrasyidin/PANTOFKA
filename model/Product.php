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

    private $id;
    private $product_name;
    private $price;
    private $info;
    private $product_img_url;
    private $promo_percantage;
    private $color;
    private $material;
     private $category;
     private $category_parent;

    private $sizes = [];
    private $ratings = [];

    public function addToSizes(Size $s){
        $this->sizes[] = $s;
    }


    /**
     * @param array $sizes
     */
    public function setSizes($sizes)
    {
        $this->sizes = $sizes;
    }

    /**
     * @param array $ratings
     */
    public function setRatings($ratings)
    {
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
        return $this->product_img_url;
    }

    /**
     * @return mixed
     */
    public function getPromoPercantage()
    {
        return $this->promo_percantage;
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
        $this->product_name = $product_name;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @param mixed $info
     */
    public function setInfo($info)
    {
        $this->info = $info;
    }

    /**
     * @param mixed $product_img_url
     */
    public function setProductImgUrl($product_img_url)
    {
        $this->product_img_url = $product_img_url;
    }

    /**
     * @param mixed $promo_percantage
     */
    public function setPromoPercantage($promo_percantage)
    {
        $this->promo_percantage = $promo_percantage;
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
        $this->category_parent = $category_parent;
    }








}