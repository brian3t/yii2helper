<?php

namespace usv\yii2helper\i18n;

use yii\i18n\Formatter;

class ViewFormatter extends Formatter
{
    public function asJson($json_array)
    {
        $text = '';
        if (!is_array($json_array)) {
            return $text;
        }
        foreach ($json_array as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }
            $text .= "$key: " . strval($value) . "&nbsp;<br/>";
        }
        return $text;
    }
}