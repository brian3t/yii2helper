<?php

namespace soc\yii2helper\grid;

use yii\base\Model;
use yii\grid\Column;

/**
 * Class AjaxToggleColumn
 * An ajax column to be used within Kartik-grid or Yii2-grid
 * Toggle to change from 0 to 1 and vice versa
 * usage: ['attribute'=>'attr','class'=>'soc\yii2helper\grid\AjaxToggleColumn']
 * @package soc\yii2helper\grid
 */
class AjaxToggleColumn extends Column
{
    public $attribute;
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->header = ucwords($this->attribute ?? 'Please pass attribute');
    }

    protected function renderDataCellContent($model, $key, $index)
    {
        /** @var $model Model */
        try {
            $class_name = strtolower((new \ReflectionClass($model))->getShortName());
        } catch (\ReflectionException $e) {
            $class_name = 'error';
        }
        return "a button here ";
    }
}
