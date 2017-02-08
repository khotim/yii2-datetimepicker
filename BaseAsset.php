<?php
/**
 * @author Thimy Khotim <thimy.khotim@gmail.com>
 * @link https://github.com/khotim/yii2-datetimepicker/
 * @license MIT License
 * @version 1.0
 */

namespace khotim\datetime;

use Yii;

/**
 * BaseAsset is AssetBundle for DatetimePicker widget.
 */
class BaseAsset extends \yii\web\AssetBundle
{
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\jui\JuiAsset'
    ];
    public $js = [
        'jquery-ui-timepicker-addon.min.js',
        'jquery-ui-sliderAccess.js'
    ];
    public $css = ['jquery-ui-timepicker-addon.min.css'];
    
    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();
        
        $this->sourcePath = __DIR__ . '/assets';
    }
}
