<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/14/2018
 * Time: 3:23 PM
 */

namespace model;


use model\dao\UserDao;

class User extends AbstractModel {
    protected $user_id;
    protected $email;
    protected $password;
    protected $first_name;
    protected $last_name;
    protected $gender;
    protected $is_admin;
    protected $favorites;
    protected $cart;

    public function jsonSerialize() {
        return get_object_vars($this);
    }

    public function __construct($json = null)
    {
        parent::__construct($json);
        if (isset($this->password)){
            $this->setPassword($this->password);
        }
        if (isset($this->email)){
            $this->setEmail($this->email);
        }
        if (isset($this->first_name)){
            $this->setFirstName($this->first_name);
        }
        if (isset($this->last_name)){
            $this->setLastName($this->last_name);
        }
        if (isset($this->gender)){
            $this->setGender($this->gender);
        }

    }

    public function addToFav(Product $p){
        $this->favorites[] = $p;
    }

    public function addToCart(Product $p){
        $this->cart[] = $p;
    }


    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }


    public function setUserId($user_id)
    {
        if ($user_id < 1){
            throw new \RuntimeException("Bad data for id");
        }
        $this->user_id = $user_id;
    }

    /**
     * @return mixed
     */

    public function getEmail(){
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email){
        if(strlen(strlen($email) < 5 || strlen($email) > 45 || is_numeric($email) )){
            throw new \RuntimeException("Bad data for email :" .$this->email. ";");
        }
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password){
        if(strlen($password) < 5 || is_numeric($password) ){
            throw new \RuntimeException("USER CLASS: Bad data for password: lenght" .strlen($password) . " and is numeric is " . var_dump(is_numeric($password)));
        }
        $this->password = sha1($password); // !!!!!!!!!!!
    }

    public function __unset($password){
        if($password !== null){
            // Double sha1 :D.....
            if(sha1($password) === $this->password){
                unset($this->password);
            }else{
                throw new \RuntimeException('Bad data is passed when un-setting password.
                                             Passed value of '.$password.' do not match real object property value of ' . $this->password);
            }
        }

    }

    /**
     * @param mixed $first_name
     */
    public function setFirstName($first_name){
        if(strlen($first_name) < 5 || strlen($first_name) > 45 || is_numeric($first_name) ){
            throw new \RuntimeException("Bad data for first name");
        }
        $this->first_name = $first_name;
    }

    /**
     * @param mixed $last_name
     */
    public function setLastName($last_name)
    {
        if(strlen($last_name) < 5 || strlen($last_name) > 45 || is_numeric($last_name) ){
            throw new \RuntimeException("Bad data for last name");
        }
        $this->last_name = $last_name;
    }


    /**
     * @param mixed $gender
     */
    public function setGender($gender)
    {
        if($gender !== 'm' && $gender !== 'f' && $gender !== 'M' && $gender !== 'F'){
        throw new \RuntimeException("Bad data for gender : " . $gender);
    }
        $this->gender = $gender;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * @return mixed
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @return mixed
     */
    public function getisAdmin()
    {
        return $this->is_admin;
    }

    /**
     * @return array
     */
    public function getFavorites()
    {
        return $this->favorites;
    }

    /**
     * @return mixed
     */
    public function getCart()
    {
        return $this->cart;
    }




}