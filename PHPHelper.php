<?php

namespace usv\yii2helper;
/**
 * User: tri
 * Date: 5/4/15
 * Time: 1:56 PM
 */
class PHPHelper
{
    public static $K_NON_SQL_PARAMS = ['expand', 'sort', 'direction', 'per-page'];

    /**
     * API parameters pre-processing
     * E.g. convert date into db format
     * @param $params
     */
    public static final function api_param_pre(&$params) {
        foreach ($params as $param => &$value) {
            if (str_contains($param, 'date')) $value = self::date_mysql($value);
        }
    }

    /**
     * Implode an array, accepting null array as an input
     * @param mixed $array
     * @param string $glue
     * @return string
     */
    public static function imp($glue = null, $array = null) {
        if (empty($array)) {
            return "";
        }
        return implode($glue, $array);
    }

    /*
     * Normalize strings to store into databases
     */
    public static function dbNormalizeString($str) {
        return preg_replace('/(\s)+/', "", strtolower($str));
    }

    /**
     * Convert a list of variables to an associative array. Var name is key, value is value
     * This is stub function; to use, you must copy paste functions' content
     * Otherwise, PHP won't be able to get $$var_name
     * @param $csv_list_of_var_names 'var1,var2,var3'
     * @return array
     */
    public static function compact_list($csv_list_of_var_names): array {
        $res = [];
        $csv_list_of_var_names = str_replace(' ', '', $csv_list_of_var_names);
        $csv_list_of_var_names = str_replace("\n", '', $csv_list_of_var_names);
        $csv_list_of_var_names = explode(',', $csv_list_of_var_names);
        foreach ($csv_list_of_var_names as $var_name) {
            $value = $$var_name;
            $res[$var_name] = $value;
        }
        return $res;
    }

    public static function dateTimeFormat($date_time) {
        return \Yii::$app->formatter->asDatetime($date_time, 'php:l, d-M-Y g:i:sA T');
    }

    /**
     * Format a date/time string to mysql YYYY-MM-DD
     * E.g. 6/1/2021 will become 2021-06-01 05:06:07
     * @param $date_time
     * @return string|null
     */
    public static final function date_mysql($date_time) {
        try {
            $date_obj = new \DateTime($date_time);
            return $date_obj->format('Y-m-d h:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Convert keys of an array into underscore, e.g. Inventory Number into inventory_number
     * @param mixed $a array or object
     * @return array
     */
    public static function arrayKeyToUnderscore($a = null) {
        $tmp = [];
        if (is_null($a) && ! is_array($a) && ! is_object($a)) {
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
    public static function stealValue(&$array, $key) {
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
    public static function parseAddress($address) {
        /***
         * 2008-05-08 used first for quickbooks import => database table
         *
         * johnson city, tex|tx|texas  78691[-1234]
         * vancouver, bc A0A 0A0
         * charlotte, n.c. 02899
         ***/
        try {
            $address = trim($address);
            $zip = '([a-z0-9][0-9][a-z0-9][- ]*[0-9][a-z0-9][0-9]*)([- ]+([0-9]{4}))*';
            $state = '([.a-z]{2,4})';
            $parts = explode(',', $address);
            array_walk($parts, function (&$part) {
                $part = trim($part);
            });
            $state_zip = array_pop($parts);
            if (preg_match("/$state\s+$zip" . '$' . "/i", $state_zip, $matches)) {
                if (sizeof($matches) >= 2) {
                    $state = strtoupper(str_replace('.', '', $matches[1]));
                    $zip = $matches[2];
                }
            } else { //state only
                $state = $state_zip;
                $zip = null;
            }
            $city = array_pop($parts);
            $address1 = array_pop($parts);
            $address2 = array_pop($parts);
            if (! empty($address2)) {
                [$address1, $address2] = [$address2, $address1];
            }

            return compact('address1', 'address2', 'city', 'state', 'zip');
        } catch (\Exception $e) {
            $address1 = null;
            $address2 = null;
            $city = null;
            $state = null;
            $zip = null;
            return compact('address1', 'address2', 'city', 'state', 'zip');
        }
    }

    public static function starts_with($haystack, $needle) {
        return substr_compare($haystack, $needle, 0, strlen($needle)) === 0;
    }

    public static function ends_with($haystack, $needle) {
        return substr_compare($haystack, $needle, -strlen($needle)) === 0;
    }

    /**
     * Returns max delta in lat/lng, based on mile distance
     * @param $mile
     * @return float
     */
    public static function rev_haversin_simple($mile) {
        $MILE_TO_KM = 1.60934;
        return $mile * $MILE_TO_KM * (1 / 111) * 1.5 / 2; //1km = 1/111 deg ; multiply by .66 to balance off bird-fly vs dog-run ; divide by 2 for radius (half north half south)
    }
    /*
    function distance_to_latlng($dist)
    {
        $earth_radius = 3960.0
$degrees_to_radians = math . pi / 180.0
$radians_to_degrees = 180.0 / math . pi

def
def change_in_longitude(latitude, miles):
    "Given a latitude and a distance west, return the change in longitude."
    # Find the radius of a circle around the earth at given latitude.
    r = earth_radius * math . cos(latitude * degrees_to_radians)
    return (miles / r) * radians_to_degrees

    }
    function rev_haversin(float $lon, float $lat, float $bearing, int $distance)
    {
      "Implementation of the reverse of Haversine formula. Takes one set of latitude/longitude as a start point, a bearing, and a distance, and returns the resultant lat/long pair."
            [{lon :long lat :lat bearing :bearing distance :distance}]
        $R = 6378.137; //Radius of Earth in km
        $lat1 = deg2rad($lat);
        $lng1 = deg2rad($lon);
        $angdist = $distance / $R;//angle distance
        $theta = deg2rad($bearing);
        //calculate
        $lat2 = 1;
        /*
          (let [R 6378.137
                lat1 (Math/toRadians lat)
                lon1 (Math/toRadians lon)
                angdist (/ distance R)
                theta (Math/toRadians bearing)
                lat2 (Math/toDegrees (Math/asin (+ (* (Math/sin lat1) (Math/cos angdist)) (* (Math/cos lat1) (Math/sin angdist) (Math/cos theta)))))
                lon2 (Math/toDegrees (+ lon1 (Math/atan2 (* (Math/sin theta) (Math/sin angdist) (Math/cos lat1)) (- (Math/cos angdist) (* (Math/sin lat1) (Math/sin lat2))))))]
            {:lat lat2 :lon lon2}))
    }
*/

}
