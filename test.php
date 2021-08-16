<?php
/**
 * Created by IntelliJ IDEA.
 * User: tn423731
 * Date: 11/7/2017
 * Time: 9:46 AM
 */

require_once "vendor/autoload.php";

use usv\yii2helper\PHPHelper;

//$a = (object)['InventoryNumber' => 123, 'here it is' => 'Text here', 'a' => 3, '4' => 3];
$a = 411;
$b = 'var b here';

$res = [];
$csv_list_of_var_names = 'a,b';
$csv_list_of_var_names = str_replace(' ', '', $csv_list_of_var_names);
$csv_list_of_var_names = explode(',', $csv_list_of_var_names);
foreach ($csv_list_of_var_names as $var_name) {
    $value = $$var_name;
    $res[$var_name] = $value;
}
var_dump($res);

//var_dump(PHPHelper::compact_list('a,b'));
exit;
