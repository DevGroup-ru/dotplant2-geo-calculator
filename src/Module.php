<?php

namespace DotPlant\GeoCalculator;

use app\components\ExtensionModule;
use app\modules\shop\models\ShippingOption;
use DotPlant\GeoCalculator\handlers\GeoCalculatorShippingCostHandler;
use yii\helpers\Json;

class Module extends ExtensionModule
{
    public static $moduleId = 'GeoCalculator';

    public $coords = ['latitude' => 55.754674, 'longitude' => 37.621543];
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'configurableModule' => [
                'class' => 'app\modules\config\behaviors\ConfigurableModuleBehavior',
                'configurationView' => '@geoCalculator/views/configurable/_config',
                'configurableModel' => 'DotPlant\GeoCalculator\components\ConfigurationModel',
            ]
        ];
    }

    public function init()
    {
        /** @var  $option  ShippingOption */
        $option = ShippingOption::findOne(['handler_class' => GeoCalculatorShippingCostHandler::className()]);
        $params = Json::decode($option->handler_params);
        $this->coords = [
            'latitude' => $params['latitude'],
            'longitude' => $params['longitude'],
        ];
        return parent::init();
    }
}
