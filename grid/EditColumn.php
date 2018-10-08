<?php

namespace usv\yii2helper\grid;

use yii\base\Model;
use yii\grid\Column;

class EditColumn extends Column
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->header = 'Name';
    }

    protected function renderDataCellContent($model, $key, $index)
    {
        /** @var $model Model */
        try {
            $class_name = strtolower((new \ReflectionClass($model))->getShortName());
        } catch (\ReflectionException $e) {
            $class_name = 'error';
        }
        $name = $model->name ?? ($model->id);
        return "<a href='/$class_name/view?id={$model->id}'>$name</a> ";
    }
}