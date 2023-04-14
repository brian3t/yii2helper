<?php

/**
 * @copyright  Copyright &copy; Kartik Visweswaran, Krajee.com, 2015 - 2022
 * @package    yii2-widgets
 * @subpackage yii2-widget-activeform
 * @version    1.6.2
 */

namespace soc\yii2helper\yii2_widget_activeform;

use Exception;
use kartik\base\BootstrapInterface;
use kartik\base\BootstrapTrait;
use kartik\form\ActiveFormAsset;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm as YiiActiveForm;
use kartik\form\ActiveForm as KartikActiveForm;

/**
 * @method ActiveField field(Model $model, string $attribute, array $options = [])
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com> Brian3t
 * @since  1.0
 */
class ActiveForm extends KartikActiveForm
{
    /**
     * @inheritdoc
     */
//    public $fieldClass = 'kartik\form\ActiveField';
    public $fieldClass = 'soc\yii2helper\yii2_widget_activeform\ActiveField';//b3t

}
