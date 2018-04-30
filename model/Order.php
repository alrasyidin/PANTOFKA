<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/15/2018
 * Time: 10:37 AM
 */

namespace model;


class Order {

    private $id;
    private $customer_id;
    private $date;
    private $total_price;
    private $items;


    /* @throws \RuntimeException */
    public function __construct($customer_id, $items)
    {
        $this->date = date('m/d/Y h:i:s a', time());
        $this->setItems($items);
        $this->setCustomerId($customer_id);
        $this->setTotalPrice($items);
    }

    /**
     * @param mixed $items
     */
    private function setItems($items)
    {
        foreach ($items as $item){
            if (!($item instanceof Product)){
                throw new \RuntimeException('Expected products, but something else was passed in cart items...');
            }
        }
        $this->items = $items;
    }

    public function setTotalPrice($items)
    {
        $total = 0;
        /* @var $item Product*/
        foreach ($items as $item_index=>$item){
            $price = $item->getPriceOnPromotion();
            $quantity = count($item->getSizes());
            $total += $price*$quantity;
        }
        $this->total_price = $total;
        return $this->total_price;
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