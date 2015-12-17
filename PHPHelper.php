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
	 * @param mixed $array
	 * @param string $glue
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
}