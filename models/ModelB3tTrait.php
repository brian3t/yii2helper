<?php
/**
 * Trait ModelB3tTrait
 * For use by \yii\db\ActiveRecord only
 * @property \yii\db\ActiveRecord $this
 */

namespace usv\yii2helper\models;

use yii\db\Exception;

trait ModelB3tTrait
{
    /**
     * Find a record that has those attributes
     * If it doesn't exist, try creating it
     * @param $attributes
     * @return ModelB3tTrait
     */
    public static function findOrCreate($attributes)
    {
        $found_model = static::findOne($attributes);
        if ($found_model instanceof static) {
            return $found_model;
        } else {
            $new_model = new static();
            $new_model->setAttributes($attributes);
            try {
                $new_model->save();
            } catch (Exception $e) {
                \Yii::error($e);
                return new static();
            }
            return $new_model;
        }
    }
}