<?php

namespace DotPlant\GeoCalculator;

use yii\web\AssetBundle;

class GeoCalculatorAsset extends AssetBundle
{
    public $sourcePath = '@geoCalculator/';
    public $js = [
        'js/geo-calculator.js',
        'https://api-maps.yandex.ru/2.0-stable/?load=package.full&lang=ru-RU',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}