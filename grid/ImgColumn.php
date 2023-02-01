<?php

namespace soc\yii2helper\grid;

use yii\base\Model;
use yii\grid\Column;

/**
 * Class ImgColumn
 * @package soc\yii2helper\grid
 * @protected $column string
 */
class ImgColumn extends Column
{
    protected string $column;
    public function __construct(array $config = [])
    {
        $this->column = $config['attribute'] ?? 'id';
        unset($config['attribute']);
        parent::__construct($config);
        $this->header = ucwords($this->options['name_column'] ?? 'Name');
    }

    protected function renderDataCellContent($model, $key, $index): string
    {
        /** @var $model Model */
        try {
            $class_name = strtolower((new \ReflectionClass($model))->getShortName());
        } catch (\ReflectionException $e) {
            $class_name = 'error';
        }
        $name_column = $this->options['name_column'] ?? 'name';
        $name = $model->$name_column ?? ($model->id);
        $img_url = $model->img;
        return "<img src='$img_url' alt='img' />";
    }
}
