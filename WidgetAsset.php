<?php
/**
 * @link http://www.timicron.com/
 * @copyright Copyright (c) 2015 Thimy Khotim
 * @license MIT License
 */

namespace khotim\datetime;

use yii\web\AssetBundle;

/**
 * @author Thimy Khotim <thimy.khotim@gmail.com>
 * @since 1.0
 */
class WidgetAsset extends AssetBundle
{
    public $sourcePath = '@khotim/datetime/assets';
    
    public $js = [
        'jquery-ui.min.js',
        'jquery-ui-timepicker-addon.min.js'
    ];
    
    public $css = [
        'jquery-ui.min.css',
        'jquery-ui-timepicker-addon.min.css'
    ];
    
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
