<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/15/2018
 * Time: 10:37 AM
 */

namespace model;


use model\dao\SizeDao;

class Order {

    private $id;
    private $customer_id;
    private $date;
    private $total_price;
    private $items;


    /* @throws \RuntimeException */
    public function __construct($customer_id, $items)
    {
        $this->id = null;
        $this->date = date('Y-m-d', time());
        $this->setItems($items);
        $this->setCustomerId($customer_id);
        $this->total_price = self::setTotalPrice($items);
    }

    /**
     * @param mixed $items
     */
    private function setItems($items)
    {
        if (is_array($items)){
        foreach ($items as $item){
            if (!($item instanceof Product)){
                throw new \RuntimeException('Expected products, but something else was passed in cart items...');
            }
        }$this->items = $items;
        }else{
            throw new \RuntimeException('Expected array of products, but something else was passed...');

        }
    }

    public function getProductSizeQuantity(Product $product , $size){
       $sizes = array_count_values($product->getSizes());
       return $sizes[$size];
    }

    public static function getSizeId($size_no){
        try{
           $size_dao = new SizeDao();
           $size_id = $size_dao->getSizeId($size_no);
           return $size_id;
        }catch (\PDOException $e){
            echo $e->getMessage();
        }
    }

    public static function setTotalPrice($items)
    {
        $total = 0;
        /* @var $item Product*/
        foreach ($items as $item_index=>$item){
            $price = $item->getPriceOnPromotion();
            $quantity = count($item->getSizes());
            $total += $price*$quantity;
        }
        return $total;
    }



    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param mixed $customer_id
     */
    private function setCustomerId($customer_id)
    {
        if ($customer_id < 0 || !is_numeric($customer_id)){
            throw new \RuntimeException('Customer id is not valid!');
        }
        $this->customer_id = $customer_id;
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
    public function getCustomerId()
    {
        return $this->customer_id;
    }

    /**
     * @return false|string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return mixed
     */
    public function getTotalPrice()
    {
        return $this->total_price;
    }

    /**
     * @return mixed
     */
    public function getItems()
    {
        return $this->items;
    }



}