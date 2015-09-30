<?php

use yii\db\Migration;
use app\modules\shop\models\ShippingOption;
use DotPlant\GeoCalculator\handlers\GeoCalculatorShippingCostHandler;

class m150929_060353_init extends Migration
{

    public function up()
    {
        $this->insert(
            ShippingOption::tableName(),
            [
                'name' => 'Доставка по адресу',
                'description' => 'Расчет стоимости в зависимости от адреса доставки.',
                'price_from' => 0,
                'price_to' => 0,
                'sort' => 0,
                'active' => 1,
                'handler_class' => GeoCalculatorShippingCostHandler::className(),
                'handler_params' => \yii\helpers\Json::encode([
                    'stepPrice' => 35,
                    'fixedInside' => 300,
                    'averagePrice' => 25000,
                    'distance' => 21,
                    'latitude' => 55.754674,
                    'longitude' => 37.621543

                ]),
            ]
        );
        $this->insert(
            '{{%configurable}}',
            [
                'module' => 'GeoCalculator',
                'sort_order' => 100,
                'section_name' => 'GeoCalculator',
                'display_in_config' => 0,
            ]
        );
    }

    public function down()
    {
        $this->delete(
            ShippingOption::tableName(),
            [
                'handler_class' => GeoCalculatorShippingCostHandler::className(),
            ]
        );
        $this->delete('{{%configurable}}', ['module' => 'GeoCalculator']);
    }
}
