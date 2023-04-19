<?php

/**
 * ActiveField
 * 2/3
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
 * @author Brian3t
 * @since  1.0
 */
class ActiveField extends KartikActiveField
{
  use AddonTrait;

  /**
   * @var string an empty string value
   */
  const NOT_SET = '';

  /**
   * @var string HTML radio input type
   */
  const TYPE_RADIO = 'radio';

  /**
   * @var string HTML checkbox input type
   */
  const TYPE_CHECKBOX = 'checkbox';

  /**
   * @var string the default height for the Krajee multi select input
   */
  const MULTI_SELECT_HEIGHT = '145px';

  /**
   * @var string default hint type that is displayed below the input
   */
  const HINT_DEFAULT = 1;

  /**
   * @var string special hint type that allows display via an indicator icon or on hover/click of the field label
   */
  const HINT_SPECIAL = 2;

  /**
   * @var array the list of hint keys that will be used by ActiveFieldHint jQuery plugin
   */
  protected static $_pluginHintKeys = [
    'iconCssClass',
    'labelCssClass',
    'contentCssClass',
    'hideOnEscape',
    'hideOnClickOut',
    'title',
    'placement',
    'container',
    'animation',
    'delay',
    'template',
    'selector',
    'viewport',
  ];

  /**
   * @var boolean whether to override the form layout styles and skip field formatting as per the form layout.
   */
  public $skipFormLayout = false;

  /**
   * @var bool whether to auto offset toggle inputs (checkboxes / radios) horizontal form layout for BS 4.x forms.
   */
  public $autoOffset = true;

  /**
   * @var bool whether to render the wrapper in the template if [[wrapperOptions]] is empty.
   */
  public $renderEmptyWrapper = false;

  /**
   * @inheritdoc
   */
  public $labelOptions = [];

  /**
   * @var integer the hint display type. If set to `self::HINT_DEFAULT`, the hint will be displayed as a text block below
   */
  public $hintType = self::HINT_DEFAULT;

  /**
   * @var array the settings for displaying the hint. These settings are parsed only if `hintType` is set to
   */
  public $hintSettings = [];

  /**
   * @var array the feedback icon configuration (applicable for [bootstrap text inputs](http://getbootstrap.com/css/#with-optional-icons)).
   */
  public $feedbackIcon = [];

  /**
   * @var string content to be placed before field within the form group at the beginning
   */
  public $contentBeforeField = '';

  /**
   * @var string content to be placed after field within the form group at the end
   */
  public $contentAfterField = '';

  /**
   * @var string content to be placed before label
   */
  public $contentBeforeLabel = '';

  /**
   * @var string content to be placed after label
   */
  public $contentAfterLabel = '';

  /**
   * @var string content to be placed before input
   */
  public $contentBeforeInput = '';

  /**
   * @var string content to be placed after input
   */
  public $contentAfterInput = '';

  /**
   * @var string content to be placed before error block
   */
  public $contentBeforeError = '';

  /**
   * @var string content to be placed after error block
   */
  public $contentAfterError = '';

  /**
   * @var string content to be placed before hint block
   */
  public $contentBeforeHint = '';

  /**
   * @var string content to be placed after hint block
   */
  public $contentAfterHint = '';

  /**
   * @var string the template for rendering the Bootstrap 4.x custom file browser control
   * @see https://getbootstrap.com/docs/4.1/components/forms/#file-browser
   */
  public $customFileTemplate = "<div class=\"custom-file\">\n{input}\n{label}\n</div>\n{error}\n{hint}";

  /**
   * @var string the template for rendering checkboxes and radios for a default Bootstrap markup without an enclosed
   * label
   */
  public $checkTemplate = "{input}\n{label}\n{error}\n{hint}";

  /**
   * @var string the template for rendering checkboxes and radios for a default Bootstrap markup with an enclosed
   */
  public $checkEnclosedTemplate = "{beginLabel}\n{input}\n{labelTitle}\n{endLabel}\n{error}\n{hint}";

  /**
   * @var array the HTML attributes for the container wrapping BS4 checkbox or radio controls within which the content
   */
  public $checkWrapperOptions = [];

  /**
   * @var bool whether to highlight error and success states on input group addons automatically
   */
  public $highlightAddon = true;

  /**
   * @var string CSS classname to add to the input
   */
  public $addClass = 'form-control';

  /**
   * @var string the static value for the field to be displayed for the static input OR when the form is in
   */
  public $staticValue;

  /**
   * @var boolean|string whether to show labels for the field. Should be one of the following values:
   */
  public $showLabels;

  /**
   * @var boolean whether to show errors for the field
   */
  public $showErrors;

  /**
   * @var boolean whether to show hints for the field
   */
  public $showHints;

  /**
   * @var boolean whether to show required asterisk/star indicator after each field label when the model attribute is
   */
  public $showRequiredIndicator = true;

  /**
   * @var boolean whether the label is to be hidden and auto-displayed as a placeholder
   */
  public $autoPlaceholder;

  /**
   * @var array options for the wrapper tag, used in the `{beginWrapper}` token within [[template]].
   */
  public $wrapperOptions = [];

  /**
   * @var string inherits and overrides values from parent class. The value can be overridden within
   */
  public $template = "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}";

  /**
   *
   * @var integer the bootstrap grid column width (usually between 1 to 12)
   */
  public $labelSpan;

  /**
   *
   * @var string one of the bootstrap sizes (refer the ActiveForm::SIZE constants)
   */
  public $deviceSize;

  /**
   * @var boolean whether to render the error. Default is `true` except for layout `inline`.
   */
  public $enableError;

  /**
   * @var boolean whether to render the label. Default is `true`.
   */
  public $enableLabel;

  /**
   * @var null|array CSS grid classes for horizontal layout. This must be an array with these keys:
   */
  public $horizontalCssClasses;

  /**
   * @var boolean whether the input is to be offset (like for checkbox or radio).
   */
  protected $_offset = false;

  /**
   * @var boolean the container for multi select
   */
  protected $_multiselect = '';

  /**
   * @var boolean is it a static input
   */
  protected $_isStatic = false;

  /**
   * @var array the settings for the active field layout
   */
  protected $_settings = [
    'input' => '{input}',
    'error' => '{error}',
    'hint' => '{hint}',
    'showLabels' => true,
    'showErrors' => true,
    'labelSpan' => ActiveForm::DEFAULT_LABEL_SPAN,
    'deviceSize' => ActiveForm::SIZE_MEDIUM,
  ];

  /**
   * @var boolean whether there is a feedback icon configuration set
   */
  protected $_hasFeedback = false;

  /**
   * @var boolean whether there is a feedback icon configuration set
   */
  protected $_isHintSpecial = false;

  /**
   * @var string the label additional css class for horizontal forms and special inputs like checkbox and radio.
   */
  private $_labelCss;

  /**
   * @var string the input container additional css class for horizontal forms and special inputs like checkbox and
   * radio.
   */
  private $_inputCss;

  /**
   * @var boolean whether the hint icon is beside the input.
   */
  private $_iconBesideInput = false;

  /**
   * @var string the identifier for the hint popover container.
   */
  private $_hintPopoverContainer;

  public function __construct($config = []) {
    $layoutConfig = $this->createLayoutConfig($config);
    $config = ArrayHelper::merge($layoutConfig, $config);
    /*
    if ($config['form']->type === ActiveForm::TYPE_HORIZONTAL) {
        unset($config['model'], $config['form']);
        die('<p>Layout Config</p><pre>'.print_r($layoutConfig, true).'</pre><p>Config</p><pre>'.print_r($config,
                true).'</pre>');
    }
    */
    parent::__construct($config);
  }

    /**
     * Create layout specific configuration
     * @param  array  $instanceConfig  the configuration passed to this instance's constructor
     * @return array the layout specific default configuration for this instance
     */
    protected function createLayoutConfig($instanceConfig = [])
    {
        $form = $instanceConfig['form'];
        $layout = $form->type;
        $bsVer = $form->getBsVer();
        $config = [
            'hintOptions' => ['tag' => 'div', 'class' => ['hint-block']],
            'errorOptions' => ['tag' => 'div', 'class' => 'invalid-feedback'],
            'inputOptions' => ['class' => 'form-control'],
            'labelOptions' => ['class' => ['form-label']],
            'options' => ['class' => $bsVer === 5 ? 'mb-3' : 'form-group'],
        ];
        if ($bsVer === 4) {
            $config['labelOptions'] = ['class' => []];
        } elseif ($bsVer === 3) {
            $config['errorOptions'] = ['tag' => 'div', 'class' => 'help-block help-block-error'];
        }
        if ($layout === ActiveForm::TYPE_HORIZONTAL) {
            $config['template'] = "{label}\n{beginWrapper}\n{input}\n{error}\n{hint}\n{endWrapper}";
            $config['wrapperOptions'] = $config['labelOptions'] = [];
            $cssClasses = [
                'offset' => $bsVer === 3 ? 'col-sm-offset-3' : ['col-sm-10', 'offset-sm-2'],
                'field' => $bsVer > 3 ? 'row' : 'form-group',
            ];
            if (isset($instanceConfig['horizontalCssClasses'])) {
                $cssClasses = ArrayHelper::merge($cssClasses, $instanceConfig['horizontalCssClasses']);
            }
            $config['horizontalCssClasses'] = $cssClasses;
            foreach (array_keys($cssClasses) as $cfg) {
                $key = $cfg === 'field' ? 'options' : "{$cfg}Options";
                if ($cfg !== 'offset' && !empty($cssClasses[$cfg])) {
                    Html::addCssClass($config[$key], $cssClasses[$cfg]);
                }
            }
        } elseif ($layout === ActiveForm::TYPE_INLINE) {
            $config['inputOptions']['placeholder'] = true;
            Html::addCssClass($config['options'], 'col-12');
            Html::addCssClass($config['labelOptions'], ['screenreader' => $form->getSrOnlyCss()]);
        } elseif ($bsVer === 5 && $layout === ActiveForm::TYPE_FLOATING) {
            $config['inputOptions']['placeholder'] = true;
            $config['template'] = "{input}\n{label}\n{error}\n{hint}";
            Html::addCssClass($config['options'], ['layout' => 'form-floating mt-3']);
        }

        return $config;
    }

    /**
     * @inheritdoc
     */
   public function begin()
    {
        if ($this->_hasFeedback) {
            Html::addCssClass($this->options, 'has-feedback');
        }

        return parent::begin().$this->contentBeforeField;
    }

    /**
     * @inheritdoc
     */
    public function end()
    {
        return $this->contentAfterField.parent::end();
    }

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->initActiveField();
    }

    /**
     * Renders a list of checkboxes. A checkbox list allows multiple selection, like [[listBox()]]. As a result, the
     * corresponding submitted value is an array. The selection of the checkbox list is taken from the value of the
     * model attribute.
     *
     * @param  array  $items  the data item used to generate the checkboxes. The array values are the labels, while the
     * array keys are the corresponding checkbox values. Note that the labels will NOT be HTML-encoded, while the
     * values will be encoded.
     * @param  array  $options  options (name => config) for the checkbox list. The following options are specially
     * handled:
     *
     * - `custom`: _bool_, whether to render bootstrap 4.x custom checkbox/radio styled control. Defaults to `false`.
     *    This is applicable only for Bootstrap 4.x forms.
     * @return ActiveField object
     * @throws InvalidConfigException
     * @see https://getbootstrap.com/docs/4.1/components/forms/#checkboxes-and-radios-1
     * - `unselect`: _string_, the value that should be submitted when none of the checkboxes is selected. By setting this
     *   option, a hidden input will be generated.
     * - `separator`: _string_, the HTML code that separates items.
     * - `inline`: _boolean_, whether the list should be displayed as a series on the same line, default is false
     * - `item: callable, a callback that can be used to customize the generation of the HTML code corresponding to a
     *   single item in $items. The signature of this callback must be:
     * ~~~
     * function ($index, $label, $name, $checked, $value)
     * ~~~
     *
     * where `$index` is the zero-based index of the checkbox in the whole list; `$label` is the label for the checkbox;
     * and `$name`, `$value` and `$checked` represent the name, value and the checked status of the checkbox input.
     *
     */
    public function checkboxList($items, $options = [])
    {
        return $this->getToggleFieldList(self::TYPE_CHECKBOX, $items, $options);
    }

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function dropDownList($items, $options = [])
    {
        $this->initDisability($options);
        $newBsCss = ($this->form->isBs(5) ? 'form' : 'custom').'-select';
        $class = $this->isCustomControl($options) ? $newBsCss : $this->addClass;
        Html::addCssClass($options, $class);

        return parent::dropDownList($items, $options);
    }

    /**
     * @inheritdoc
     */
    public function hint($content, $options = [])
    {
        if ($this->getConfigParam('showHints') === false) {
            $this->parts['{hint}'] = '';
            return $this;
        }
        if ($this->_isHintSpecial) {
            Html::addCssClass($options, 'kv-hint-block');
        }
        return parent::hint($this->generateHint($content), $options);
    }

    /**
     * Checks whether bootstrap 4.x custom control based on `options` parameter
     * @param  array  $options  HTML attributes for the control
     * @return bool
     * @throws InvalidConfigException|Exception
     */
    protected function isCustomControl(&$options)
    {
        return ArrayHelper::remove($options, 'custom', false) && !$this->form->isBs(3);
    }

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function fileInput($options = [])
    {
        $isBs5 = $this->form->isBs(5);
        if ($this->isCustomControl($options)) {
            if (!$isBs5) {
                $view = $this->form->getView();
                Bs4CustomFileInputAsset::register($view);
                $view->registerJs('bsCustomFileInput.init();');
                Html::removeCssClass($options, 'form-control');
                Html::addCssClass($options, 'custom-file-input');
                Html::addCssClass($this->labelOptions, 'custom-file-label');
                $this->template = $this->customFileTemplate;
            } else {
                Html::addCssClass($options, 'form-control');
            }
        } elseif ($isBs5) {
            Html::removeCssClass($options, 'form-control');
        }

        return parent::fileInput($options);
    }

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function input($type, $options = [])
    {
        $this->initFieldOptions($options);
        if ($this->isCustomControl($options) && $type === 'range') {
            Html::addCssClass($options, $this->getCustomCss('range'));
        }
        if ($type !== 'range' && $type !== 'color') {
            Html::addCssClass($options, $this->addClass);
        }
        $this->initDisability($options);

        return parent::input($type, $options);
    }

    /**
     * @inheritdoc
     */
    public function label($label = null, $options = [])
    {
        $hasLabels = $this->hasLabels();
        $processLabels = $label !== false && $this->_isHintSpecial && $hasLabels !== false &&
            $hasLabels !== ActiveForm::SCREEN_READER && ($this->getHintData('onLabelClick') || $this->getHintData(
                    'onLabelHover'
                ));
        if ($processLabels) {
            if ($label === null) {
                $label = $this->model->getAttributeLabel($this->attribute);
            }
            $opts = ['class' => 'kv-type-label'];
            Html::addCssClass($opts, $this->getHintIconCss('Label'));
            $label = Html::tag('span', $label, $opts);
            if ($this->getHintData('showIcon') && !$this->getHintData('iconBesideInput')) {
                $label = Lib::strtr(
                    $this->getHintData('labelTemplate'),
                    ['{label}' => $label, '{help}' => $this->getHintIcon()]
                );
            }
        }
        if (Lib::strpos($this->template, '{beginLabel}') !== false) {
            $this->renderLabelParts($label, $options);
        }
        if ($this->_offset) {
            $label = '';
        }

        return parent::label($label, $options);
    }

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function listBox($items, $options = [])
    {
        $this->initDisability($options);
        $newBsCss = $this->getCustomCss('select');
        $class = $this->isCustomControl($options) ? $newBsCss : $this->addClass;
        Html::addCssClass($options, $class);

        return parent::listBox($items, $options);
    }

    /**
     * Gets custom CSS for custom controls supported in bootstrap 4.x and 5.x
     * @param  string  $type
     * @return string
     * @throws Exception
     */
    protected function getCustomCss($type)
    {
        return $this->form->isBs(5) ? "form-{$type}" : "custom-{$type}";
    }

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function passwordInput($options = [])
    {
        $this->initFieldOptions($options);
        Html::addCssClass($options, $this->addClass);
        $this->initDisability($options);

        return parent::passwordInput($options);
    }

    /**
     * Renders a radio button. This method will generate the "checked" tag attribute according to the model attribute
     * value.
     *
     * @param  array  $options  the tag options in terms of name-value pairs. The following options are specially
     * handled:
     *
     * - `custom`: _bool_, whether to render bootstrap 4.x custom radio styled control. Defaults to `false`.
     *    This is applicable only for Bootstrap 4.x forms.
     * @param  bool|null  $enclosedByLabel  whether to enclose the radio within the label. If `true`, the method will still
     * use [[template]] to layout the checkbox and the error message except that the radio is enclosed by the label tag.
     *
     * @return ActiveField object
     * @throws InvalidConfigException
     * @see https://getbootstrap.com/docs/4.1/components/forms/#checkboxes-and-radios-1
     * - `uncheck`: _string_, the value associated with the uncheck state of the radio button. If not set, it will take the
     *   default value '0'. This method will render a hidden input so that if the radio button is not checked and is
     *   submitted, the value of this attribute will still be submitted to the server via the hidden input.
     * - `label`: _string_, a label displayed next to the radio button. It will NOT be HTML-encoded. Therefore you can pass
     *   in HTML code such as an image tag. If this is is coming from end users, you should [[Html::encode()]] it to
     *   prevent XSS attacks. When this option is specified, the radio button will be enclosed by a label tag.
     * - `labelOptions`: _array_, the HTML attributes for the label tag. This is only used when the "label" option is
     *   specified.
     * - `container: boolean|array, the HTML attributes for the checkbox container. If this is set to false, no
     *   container will be rendered. The special option `tag` will be recognized which defaults to `div`. This
     *   defaults to: `['tag' => 'div', 'class'=>'radio']`
     * The rest of the options will be rendered as the attributes of the resulting tag. The values will be HTML-encoded
     *   using [[Html::encode()]]. If a value is null, the corresponding attribute will not be rendered.
     *
     */
    public function radio($options = [], $enclosedByLabel = null)
    {
        return $this->getToggleField(self::TYPE_RADIO, $options, $enclosedByLabel);
    }

    /**
     * Renders a list of radio buttons. A radio button list is like a checkbox list, except that it only allows single
     * selection. The selection of the radio buttons is taken from the value of the model attribute.
     *
     * @param  array  $items  the data item used to generate the radio buttons. The array keys are the labels, while the
     * array values are the corresponding radio button values. Note that the labels will NOT be HTML-encoded, while
     * the values will.
     * @param  array  $options  options (name => config) for the radio button list. The following options are specially
     * handled:
     *
     *
     * - `custom`: _bool_, whether to render bootstrap 4.x custom checkbox/radio styled control. Defaults to `false`.
     *    This is applicable only for Bootstrap 4.x forms.
     * @return ActiveField object
     * @throws InvalidConfigException
     * @see https://getbootstrap.com/docs/4.1/components/forms/#checkboxes-and-radios-1
     * - `unselect`: _string_, the value that should be submitted when none of the radio buttons is selected. By setting
     *   this option, a hidden input will be generated.
     * - `separator`: _string_, the HTML code that separates items.
     * - `inline`: _boolean_, whether the list should be displayed as a series on the same line, default is false
     * - `item: callable, a callback that can be used to customize the generation of the HTML code corresponding to a
     *   single item in $items. The signature of this callback must be:
     *
     * ~~~
     * function ($index, $label, $name, $checked, $value)
     * ~~~
     *
     * where `$index` is the zero-based index of the radio button in the whole list; `$label` is the label for the radio
     * button; and `$name`, `$value` and `$checked` represent the name, value and the checked status of the radio button
     * input.
     *
     */
    public function radioList($items, $options = [])
    {
        return $this->getToggleFieldList(self::TYPE_RADIO, $items, $options);
    }

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function render($content = null)
    {
        if ($this->getConfigParam('showHints') === false) {
            $this->hintOptions['hint'] = '';
        } else {
            if ($content === null && !isset($this->parts['{hint}']) && !isset($this->hintOptions['hint'])) {
                $this->hintOptions['hint'] = $this->generateHint();
            }
            $this->template = Lib::strtr($this->template, ['{hint}' => $this->_settings['hint']]);
        }

        if ($this->form->staticOnly === true) {
            $this->buildTemplate();
            $this->staticInput();
        } else {
            $this->initFieldOptions($this->inputOptions);
            $this->initDisability($this->inputOptions);
            $this->buildTemplate();
        }

        return parent::render($content);
    }

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function textarea($options = [])
    {
        $this->initFieldOptions($options);
        Html::addCssClass($options, $this->addClass);
        $this->initDisability($options);

        return parent::textarea($options);
    }

    /**
     * @inheritdoc
     */
    public function widget($class, $config = [])
    {
        if (property_exists($class, 'disabled') && property_exists($class, 'readonly')) {
            $this->initDisability($config);
        }

        return parent::widget($class, $config);
    }

  /**
   * Adds Bootstrap 4 validation class to the input options if needed.
   * @param array $options
   * @throws Exception
   */
  protected function addErrorClassBS4(&$options) {
    $attributeName = Html::getAttributeName($this->attribute);
    if (! $this->form->isBs(3) &&
      $this->model->hasErrors($attributeName) &&
      $this->form->validationStateOn === ActiveForm::VALIDATION_STATE_ON_CONTAINER) {
      Html::addCssClass($options, 'is-invalid');
    }
  }

    /**
     * Gets configuration parameter from formConfig
     *
     * @param  string  $param  the parameter name
     * @param  mixed  $default  the default parameter value
     *
     * @return string|bool the parsed parameter value
     * @throws Exception
     */
    protected function getConfigParam($param, $default = true)
    {
        return isset($this->$param) ? $this->$param : ArrayHelper::getValue($this->form->formConfig, $param, $default);
    }

    /**
     * Generates the hint.
     *
     * @param  string  $content  the hint content
     *
     * @return string
     */
    protected function generateHint($content = null)
    {
        if ($content === null && method_exists($this->model, 'getAttributeHint')) {
            $content = $this->model->getAttributeHint($this->attribute);
        }

        return $this->contentBeforeHint.$content.$this->contentAfterHint;
    }

    /**
     * Initialize the active field
     * @throws InvalidConfigException
     */
    protected function initActiveField()
    {
        if (isset($this->enableError)) {
            $this->showErrors = $this->enableError;
        }
        if (isset($this->enableLabel)) {
            $this->showLabels = $this->enableLabel;
        }
        $bsVer = $this->form->getBsVer();
        $isInline = $this->form->isInline();
        $isHorizontal = $this->form->isHorizontal();
        if ($bsVer > 3) {
            $errCss = $this->form->tooltipStyleFeedback ? 'invalid-tooltip' : 'invalid-feedback';
            Html::addCssClass($this->errorOptions, $errCss);
        }
        $showLabels = $this->getConfigParam('showLabels');
        $this->_isHintSpecial = $this->hintType === self::HINT_SPECIAL;
        if ($isInline && !isset($this->autoPlaceholder) && $showLabels !== true) {
            $this->autoPlaceholder = true;
        } elseif (!isset($this->autoPlaceholder)) {
            $this->autoPlaceholder = false;
        }
        if (!isset($this->labelOptions['class']) && ($isHorizontal || $bsVer === 3 && !$isInline)) {
            $this->labelOptions['class'] = $this->form->getCssClass(ActiveForm::BS_CONTROL_LABEL);
        }
        if ($showLabels === ActiveForm::SCREEN_READER) {
            Html::addCssClass($this->labelOptions, $this->form->getSrOnlyCss());
        }
        if ($this->showRequiredIndicator) {
            Html::addCssClass($this->labelOptions, 'has-star');
        }
        if ($this->highlightAddon) {
            Html::addCssClass($this->options, 'highlight-addon');
        }
        if ($isHorizontal) {
            $this->initHorizontal();
        }
        $this->initLabels();
        $this->initHints();
        $this->_hasFeedback = !empty($this->feedbackIcon) && is_array($this->feedbackIcon);
        $this->_iconBesideInput = (bool)ArrayHelper::getValue($this->hintSettings, 'iconBesideInput');
        if ($this->_iconBesideInput) {
            $id = ArrayHelper::getValue($this->options, 'id', '');
            $this->_hintPopoverContainer = $id ? "#{$id}-table" : '';
        } else {
            $id = ArrayHelper::getValue($this->form->options, 'id', '');
            $this->_hintPopoverContainer = $id ? "#{$id}" : '';
        }
    }

    /**
     * Initialize label options
     */
    protected function initLabels()
    {
        $labelCss = $this->_labelCss;
        if ($this->hasLabels() === ActiveForm::SCREEN_READER) {
            Html::addCssClass($this->labelOptions, $this->form->getSrOnlyCss());
        } elseif ($labelCss != self::NOT_SET) {
            Html::addCssClass($this->labelOptions, $labelCss);
        }
    }

    /**
     * Validate label display status
     *
     * @return boolean|string whether labels are to be shown
     * @throws Exception
     */
    protected function hasLabels()
    {
        $showLabels = $this->getConfigParam('showLabels'); // plus abfrage $this-showLabels kombinieren.
        if ($this->autoPlaceholder && $showLabels !== ActiveForm::SCREEN_READER) {
            $showLabels = false;
        }

        return $showLabels;
    }

    /**
     * Prepares bootstrap grid col classes for horizontal layout including label and input tags and initiate private
     * CSS variables. The process order for 'labelSpan' and 'wrapper' is as follows:
     *
     * - Step 1: Check `$labelSpan` and `$deviceSize`.
     * - Step 2: Check `formConfig(['labelSpan' => x, 'deviceSize' => xy]) and build css tag.
     * - If `horizontalCssClasses['wrapper']` is set and no 'col-' tag then add this to css tag from Step 1.
     * - If `horizontalCssClasses['wrapper']` is set and wrapper has 'col-' tag then override css tag completely.
     * - If no `$labelSpan` and no `horizontalCssClasses['wrapper']` is set then use default from [[$_settings]].
     *   Similar behavior to `horizontalCssClasses['label']`.
     * @throws InvalidConfigException
     */
    protected function initHorizontal()
    {
        $hor = $this->horizontalCssClasses;
        $span = $this->getConfigParam('labelSpan', '');
        $size = $this->getConfigParam('deviceSize', '');
        $bsVer = $this->form->getBsVer();
        if ($bsVer > 3) {
            Html::addCssClass($this->options, 'row');
        }
        // check horizontalCssClasses['wrapper'] if there is a col- class
        if (isset($hor['wrapper']) && Lib::strpos($hor['wrapper'], 'col-') !== false) {
            $span = '';
        }
        if (empty($span) && !isset($hor['wrapper'])) {
            $span = $this->_settings['labelSpan'];
        }
        if (empty($size)) {
            $size = ArrayHelper::getValue($this->_settings, 'deviceSize');
        }
        $this->deviceSize = $size;
        if (empty($span)) {
            $span = ActiveForm::DEFAULT_LABEL_SPAN;
        }
        if ($span != self::NOT_SET && intval($span) > 0) {
            $span = intval($span);

            // validate if invalid labelSpan is passed - else set to DEFAULT_LABEL_SPAN
            if ($span <= 0 || $span >= $this->form->fullSpan) {
                $span = $this->form->fullSpan;
            }

            // validate if invalid deviceSize is passed - else default to SIZE_MEDIUM
            $sizes = [ActiveForm::SIZE_TINY, ActiveForm::SIZE_SMALL, ActiveForm::SIZE_MEDIUM, ActiveForm::SIZE_LARGE];
            if ($size == self::NOT_SET || !in_array($size, $sizes)) {
                $size = ActiveForm::SIZE_MEDIUM;
            }

            $this->labelSpan = $span;
            $prefix = $this->getColCss($size);
            $this->_labelCss = $prefix.$span;
            $this->_inputCss = $prefix.($this->form->fullSpan - $span);
        }

        if (isset($hor['wrapper'])) {
            if ($span !== self::NOT_SET) {
                $this->_inputCss .= " ";
            }
            $this->_inputCss .= implode(' ', (array)$hor['wrapper']);
        }

        if (isset($hor['label'])) {
            if ($span !== self::NOT_SET) {
                $this->_labelCss .= " ";
            }
            $this->_labelCss .= implode(' ', (array)$hor['label']);
        }

        if (isset($hor['error'])) {
            Html::addCssClass($this->errorOptions, $hor['error']);
        }
    }

  /**
   * Initialize layout settings for label, input, error and hint blocks and for various bootstrap 3 form layouts
   * @throws InvalidConfigException
   */
  protected function initLayout() {
    $showLabels = $this->hasLabels();
    $showErrors = $this->getConfigParam('showErrors');
    $this->mergeSettings($showLabels, $showErrors);
    $this->buildLayoutParts($showLabels, $showErrors);
  }

    /**
     * Merges the parameters for layout settings
     *
     * @param  boolean  $showLabels  whether to show labels
     * @param  boolean  $showErrors  whether to show errors
     */
    protected function mergeSettings($showLabels, $showErrors)
    {
        $this->_settings['showLabels'] = $showLabels;
        $this->_settings['showErrors'] = $showErrors;
    }

  /**
   * Builds the field layout parts
   *
   * @param boolean $showLabels whether to show labels
   * @param boolean $showErrors whether to show errors
   * @throws InvalidConfigException
   */
  protected function buildLayoutParts($showLabels, $showErrors) {
    if (! $showErrors) {
      $this->_settings['error'] = '';
    }
    if ($this->skipFormLayout) {
      $this->mergeSettings($showLabels, $showErrors);
      $this->parts['{beginWrapper}'] = '';
      $this->parts['{endWrapper}'] = '';
      $this->parts['{beginLabel}'] = '';
      $this->parts['{labelTitle}'] = '';
      $this->parts['{endLabel}'] = '';

      return;
    }
    if (! empty($this->_inputCss)) {
      $inputDivClass = $this->_inputCss;
      if ($showLabels === false || $showLabels === ActiveForm::SCREEN_READER) {
        $inputDivClass = $this->getColCss($this->deviceSize) . $this->form->fullSpan;
      }
      Html::addCssClass($this->wrapperOptions, $inputDivClass);
    }
    if (! isset($this->parts['{beginWrapper}'])) {
      if ($this->renderEmptyWrapper || ! empty($this->wrapperOptions)) {
        $options = $this->wrapperOptions;
        $tag = ArrayHelper::remove($options, 'tag', 'div');
        $this->parts['{beginWrapper}'] = Html::beginTag($tag, $options);
        $this->parts['{endWrapper}'] = Html::endTag($tag);
      } else {
        $this->parts['{beginWrapper}'] = $this->parts['{endWrapper}'] = '';
      }
    }
    $this->mergeSettings($showLabels, $showErrors);
  }

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
    public function checkbox($options = [], $enclosedByLabel = null)
    {
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
            if (isset($options['label']) && !isset($this->parts['{label}'])) {
                $this->parts['{label}'] = $options['label'];
                if (!empty($options['labelOptions'])) {
                    $this->labelOptions = $options['labelOptions'];
                }
            }
            unset($options['labelOptions']);
            $options['label'] = null;
            $this->parts['{input}'] = Html::activeCheckbox($this->model, $this->attribute, $options);
        }

        return $this;

    }

  /**
   * Validates and sets disabled or readonly inputs
   *
   * @param array $options the HTML attributes for the input
   */
  protected function initDisability(&$options) {
    if ($this->form->disabled && ! isset($options['disabled'])) {
      $options['disabled'] = true;
    }
    if ($this->form->readonly && ! isset($options['readonly'])) {
      $options['readonly'] = true;
    }
  }

    /**
     * Initialize hint settings
     * @throws InvalidConfigException|Exception
     */
    protected function initHints()
    {
        if ($this->hintType !== self::HINT_SPECIAL) {
            return;
        }
        $container = $this->_hintPopoverContainer;
        if ($container === '') {
            $container = $this->_iconBesideInput ? 'table' : 'form';
        }
        $iconCss = !$this->form->isBs(3) ? 'fas fa fa-question-circle' : 'glyphicon glyphicon-question-sign';
        $attr = 'style="width:100%"{id}';
        $defaultSettings = [
            'showIcon' => true,
            'iconBesideInput' => false,
            'labelTemplate' => '{label}{help}',
            'inputTemplate' => "<table {$attr}><tr><td>{input}</td>".'<td style="width:5%">{help}</td></tr></table>',
            'onLabelClick' => false,
            'onLabelHover' => true,
            'onIconClick' => true,
            'onIconHover' => false,
            'labelCssClass' => 'kv-hint-label',
            'iconCssClass' => 'kv-hint-icon',
            'contentCssClass' => 'kv-hint-content',
            'icon' => '<i class="'.$iconCss.' text-info"></i>',
            'hideOnEscape' => true,
            'hideOnClickOut' => true,
            'placement' => 'top',
            'container' => $container,
            'viewport' => ['selector' => 'body', 'padding' => 0],
        ];
        $this->hintSettings = array_replace_recursive($defaultSettings, $this->hintSettings);
        Html::addCssClass($this->options, 'kv-hint-special');
        foreach (static::$_pluginHintKeys as $key) {
            $this->setHintData($key);
        }
    }

    /**
     * Sets a hint property setting as a data attribute within `self::$options`
     *
     * @param  string  $key  the hint property key
     */
    protected function setHintData($key)
    {
        if (isset($this->hintSettings[$key])) {
            $value = $this->hintSettings[$key];
            $this->options['data-'.Inflector::camel2id($key)] = is_bool($value) ? (int)$value : $value;
        }
    }

  /**
   * Initializes sizes and placeholder based on $autoPlaceholder
   *
   * @param array $options the HTML attributes for the input
   * @throws InvalidConfigException
   */
  protected function initFieldOptions(&$options) {
    $this->initFieldSize($options, 'lg');
    $this->initFieldSize($options, 'sm');
    if ($this->autoPlaceholder) {
      $label = $this->model->getAttributeLabel(Html::getAttributeName($this->attribute));
      $this->inputOptions['placeholder'] = $label;
      $options['placeholder'] = $label;
    }
    $this->addErrorClassBS4($options);
  }

  /**
   * Initializes field by detecting the bootstrap CSS size and sets a size modifier CSS to the field container
   * @param array $options the HTML options
   * @param string $size the size to init
   * @throws InvalidConfigException|Exception
   */
  protected function initFieldSize($options, $size) {
    $notBs3 = ! $this->form->isBs(3);
    if ($notBs3 && Config::hasCssClass($options, "form-control-{$size}") ||
      ! $notBs3 && Config::hasCssClass($options, "input-{$size}") ||
      isset($this->addon['groupOptions']) &&
      Config::hasCssClass($this->addon['groupOptions'], "input-group-{$size}")) {
      Html::addCssClass($this->options, "has-size-{$size}");
    }
  }

    /**
     * Gets a hint configuration setting value
     *
     * @param  string  $key  the hint setting key to fetch
     * @param  mixed  $default  the default value if not set
     *
     * @return mixed
     * @throws Exception
     */
    protected function getHintData($key, $default = null)
    {
        return ArrayHelper::getValue($this->hintSettings, $key, $default);
    }

    /**
     * Gets the hint icon css based on `hintSettings`
     *
     * @param  string  $type  whether `Label` or `Icon`
     *
     * @return array the css to be applied
     */
    protected function getHintIconCss($type)
    {
        $css = ["kv-hintable"];
        if ($type === 'Icon') {
            $css[] = 'hide';
        }
        if (!empty($this->hintSettings["on{$type}Click"])) {
            $css[] = "kv-hint-click";
        }
        if (!empty($this->hintSettings["on{$type}Hover"])) {
            $css[] = "kv-hint-hover";
        }

        return $css;
    }

    /**
     * Builds the final template based on the bootstrap form type, display settings for label, error, and hint, and
     * content before and after label, input, error, and hint.
     * @throws InvalidConfigException
     */
    protected function buildTemplate()
    {
        $showLabels = $showErrors = $input = $error = null;
        extract($this->_settings);
        if ($this->_isStatic || (isset($this->showErrors) && !$this->showErrors) ||
            (!$this->skipFormLayout && !$this->getConfigParam('showErrors'))) {
            $showErrors = false;
        }
        $showLabels = $showLabels && $this->hasLabels();
        $this->buildLayoutParts($showLabels, $showErrors);
        extract($this->_settings);
        if (!$showErrors) {
            Html::addCssClass($this->options, 'hide-errors');
        }
        if (!empty($this->_multiselect)) {
            $input = Lib::str_replace('{input}', $this->_multiselect, $input);
        }
        if ($this->_isHintSpecial && $this->getHintData('iconBesideInput') && $this->getHintData('showIcon')) {
            $id = $this->_hintPopoverContainer ? ' id="'.$this->_hintPopoverContainer.'"' : '';
            $help = Lib::strtr($this->getHintData('inputTemplate'), ['{help}' => $this->getHintIcon(), '{id}' => $id,]);
            $input = Lib::str_replace('{input}', $help, $input);
        }
        $newInput = $this->contentBeforeInput.$this->generateAddon().$this->renderFeedbackIcon().
            $this->contentAfterInput;
        $newError = "{$this->contentBeforeError}{error}{$this->contentAfterError}";
        $config = [
            '{beginLabel}' => $showLabels ? '{beginLabel}' : "",
            '{endLabel}' => $showLabels ? '{endLabel}' : "",
            '{label}' => $showLabels ? "{$this->contentBeforeLabel}{label}{$this->contentAfterLabel}" : "",
            '{labelTitle}' => $showLabels ? "{$this->contentBeforeLabel}{labelTitle}{$this->contentAfterLabel}" : "",
            '{input}' => Lib::str_replace('{input}', $newInput, $input),
            '{error}' => $showErrors ? Lib::str_replace('{error}', $newError, $error) : '',
        ];
        $this->template = Lib::strtr($this->template, $config);
    }

    /**
     * Generates the addon markup
     *
     * @return string
     * @throws InvalidConfigException|Exception
     */
    protected function generateAddon()
    {
        if (empty($this->addon)) {
            return '{input}';
        }
        $addon = $this->addon;
        $ver = $this->form->getBsVer();
        $prepend = $this->getAddonContent('prepend', $ver);
        $append = $this->getAddonContent('append', $ver);
        $content = $prepend.'{input}'.$append;
        $group = ArrayHelper::getValue($addon, 'groupOptions', []);
        Html::addCssClass($group, 'input-group');
        $contentBefore = ArrayHelper::getValue($addon, 'contentBefore', '');
        $contentAfter = ArrayHelper::getValue($addon, 'contentAfter', '');

        return Html::tag('div', $contentBefore.$content.$contentAfter, $group);
    }

    /**
     * Render the bootstrap feedback icon
     *
     * @see http://getbootstrap.com/css/#with-optional-icons
     *
     * @return string
     * @throws Exception
     */
    protected function renderFeedbackIcon()
    {
        if (!$this->_hasFeedback) {
            return '';
        }
        $config = $this->feedbackIcon;
        $type = ArrayHelper::getValue($config, 'type', 'icon');
        $prefix = ArrayHelper::getValue($config, 'prefix', $this->form->getDefaultIconPrefix());
        $id = Html::getInputId($this->model, $this->attribute);

        return $this->getFeedbackIcon($config, 'default', $type, $prefix, $id).
            $this->getFeedbackIcon($config, 'success', $type, $prefix, $id).
            $this->getFeedbackIcon($config, 'error', $type, $prefix, $id);
    }

    /**
     * Render the label parts
     *
     * @param  string|null  $label  the label or null to use model label
     * @param  array  $options  the tag options
     */
    protected function renderLabelParts($label = null, $options = [])
    {
        $options = array_merge($this->labelOptions, $options);
        if ($label === null) {
            if (isset($options['label'])) {
                $label = $options['label'];
                unset($options['label']);
            } else {
                $attribute = Html::getAttributeName($this->attribute);
                $label = Html::encode($this->model->getAttributeLabel($attribute));
            }
        }
        if (!isset($options['for'])) {
            $options['for'] = Html::getInputId($this->model, $this->attribute);
        }
        $this->parts['{beginLabel}'] = Html::beginTag('label', $options);
        $this->parts['{endLabel}'] = Html::endTag('label');
        if (!isset($this->parts['{labelTitle}'])) {
            $this->parts['{labelTitle}'] = $label;
        }
    }

    /**
     * Generates a feedback icon
     *
     * @param  array  $config  the feedback icon configuration
     * @param  string  $cat  the feedback icon category
     * @param  string  $type  the feedback icon type
     * @param  string  $prefix  the feedback icon prefix
     * @param  string  $id  the input attribute identifier
     *
     * @return string
     * @throws Exception
     */
    protected function getFeedbackIcon($config, $cat, $type, $prefix, $id)
    {
        $markup = ArrayHelper::getValue($config, $cat);
        if ($markup === null) {
            return '';
        }
        $desc = ArrayHelper::remove($options, 'description', "({$cat})");
        $options = ArrayHelper::getValue($config, $cat.'Options', []);
        $options['aria-hidden'] = true;
        $key = $id.'-'.$cat;
        $this->inputOptions['aria-describedby'] = empty($this->inputOptions['aria-describedby']) ? $key :
            $this->inputOptions['aria-describedby'].' '.$key;
        Html::addCssClass($options, ['form-control-feedback', "kv-feedback-{$cat}"]);
        $icon = $type === 'raw' ? $markup : Html::tag('i', '', ['class' => $prefix.$markup]);

        return Html::tag('span', $icon, $options).
            Html::tag('span', $desc, ['id' => $key, 'class' => $this->form->getSrOnlyCss()]);
    }

    /**
     * Renders a list of checkboxes / radio buttons. The selection of the checkbox / radio buttons is taken from the
     * value of the model attribute.
     *
     * @param  string  $type  the toggle input type 'checkbox' or 'radio'.
     * @param  array  $items  the data item used to generate the checkbox / radio buttons. The array keys are the labels,
     * while the array values are the corresponding checkbox / radio button values. Note that the labels will NOT
     * be HTML-encoded, while the values will be encoded.
     * @param  array  $options  options (name => config) for the checkbox / radio button list. The following options are
     * specially handled:
     *
     * - `custom`: _bool_, whether to render bootstrap 4.x custom checkbox/radio styled control. Defaults to `false`.
     *    This is applicable only for Bootstrap 4.x forms.
     * @param  boolean  $asBtnGrp  whether to generate the toggle list as a bootstrap button group
     *
     * @return ActiveField object
     * @throws InvalidConfigException
     * @see https://getbootstrap.com/docs/4.1/components/forms/#checkboxes-and-radios-1
     * - `unselect`: _string_, the value that should be submitted when none of the checkbox / radio buttons is selected. By
     *   setting this option, a hidden input will be generated.
     * - `separator`: _string_, the HTML code that separates items.
     * - `inline`: _boolean_, whether the list should be displayed as a series on the same line, default is false
     * - `disabledItems`: _array_, the list of values that will be disabled.
     * - `readonlyItems`: _array_, the list of values that will be readonly.
     * - `item: callable, a callback that can be used to customize the generation of the HTML code corresponding to a
     *   single item in $items. The signature of this callback must be:
     *
     * ~~~
     * function ($index, $label, $name, $checked, $value)
     * ~~~
     *
     * where $index is the zero-based index of the checkbox/ radio button in the whole list; $label is the label for
     * the checkbox/ radio button; and $name, $value and $checked represent the name, value and the checked status
     * of the checkbox/ radio button input.
     *
     */
    protected function getToggleFieldList($type, $items, $options = [], $asBtnGrp = false)
    {
        $isBs5 = $this->form->isBs(5);
        $notBs3 = !$this->form->isBs(3);
        $disabled = ArrayHelper::remove($options, 'disabledItems', []);
        $readonly = ArrayHelper::remove($options, 'readonlyItems', []);
        $cust = $this->isCustomControl($options);
        $pre = $cust ? ($isBs5 ? 'form-check' : 'custom-control') : ($notBs3 ? "me-1 mr-1 bs-{$type}" : '');
        if ($asBtnGrp) {
            $css = ['btn-group'];
            if (!$isBs5) {
                $css[] = 'btn-group-toggle';
            }
            Html::addCssClass($options, $css);
            $options['data-toggle'] = 'buttons';
            $options['inline'] = true;
            if (!isset($options['itemOptions']['labelOptions']['class'])) {
                $options['itemOptions']['labelOptions']['class'] = 'btn '.$this->form->getDefaultBtnCss();
            }
        }
        $in = ArrayHelper::remove($options, 'inline', false);
        $inputType = "{$type}List";
        $opts = ArrayHelper::getValue($options, 'itemOptions', []);
        $this->initDisability($opts);
        $css = $this->form->disabled ? ' disabled' : '';
        $css .= $this->form->readonly ? ' readonly' : '';
        if ($notBs3) {
            Html::addCssClass($this->labelOptions, 'pt-0');
        }
        if (!$notBs3 && $in && !isset($options['itemOptions']['labelOptions']['class'])) {
            $options['itemOptions']['labelOptions']['class'] = "{$type}-inline{$css}";
        } elseif (!isset($options['item'])) {
            $labelOpts = ArrayHelper::getValue($opts, 'labelOptions', []);
            $options['item'] = function ($index, $label, $name, $checked, $value)
            use (
                $isBs5,
                $type,
                $css,
                $disabled,
                $readonly,
                $asBtnGrp,
                $labelOpts,
                $opts,
                $in,
                $notBs3,
                $cust,
                $pre,
                $options
            ) {
                $id = isset($options['id']) ? $options['id'].'-'.$index :
                    Lib::strtolower(Lib::preg_replace('/[^a-zA-Z0-9=\s—–-]+/u', '-', $name)).'-'.$index;
                $opts += [
                    'data-index' => $index,
                    'value' => $value,
                    'disabled' => $this->form->disabled,
                    'readonly' => $this->form->readonly,
                ];
                $enclosedLabel = (!$cust && !$notBs3) || ($asBtnGrp && !$isBs5);
                if ($enclosedLabel) {
                    $opts += ['label' => $label];
                }
                if (!isset($opts['id'])) {
                    $opts['id'] = $id;
                }
                $wrapperOptions = [];
                if ($notBs3 && !$asBtnGrp) {
                    $opts += ['class' => "{$pre}-input"];
                    Html::addCssClass($labelOpts, "{$pre}-label");
                    $wrapperOptions = ['class' => [$pre.($cust ? ' custom-'.$type : '')]];
                    if ($in) {
                        Html::addCssClass($wrapperOptions, "{$pre}-inline");
                    }
                } elseif (!$notBs3) {
                    $wrapperOptions = ['class' => [$type.$css]];
                }
                if ($asBtnGrp) {
                    if ($checked) {
                        Html::addCssClass($labelOpts, 'active');
                    }
                    $opts['autocomplete'] = 'off';
                }
                if (!empty($disabled) && in_array($value, $disabled) || $this->form->disabled) {
                    Html::addCssClass($labelOpts, 'disabled');
                    $opts['disabled'] = true;
                }
                if (!empty($readonly) && in_array($value, $readonly) || $this->form->readonly) {
                    Html::addCssClass($labelOpts, 'disabled');
                    $opts['readonly'] = true;
                }
                if ($isBs5 && $asBtnGrp) {
                    Html::addCssClass($opts, 'btn-check');
                } else {
                    $opts['labelOptions'] = $labelOpts;
                }
                $out = Html::$type($name, $checked, $opts);
                if (!$enclosedLabel) {
                    $out .= Html::label($label, $opts['id'], $labelOpts);
                }

                return $asBtnGrp ? $out : Html::tag('div', $out, $wrapperOptions);
            };
        }

        return parent::$inputType($items, $options);
    }

    /**
     * Adds Bootstrap 4 validation class to the input options if needed.
     * @param  array  $options
     * @throws Exception
     */
    public function textInput($options = [])
    {
        $this->initFieldOptions($options);
        Html::addCssClass($options, $this->addClass);
        $this->initDisability($options);
        $attr = $this->attribute;
        if (isset($options['has_def_chk']) && $options['has_def_chk']) {
            $input_name = \yii\helpers\BaseHtml::getInputName($this->model, 'seq');
            $this->parts['{label}'] = $options['placeholder'] .
                "&nbsp;<div class='form-check use_default'><label for='flexCheckDefault_$attr'>Use Default</label>
<input class='form-check-input' type='checkbox' id='flexCheckDefault_$attr' data-input_name='$input_name' onchange='sc_toggle_default(event)' />
</div>
";
        }
        $yii_text_input = parent::textInput($options);
        return $yii_text_input;
    }
}
