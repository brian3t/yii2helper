<?php
namespace usv\yii2helper;
/**
 * Created by PhpStorm.
 * User: tri
 * Date: 5/4/15
 * Time: 1:56 PM
 */

class PHPHelper {
	/**
     * Implode an array, accepting null array as an input
	 * @param mixed $array
	 * @param string $glue
     * @return string
	 */
	static public function imp( $glue = null,$array = null ) {
		if ( empty( $array ) ) {
			return "";
		}
		return implode($glue, $array);
	}
	/*
	 * Normalize strings to store into databases
	 */
	static public function dbNormalizeString($str){
		return preg_replace( '/(\s)+/',"",strtolower($str));
	}

    static public function date_time_format($date_time){
        return \Yii::$app->formatter->asDatetime($date_time, 'php:l, d-M-Y g:i:sA T');
    }

    /**
     * Convert keys of an array into underscore, e.g. Inventory Number into inventory_number
     * @param $a
     * @return array
     */
    static public function array_key_to_underscore($a = []){
        $tmp  = [];
        if (!is_array($a)){
            return $tmp;
        }
        foreach ($a as $k=>$v){
            $tmp[strtolower(str_replace(' ','_',$k))] = $v;
        }
        return $tmp;
    }
}