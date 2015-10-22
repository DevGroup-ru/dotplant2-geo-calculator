<?php
namespace DotPlant\GeoCalculator\handlers;

use app\modules\shop\components\AbstractShippingHandler;
use app\modules\shop\helpers\PriceHelper;
use app\modules\shop\models\SpecialPriceList;
use Yii;

class GeoCalculatorShippingCostHandler extends AbstractShippingHandler
{
    /**
     * @var  float addition price per kilometer outside range
     * Цена за километр при выезде за указанное количество километров (за МКАД)
     */
    public $stepPrice;
    /**
     * @var  float min order price for free shipping enabling inside range
     * Минимальная цена заказа, при которой не учитываем стоимость доставки внутри заданного расстояния (внутри МКАД)
     */
    public $averagePrice;
    /**
     * @var  integer calculated distance from start point to point of customer given address
     * Расстояние между базовой стартовой точной (красная площадь) и точкой, расчитанной по указанному пользователем адресу
     */
    public $distance;
    /**
     * @var float fixed price inside range if $averagePrice > total Order price
     * Фиксированная надбавка, если стоимость заказа меньше минимальной стоимости бесплатной доставки
     */
    public $fixedInside;
    /**
     * @var float latitude of map start delivery point
     */
    public $latitude;

    /**
     * @var float longitude of map start delivery point
     */
    public $longitude;

    /***
     * @var bool
     * Использовать ограничение области поиска
     */
    public $useAreaLimit = 0;

    /**
     * @var float
     * Разница между максимальной и минимальной долготой в градусах
     */
    public $areaLimitLengthLng = 2.5;

    /**
     * @var float
     * Разница между максимальной и минимальной широтой в градусах
     */
    public $areaLimitLengthLat = 2.5;

    private $deliveryPrice = 0;

    /**
     * @inheritdoc
     */
    public function calculate($data = [])
    {
        if (false === isset($data['distance'])) {
            $sessionData = Yii::$app->session->get('gc-data');
            $usersDistance = isset($sessionData['distance']) ? $sessionData['distance'] : null;
        } else {
            $usersDistance = $data['distance'];
        }
        if (null === $usersDistance) {
            $this->lastErrorMessage = \Yii::t('app', 'Distance not defined');
            return false;
        }
        $totalPrice = PriceHelper::getOrderPrice($data['order'], SpecialPriceList::TYPE_CORE);
        $wDiscountsPrice = PriceHelper::getOrderPrice($data['order']);
        if (($delta = ($usersDistance - $this->distance)) > 0) {
            $this->deliveryPrice = $delta * $this->stepPrice;
        }
        if ($wDiscountsPrice < $this->averagePrice) {
            $this->deliveryPrice += $this->fixedInside;
        }
        return $this->deliveryPrice;
    }

    /**
     * @inheritdoc
     */
    public function getCartForm($form, $order)
    {
        return '';
    }
}
