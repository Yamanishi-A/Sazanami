<?php

//FuelPHP 1.8 の htmlentities バグを修正するためのオーバーライド

class Security extends \Fuel\Core\Security
{
    public static function htmlentities($value, $flags = null, $encoding = null, $double_encode = null)
    {
        if (is_null($value)) {
            return $value;
        }
        
        return parent::htmlentities($value, $flags, $encoding, $double_encode);
    }
}