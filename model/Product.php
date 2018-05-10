<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/14/2018
 * Time: 3:30 PM
 */

namespace model;



use controller\ProductController;
use model\dao\ProductsDao;
use model\dao\SizeDao;

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
    protected $size_quantities;

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

    public function addToSizes($size_no){ // This should take a Size object
        $this->sizes[] = $size_no;
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

        // =========================================
        $this->size_quantities = array();

    }

    /**
     * @return mixed
     */
    public function getProductId()
    {
        return $this->product_id;
    }

    /**
     * @return mixed
     */
    public function getProductImageUrl()
    {
        return $this->product_image_url;
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


    public function getSizeQuantity($size_no = null){
        if ($size_no == null){
            // when calling the method with no parameter
            // we need size quantities,
            // despite its value and indexes
            return $this->size_quantities;
        }

        //Otherwise we need data for a specific size, passed by
        if (!is_numeric($size_no) ||
            $size_no < ProductController::MIN_SIZE_NUMBER_KIDS ||
            $size_no > ProductController::MAX_SIZE_NUMBER_MEN ){
            throw new \RuntimeException("Invalid data for size in the getSizeQuantity getter");
        }
        $sizes_and_quantities = $this->size_quantities;

        if (array_key_exists($size_no , $sizes_and_quantities)){
           return $sizes_and_quantities[$size_no];
        }
        return -1;
    }

    public function unsetSizeQuantity()
    {
        $this->size_quantities = array();
    }

    public function setSizeQuantity($size_no)
    {
        if (!is_numeric($size_no) ||
            $size_no < ProductController::MIN_SIZE_NUMBER_KIDS ||
            $size_no > ProductController::MAX_SIZE_NUMBER_MEN){
            throw new \RuntimeException("Invalid data for size/quantity");

        }
        $sizes_and_quantities = $this->size_quantities;
        if (empty($sizes_and_quantities)){

            $sizes_and_quantities = array();
            $sizes_and_quantities[$size_no] = 1;
        }else{
          if (array_key_exists($size_no , $sizes_and_quantities)){
              $sizes_and_quantities[$size_no]++;
          }
        }
        $this->size_quantities = $sizes_and_quantities;
    }


}