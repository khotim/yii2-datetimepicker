<?php
/**
 * @link http://www.timicron.com/
 * @copyright Copyright (c) 2015 Thimy Khotim
 * @license MIT License
 */

namespace khotim\datetime;

use Yii;
use yii\web\AssetBundle;

/**
 * LanguageAsset is AssetBundle for localization of DatetimePicker widget.
 */
class LanguageAsset extends AssetBundle
{
    /**
     * @var boolean whether to automatically generate the needed language js files.
     * If this is true, the language js files will be determined based on the actual usage of [[DatetimePicker]]
     * and its language settings. If this is false, you should explicitly specify the language js files via [[js]].
     */
    public $autoGenerate = true;

    /**
     * @var string language to register translation file for
     */
    public $language;

    /**
     * @inheritdoc
     */
    public $depends = [
        'yii\jui\DatePickerLanguageAsset',
        'khotim\datetime\BaseAsset',
    ];
    
    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();
        
        $this->sourcePath = __DIR__ . '/assets';
    }

    /**
     * @inheritdoc
     */
    public function registerAssetFiles($view)
    {
        if ($this->autoGenerate) {
            $language = $this->language;
            $fallbackLanguage = substr($this->language, 0, 2);
            $timeFile = Yii::getAlias($this->sourcePath . "/i18n/jquery-ui-timepicker-{$language}.js");
            
            if ($fallbackLanguage !== $this->language && (!file_exists($dateFile) || !file_exists($timeFile))) {
                $language = $fallbackLanguage;
            }
            
            $this->js[] = "i18n/jquery-ui-timepicker-$language.js";
        }
        parent::registerAssetFiles($view);
    }
}
