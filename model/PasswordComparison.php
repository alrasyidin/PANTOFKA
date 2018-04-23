<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 4/21/2018
 * Time: 5:04 PM
 */

namespace model;

class PasswordComparison extends AbstractModel
{
    protected $owner_id;
    protected $password;
    protected $password_repeat;
    protected $new_password;
    protected $new_password_repeat;
    protected $old_password;

    public function __construct($json = null){
        parent::__construct($json);
    }

    public function jsonSerialize() {
        return get_object_vars($this);
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

}