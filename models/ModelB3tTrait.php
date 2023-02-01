<?php
/**
 * Trait ModelB3tTrait
 * For use by \yii\db\ActiveRecord only
 * @property \yii\db\ActiveRecord $this
 */

namespace soc\yii2helper\models;

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

    /**
     * Save model, and also log errors in any
     * @param bool $runValidation
     * @param null $attributeNames
     * @return bool
     */
    public function saveAndLogError(bool $runValidation = true, $attributeNames = null): bool {
        $this->save($runValidation, $attributeNames);
        if (count($this->errors)){
            echo "Error saveAndLogError: classname= " . static::class . " | id= " . $this->id . ' | error= ' . json_encode($this->errors);
            \Yii::error($this->errors);
            return false;
        }
        return true;
    }
}
