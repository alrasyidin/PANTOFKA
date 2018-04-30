<?php
/**
 * Created by PhpStorm.
 * User: user-09
 * Date: 19.04.18
 * Time: 14:59
 */

namespace model;


abstract class AbstractModel implements \JsonSerializable {

    public function __construct($json = null)
    {
        if($json != null) {
            $json_obj = json_decode($json);
            foreach ($json_obj as $key => $value) {
                if(!is_array($value) && !is_object($value)) {
                    $this->$key = $value;
                }
            }
        }
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    public function __set($name, $value)
    {
        $this->name = $value;
    }


}