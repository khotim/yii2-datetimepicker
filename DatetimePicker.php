<?php
/**
 * @link http://www.timicron.com/
 * @copyright Copyright (c) 2015 Thimy Khotim
 * @license MIT License
 */

namespace khotim\datetime;

use Yii;
use yii\base\InvalidParamException;
use yii\helpers\FormatConverter;
use yii\helpers\Html;
use yii\helpers\Json;

/**
 * DatetimePicker is a wrapper around jQuery UI Datepicker
 * with Timepicker addon.
 *
 * ```
 *
 * @see http://api.jqueryui.com/datepicker/
 * @see https://github.com/trentrichardson/jQuery-Timepicker-Addon
 * @author Thimy Khotim <thimy.khotim@gmail.com>
 * @since 1.0
 */
class DatetimePicker extends \yii\jui\DatePicker
{
    /**
     * @var string picker ID.
     */
    public $pickerID = "datetimepicker";
    /**
     * @var boolean If set to true, it will only render datepicker.
     */
    public $dateOnly = false;
    /**
     * @var boolean If set to true, it will only render timepicker.
     */
    public $timeOnly = false;
    /**
     * @var array the HTML attributes for the widget container tag.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options = [];
    /**
     * @var boolean If set to true, it will render as bootstrap input group.
     */
    public $inputGroup = true;
    /**
     * @var string the CSS for the picker button. Used only when $inputGroup is set to true.
     */
    public $buttonCssClass = 'btn btn-default';

    /**
     * Renders the widget.
     */
    public function run()
    {
        echo $this->renderWidget() . "\n";

        $containerID = $this->inline ? $this->containerOptions['id'] : $this->options['id'];
        $language = $this->language ? $this->language : Yii::$app->language;
        $inputGroupJs = null;
        
        if ($this->dateOnly) {
            $this->pickerID = "datepicker";
        } elseif ($this->timeOnly) {
            $this->pickerID = "timepicker";
        }
        
        if ($this->inputGroup) {
            $this->clientOptions['showOn'] = 'button';
            $this->clientOptions['buttonText'] = '<span class="glyphicon glyphicon-calendar"></span>';
        }
        
        if ($language !== 'en-US') {
            $view = $this->getView();
            $assetBundle = LanguageAsset::register($view);
            $assetBundle->language = $language;
            $options = Json::htmlEncode($this->clientOptions);
            $language = Html::encode($language);
            
            $localizeDate = '{}';
            $localizeTime = '{}';
            
            if ($this->dateOnly) {
                $localizeDate = "$.datepicker.regional['{$language}']";
            } elseif ($this->timeOnly) {
                $localizeTime = "$.timepicker.regional['{$language}']";
            }
            
            $view->registerJs("$('#{$containerID}').{$this->pickerID}($.extend({}, {$localizeDate}, {$localizeTime}, $options))");
        } else {
            $this->registerClientOptions($this->pickerID, $containerID);
        }
        
        if ($this->inputGroup) {
            $this->getView()->registerJs("jQuery('#{$containerID}').next('.ui-datepicker-trigger').addClass('{$this->buttonCssClass}').wrap('<span class=\"input-group-btn\">');");
        }
        $this->registerClientEvents($this->pickerID, $containerID);
        BaseAsset::register($this->getView());
    }
    
    /**
     * Renders the DatePicker widget.
     * @return string the rendering result.
     */
    protected function renderWidget()
    {
        $contents = [];

        // get formatted date value
        if ($this->hasModel()) {
            $value = Html::getAttributeValue($this->model, $this->attribute);
        } else {
            $value = $this->value;
        }
        if ($value !== null && $value !== '') {
            // format value according to dateFormat
            try {
                $value = Yii::$app->formatter->asDate($value, $this->dateFormat);
            } catch(InvalidParamException $e) {
                // ignore exception and keep original value if it is not a valid date
            }
        }
        $options = $this->options;
        $options['value'] = $value;

        if ($this->inline === false) {
            if ($this->hasModel()) {
                $content = Html::activeTextInput($this->model, $this->attribute, $options);
            } else {
                $content = Html::textInput($this->name, $value, $options);
            }
            
            // render a text input
            $contents[] = $this->inputGroup ? '<div class="input-group">'.$content.'</div>' : $content;
            
        } else {
            // render an inline date picker with hidden input
            if ($this->hasModel()) {
                $contents[] = Html::activeHiddenInput($this->model, $this->attribute, $options);
            } else {
                $contents[] = Html::hiddenInput($this->name, $value, $options);
            }
            $this->clientOptions['defaultDate'] = $value;
            $this->clientOptions['altField'] = '#' . $this->options['id'];
            $contents[] = Html::tag('div', null, $this->containerOptions);
        }

        return implode("\n", $contents);
    }
}
