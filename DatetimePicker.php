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
     * @var array the HTML attributes for the picker button.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $buttons = [];

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
            
            $localizeDate = '{}';
            $localizeTime = '{}';
            
            if ($this->dateOnly) {
                $localizeDate = "$.datepicker.regional['{$language}']";
            } elseif ($this->timeOnly) {
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
        BaseAsset::register($this->getView());
    }
}
