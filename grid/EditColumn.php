<?php

namespace usv\yii2helper\grid;

use yii\base\Model;
use yii\grid\Column;

class EditColumn extends Column
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->header = ucwords($this->options['name_column'] ?? 'Name');
    }

    protected function renderDataCellContent($model, $key, $index)
    {
        /** @var $model Model */
        try {
            $class_name = strtolower((new \ReflectionClass($model))->getShortName());
        } catch (\ReflectionException $e) {
            $class_name = 'error';
        }
        $name_column = $this->options['name_column'] ?? 'name';
        $name = $model->$name_column ?? ($model->id);
        $view_or_edit = $this->options['is_edit'] ? 'update' : 'view';
        return "<a href='/$class_name/$view_or_edit?id={$model->id}'>$name</a> ";
    }
}