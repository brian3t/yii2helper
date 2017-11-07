<?php
/**
 * Created by IntelliJ IDEA.
 * User: tn423731
 * Date: 11/7/2017
 * Time: 9:46 AM
 */

require_once "vendor/autoload.php";

use usv\yii2helper\PHPHelper;

$a = (object)['InventoryNumber' => 123, 'here it is' => 'Text here', 'a' => 3, '4' => 3];

var_dump(PHPHelper::array_key_to_underscore($a));
exit;

$s = 'InventoryNumber';
$res = preg_replace_callback('([A-Z])', function ($uppercase) {
    return '_' . strtolower(array_shift($uppercase));
}, $s);

echo $res;