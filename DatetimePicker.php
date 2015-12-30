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
class DatetimePicker extends \yii\base\Widget
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
     * @var array the options for the underlying widget.
     * Please refer to [jQuery UI Datepicker](http://api.jqueryui.com/datepicker/)
     * as well as [Timepicker Addon](https://github.com/trentrichardson/jQuery-Timepicker-Addon)
     * for possible options.
     */
    public $clientOptions = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
    }

    /**
     * Renders the widget.
     */
    public function run()
    {
        echo $this->renderWidget() . "\n";

        $containerID = $this->inline ? $this->containerOptions['id'] : $this->options['id'];
        $language = $this->language ? $this->language : Yii::$app->language;
        
        if ($this->dateOnly) {
            $this->pickerID = "datepicker";
        } elseif ($this->timeOnly) {
            $this->pickerID = "timepicker";
        }

        if ($language !== 'en-US') {
            $view = $this->getView();
            $assetBundle = LanguageAsset::register($view);
            $assetBundle->language = $language;
            $options = Json::htmlEncode($this->clientOptions);
            $language = Html::encode($language);
            
            if ($this->dateOnly) {
                $localizeDate = "$.datepicker.regional['{$language}']";
                $localizeTime = null;
            } elseif ($this->timeOnly) {
                $localizeDate = null;
                $localizeTime = "$.timepicker.regional['{$language}']";
            }
            
            $view->registerJs("$('#{$containerID}').{$this->pickerID}($.extend({}, {$localizeDate}, {$localizeTime}, $options))");
            
            if (isset($this->clientOptions['showOn'])) {
                if (isset($this->buttons['class']))
                    $buttonCssClass = $this->buttons['class'];
                else
                    $buttonCssClass = 'btn btn-default';
                
                $view->registerJs("$('#{$containerID}').next('.ui-datepicker-trigger')
                    .addClass({$buttonCssClass})
                    .wrap({$this->buttonText});");
            }
        } else {
            $this->registerClientOptions($this->pickerID, $containerID);
        }

        $this->registerClientEvents($this->pickerID, $containerID);
        WidgetAsset::register($this->getView());
    }

    /**
     * Renders the DatetimePicker widget.
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
            // render a text input
            if ($this->hasModel()) {
                $contents[] = Html::activeTextInput($this->model, $this->attribute, $options);
            } else {
                $contents[] = Html::textInput($this->name, $value, $options);
            }
        } else {
            // render an inline datetime picker with hidden input
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
