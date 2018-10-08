<?php

namespace usv\yii2helper;
/**
 * Created by PhpStorm.
 * User: tri
 * Date: 5/4/15
 * Time: 1:56 PM
 */
class PHPHelper
{
    /**
     * Implode an array, accepting null array as an input
     * @param mixed $array
     * @param string $glue
     * @return string
     */
    public static function imp($glue = null, $array = null)
    {
        if (empty($array)) {
            return "";
        }
        return implode($glue, $array);
    }

    /*
     * Normalize strings to store into databases
     */
    public static function dbNormalizeString($str)
    {
        return preg_replace('/(\s)+/', "", strtolower($str));
    }

    public static function dateTimeFormat($date_time)
    {
        return \Yii::$app->formatter->asDatetime($date_time, 'php:l, d-M-Y g:i:sA T');
    }

    /**
     * Convert keys of an array into underscore, e.g. Inventory Number into inventory_number
     * @param mixed $a array or object
     * @return array
     */
    public static function arrayKeyToUnderscore($a = null)
    {
        $tmp = [];
        if (is_null($a) && !is_array($a) && !is_object($a)) {
            return $tmp;
        }
        foreach ($a as $k => $v) {
            $k = preg_replace_callback('([A-Z])', function ($uppercase) {
                return '_' . strtolower(array_shift($uppercase));
            }, $k);//uppercase to _lowercase
            $tmp[str_replace(' ', '_', $k)] = $v;
        }
        return $tmp;
    }

    /**
     * Steal a value from an array
     * @param $array
     * @param $key
     * @return integer The stolen value
     */
    public static function stealValue(&$array, $key)
    {
        $value = isset($array[$key]) ? $array[$key] : null;
        unset($array[$key]);
        return $value;
    }

    /**
     * Parse address with this format:
     * add1 add2, city, state zip
     * @param $address
     * @return array
     */
    public static function parseAddress($address)
    {
        /***
         * 2008-05-08 used first for quickbooks import => database table
         *
         * johnson city, tex|tx|texas  78691[-1234]
         * vancouver, bc A0A 0A0
         * charlotte, n.c. 02899
         ***/
        $address = trim($address);
        $zip = '([a-z0-9][0-9][a-z0-9][- ]*[0-9][a-z0-9][0-9]*)([- ]+([0-9]{4}))*';
        $state = '([.a-z]{2,4})';
        $parts = explode(',', $address);
        array_walk($parts, function (&$part) {
            $part = trim($part);
        });
        $state_zip = array_pop($parts);
        if (!preg_match("/$state\s+$zip" . '$' . "/i", $state_zip, $matches)) {
            return [];
        }
        if (sizeof($matches) < 2) {
            return [];
        }
        $state = strtoupper(str_replace('.', '', $matches[1]));
        $zip = $matches[2];
        $city = array_pop($parts);
        $address1 = array_pop($parts);
        $address2 = array_pop($parts);
        if (!empty($address2)) {
            [$address1, $address2] = [$address2, $address1];
        }

        return compact('address1', 'address2', 'city', 'state', 'zip');
    }

}
