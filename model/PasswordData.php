<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/21/2018
 * Time: 5:04 PM
 */

namespace model;

class PasswordData extends AbstractModel
{
    protected $owner_id;
    protected $password;
    protected $password_repeat;
    protected $new_password;
    protected $new_password_repeat;
    protected $old_password;

    /* @throws \RuntimeException */
    public function __construct($json = null){
        try{
            self::setPasswords($json);
        }catch (\RuntimeException $e){
            throw $e;
        }
        parent::__construct($json);
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return mixed
     */
    public function getPasswordRepeat()
    {
        return $this->password_repeat;
    }

    /**
     * @return mixed
     */
    public function getNewPasswordRepeat()
    {
        return $this->new_password_repeat;
    }

    /**
     * @return mixed
     */
    public function getOwnerId()
    {
        return $this->owner_id;
    }

    /**
     * @param mixed $owner_id
     */
    public function setOwnerId($owner_id)
    {
        $this->owner_id = $owner_id;
    }

    /**
     * @return mixed
     */
    public function getNewPassword()
    {
        return $this->new_password;
    }

    /**
     * @return mixed
     */
    public function getOldPassword()
    {
        return $this->old_password;
    }

    private static function setPasswords($json)
    {
        if($json != null) {
            $passwords = json_decode($json);
            foreach ($passwords as $password_type => &$password_value) {
                if(strlen($password_value) < 5 || strlen($password_value) > 45){
                    throw new \RuntimeException("Value of $password_type is wrong length!");
                }
                $password_value = sha1($password_value);
            }
            return json_encode($passwords);
        }
    }

}