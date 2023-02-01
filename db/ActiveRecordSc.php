<?php
/**
 * Active Record Socal
 *
 */

namespace soc\yii2helper\db;

class ActiveRecordSc extends \yii\db\ActiveRecord
{
  protected function insertInternal($attributes = null) {
    if (! $this->beforeSave(true)) {
      return false;
    }
    $values = $this->getDirtyAttributes($attributes);
    //b3t ignore empty values, let db default handle it
    foreach ($values as $k => $value) {
      if (empty($value)) unset($values[$k]);
    }
    if (($primaryKeys = static::getDb()->schema->insert(static::tableName(), $values)) === false) {
      return false;
    }
    foreach ($primaryKeys as $name => $value) {
      $id = static::getTableSchema()->columns[$name]->phpTypecast($value);
      $this->setAttribute($name, $id);
      $values[$name] = $id;
    }

    $changedAttributes = array_fill_keys(array_keys($values), null);
    $this->setOldAttributes($values);
    $this->afterSave(true, $changedAttributes);

    return true;
    //b3t ignore empty values, let db default handle it
    /* yii code 1/21/23
     * if (!$this->beforeSave(true)) {
            return false;
        }
        $values = $this->getDirtyAttributes($attributes);
        if (($primaryKeys = static::getDb()->schema->insert(static::tableName(), $values)) === false) {
            return false;
        }
        foreach ($primaryKeys as $name => $value) {
            $id = static::getTableSchema()->columns[$name]->phpTypecast($value);
            $this->setAttribute($name, $id);
            $values[$name] = $id;
        }

        $changedAttributes = array_fill_keys(array_keys($values), null);
        $this->setOldAttributes($values);
        $this->afterSave(true, $changedAttributes);

        return true;
     */
  }
}
