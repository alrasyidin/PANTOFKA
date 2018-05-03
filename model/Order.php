<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/15/2018
 * Time: 10:37 AM
 */

namespace model;


use model\dao\SizeDao;

class Order extends AbstractModel {

    private $order_id;
    private $user_id;
    private $date;
    private $total_price;
    private $products = array();
    private $products_has_sizes = array();

    /* @throws \RuntimeException */
    public function __construct($json = null)
    {
        parent::__construct($json);

        if (!isset($this->date)){
            $this->date = date('Y-m-d', time());
        }
        if (!isset($this->total_price)){
            $this->total_price = self::calculateTotalPrice($this->products);
        }
    }

    /**
     * @param mixed $items
     */
    public function setProducts($items)
    {
        if (is_array($items)){
            foreach ($items as $index=>&$item){
                if (!($item instanceof Product)){
                    throw new \RuntimeException('Expected products, but something else was passed in cart items...');
                }else{
                    $this->products[$index] = $item;
                }
            }
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


    public static function calculateTotalPrice($items)
    {
        $total = 0;
        /* @var $item Product*/
        foreach ($items as $item){
            $price = $item->getPriceOnPromotion();
            $quantity = count($item->getSizes());
            $total += $price*$quantity;
        }
        return $total;
    }

    /**
     * @return float|int
     */
    public function getTotalPrice()
    {
        return $this->total_price;
    }

    /**
     * @param float|int $total_price
     */
    public function setTotalPrice($total_price = null)
    {
        if (!isset($total_price)){
            $products = $this->getProducts();
            if (isset($products)){
                $this->total_price = self::calculateTotalPrice($products);

            }else{
                throw new \RuntimeException('No data provided to calculate total price');
            }
        }else{
            if ($total_price > 0){
                $this->total_price = $total_price;
            }else{
                throw new \RuntimeException('Bad data provided for total price');

            }
        }
    }


    /**
     * @param mixed $customer_id
     */
    public function setUserId($customer_id)
    {
        if ($customer_id < 0 || !is_numeric($customer_id)){
            throw new \RuntimeException('Customer id is not valid!');
        }
        $this->user_id = $customer_id;
    }


    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
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
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @return mixed
     */
    public function getOrderId()
    {
        return $this->order_id;
    }

    /**
     * @param mixed $order_id
     */
    public function setOrderId($order_id)
    {
        $this->order_id = $order_id;
    }



}