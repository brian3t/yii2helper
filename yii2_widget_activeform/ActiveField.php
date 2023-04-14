<?php

/**
 * @copyright  Copyright &copy; Kartik Visweswaran, Krajee.com, 2015 - 2022
 * @package    yii2-widgets
 * @subpackage yii2-widget-activeform
 * @version    1.6.2
 */

namespace soc\yii2helper\yii2_widget_activeform;

use Exception;
use kartik\base\Config;
use kartik\base\Lib;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
//use yii\helpers\Html;
use soc\yii2helper\Html;
use yii\helpers\Inflector;
use kartik\base\AddonTrait;
use yii\widgets\ActiveField as YiiActiveField;
use yii\widgets\ActiveField as KartikActiveField;

/**
 * @property ActiveForm $form
 * @author Kartik Visweswaran <kartikv2@gmail.com> Brian3t
 * @since  1.0
 */
class ActiveField extends KartikActiveField
{

  /**
   * Renders a checkbox. This method will generate the "checked" tag attribute according to the model attribute value.
   *
   * @param array $options the tag options in terms of name-value pairs. The following options are specially
   * @param bool|null $enclosedByLabel whether to enclose the radio within the label. If `true`, the method will
   * still use [[template]] to layout the checkbox and the error message except that the radio is enclosed by
   * the label tag.
   *
   * @return ActiveField object
   * @throws InvalidConfigException
   */
  public function checkbox($options = [], $enclosedByLabel = null) {
//        return $this->getToggleField(self::TYPE_CHECKBOX, $options, $enclosedByLabel); b3t disabled
    if ($this->form->validationStateOn === ActiveForm::VALIDATION_STATE_ON_INPUT) {
      $this->addErrorClassIfNeeded($options);
    }

    $this->addAriaAttributes($options);
    $this->adjustLabelFor($options);

    if ($enclosedByLabel) {
      $this->parts['{input}'] = Html::activeCheckbox($this->model, $this->attribute, $options);
      $this->parts['{label}'] = '';
    } else {
      if (isset($options['label']) && ! isset($this->parts['{label}'])) {
        $this->parts['{label}'] = $options['label'];
        if (! empty($options['labelOptions'])) {
          $this->labelOptions = $options['labelOptions'];
        }
      }
      unset($options['labelOptions']);
      $options['label'] = null;
      $this->parts['{input}'] = Html::activeCheckbox($this->model, $this->attribute, $options);
    }

    return $this;

  }

}
