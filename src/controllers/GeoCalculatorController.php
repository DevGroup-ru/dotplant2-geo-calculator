<?php

namespace DotPlant\GeoCalculator\controllers;

use app\components\Controller;
use app\modules\shop\components\ShippingHandlerHelper;
use app\modules\shop\models\Order;
use DotPlant\GeoCalculator\handlers\GeoCalculatorShippingCostHandler;
use app\modules\shop\models\ShippingOption;
use Yandex\Geo\Api;
use Yii;
use yii\web\HttpException;
use yii\web\Response;

class GeoCalculatorController extends Controller
{
    public $address;
    /** @var  \Yandex\Geo\Response */
    protected $response;

    protected $answer = [];
    protected $distance = null;

    public function actionConfirmDistance()
    {
        if (false === Yii::$app->request->isAjax) {
            throw new HttpException(403);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        $this->distance = Yii::$app->request->post('gc-distance', '');
        $address = Yii::$app->request->post('gc-address', '');
        if (true === empty($this->distance) || true === empty($address)) {
            return ['error' => 'Извините, при выполнении запроса возникла ошибка. Попробуйте позже'];
        }
        Yii::$app->session->set('gc-data', ['distance' => $this->distance, 'address' => $address]);
        return 1;
    }

    public function actionGetPosition()
    {
        if (false === Yii::$app->request->isAjax) {
            throw new HttpException(403);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        $this->address = Yii::$app->request->post("gc-address", '');
        if (true === empty($this->address)) {
            return ['error' => 'Необходимо указать адрес доставки'];
        }

        $id = ShippingOption::findOne(['handler_class' => GeoCalculatorShippingCostHandler::className()])->id;
        $handler = ShippingHandlerHelper::createHandlerByShippingOptionId($id);

        $api = new Api();
        $api->setQuery($this->address);
        if ($handler->useAreaLimit) {
            $api->setArea(
                $handler->areaLimitLengthLng,
                $handler->areaLimitLengthLat,
                $handler->longitude,
                $handler->latitude
            );
            $api->useAreaLimit(true);
        }
        $api->setLimit(1)->load();
        $this->response = $api->getResponse();
        $count = $this->response->getFoundCount();
        if ($count <= 0) {
            return ['error' => 'По вашему запросу ничего не найдено'];
        }
        $this->checkPrecision();
        return $this->answer;
    }

    protected function checkPrecision()
    {
        $rawData = $this->response->getData();
        $item = $this->response->getList()[0];
        $address = $item->getAddress();
        $latitude = $item->getLatitude();
        $longitude = $item->getLongitude();
        $this->answer = [
            'longitude' => $longitude,
            'latitude' => $latitude,
        ];
        $precision = $rawData['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['metaDataProperty']['GeocoderMetaData']['precision'];
        switch ($precision) {
            case 'exact' :
                $this->answer['address'] = $address;
                break;
            case 'near' :
                $this->answer['suggestion'] = 'По вашему запросу точного совпадения не найдено. Ближайший адрес: ' . $address;
                $this->answer['address'] = $this->address;
                break;
            default :
                $this->answer = ['error' => 'По вашему запросу ничего не найдено'];
        }
    }

    public function actionGetPrice()
    {
        if (false === Yii::$app->request->isAjax) {
            throw new HttpException(403);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        $distance = Yii::$app->request->post("distance", '');
        if (true === empty($distance)) {
            return ['error' => 'Ошибка расчета расстояния маршрута'];
        }
        $id = ShippingOption::findOne(['handler_class' => GeoCalculatorShippingCostHandler::className()])->id;
        $handler = ShippingHandlerHelper::createHandlerByShippingOptionId($id);
        $data = [
            'order' => Order::getOrder(),
            'distance' => $distance
        ];
        return $handler->calculate($data);
    }
}
