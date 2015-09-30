<?php
namespace DotPlant\GeoCalculator\widgets;

use yii\base\Widget;

class GeoCalculatorWidget extends Widget
{
    protected $coords;

    public function init()
    {
        $this->coords = \Yii::$app->getModule('GeoCalculator')->coords;
        return parent::init();
    }

    public function run()
    {
        return $this->render(
            'index',
            [
                'wLatitude' => $this->coords['latitude'],
                'wLongitude' => $this->coords['longitude'],
            ]
        );
    }
}