<?php
/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace soc\yii2helper;

use yii\helpers\BaseHtml;

/**
 * Html provides a set of static methods for generating commonly used HTML tags.
 *
 * Nearly all of the methods in this class allow setting additional html attributes for the html
 * tags they generate. You can specify, for example, `class`, `style` or `id` for an html element
 * using the `$options` parameter. See the documentation of the [[tag()]] method for more details.
 *
 * For more details and usage information on Html, see the [guide article on html helpers](guide:helper-html).
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Html extends \kartik\helpers\Html
{
  /**
   * b3t: add iam_checkbox_hidden to the <input type=hidden>
   * @param $type
   * @param $name
   * @param $checked
   * @param $options
   * @return string
   */
  protected static function booleanInput($type, $name, $checked = false, $options = []) {
    // 'checked' option has priority over $checked argument
    if (! isset($options['checked'])) {
      $options['checked'] = (bool)$checked;
    }
    $value = array_key_exists('value', $options) ? $options['value'] : '1';
    if (isset($options['uncheck'])) {
      // add a hidden field so that if the checkbox is not selected, it still submits a value
//      $hiddenOptions = [];
      $hiddenOptions = ['iam_checkbox_hidden' => true];
      if (isset($options['form'])) {
        $hiddenOptions['form'] = $options['form'];
      }
      // make sure disabled input is not sending any value
      if (! empty($options['disabled'])) {
        $hiddenOptions['disabled'] = $options['disabled'];
      }
      $hidden = static::hiddenInput($name, $options['uncheck'], $hiddenOptions);
      unset($options['uncheck']);
    } else {
      $hidden = '';
    }
    if (isset($options['label'])) {
      $label = $options['label'];
      $labelOptions = isset($options['labelOptions']) ? $options['labelOptions'] : [];
      unset($options['label'], $options['labelOptions']);
      $content = static::label(static::input($type, $name, $value, $options) . ' ' . $label, null, $labelOptions);
      return $hidden . $content;
    }

    return $hidden . static::input($type, $name, $value, $options);
  }

  /**
   * b3t: if this hidden input was requested via booleanInput( ); make it disabled = true
   * @param $name
   * @param $value
   * @param array $options
   * @return string
   */
  public static function hiddenInput($name, $value = null, $options = []): string {
    $iam_checkbox_hidden = $options['iam_checkbox_hidden'] ?? false;
    if ($iam_checkbox_hidden) $options['disabled'] = true;
    return static::input('hidden', $name, $value, $options);
  }
}
