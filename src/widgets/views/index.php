<?php
/**
 * @var $this \yii\web\View
 */
use yii\helpers\Url;
$getPosition = Url::toRoute(['/GeoCalculator/geo-calculator/get-position']);
$confirmDistance = Url::toRoute(['/GeoCalculator/geo-calculator/confirm-distance']);
$getPrice = Url::toRoute(['/GeoCalculator/geo-calculator/get-price']);
$JS = <<<JS
var \$getPosition = '$getPosition';
var \$confirmDistance = '$confirmDistance';
var \$getPrice = '$getPrice';
var \$latitude = $wLatitude;
var \$longitude = $wLongitude;
JS;
$this->registerJs($JS, \yii\web\View::POS_HEAD);
\DotPlant\GeoCalculator\GeoCalculatorAsset::register($this);
?>
<div id="modal_delivery" class="modal for_del fade" tabindex="-1" aria-hidden="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="title_mini">Рассчитать стоимость доставки</div>
                <button type="button" class="close_modal" data-dismiss="modal" aria-hidden="true"></button>

                <div class="tab_like_radio">
                    <ul class="nav-tabs nav">
                        <li class="active"><a href="#sposob_dostavki_1" data-toggle="tab">Самовывоз</a></li>
                        <li><a href="#sposob_dostavki_2" data-toggle="tab">По Москве</a></li>
                        <li><a href="#sposob_dostavki_3" data-toggle="tab">По московской области</a></li>
                        <li><a href="#sposob_dostavki_4" data-toggle="tab">По России</a></li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane" id="sposob_dostavki_2">
                            <div class="left_side">
                                <div class="text form-group">
                                    <p class="control-label">Введите адрес доставки</p>
                                    <input data-gc-area="msk" name="address" type="text" value=""  placeholder="город улица дом" class="form-control">
                                    <p class="help-block"> </p>
                                </div>
                                <a href="#" data-gc-area="msk" data-gc-mode="calc" class="main_btn">Рассчитать</a>
                                <div class="text">
                                    После согласования срока и адреса доставки с нашим менеджером осуществляется <span class="bold_style">в течении 2 дней.</span>
                                </div>
                                <div>
                                    <p class="gc-pre-price"></p>
                                </div>
                            </div>

                            <div class="right_side">
                                <div id="gc-map" style="width: 545px; height: 245px;" class="map_wrap">

                                </div>
                                <a href="#" data-dismiss="modal" data-target="#modal_delivery" class="look_all">назад в корзину</a>
                                <a href="#" data-gc-mode="confirm" data-gc-address="" data-gc-distance="" class="look_all">учесть при оформлени заказа</a>
                                <p class="help-block"> </p>
                            </div>
                        </div>
                        <div class="tab-pane active" id="sposob_dostavki_1">
                            <div class="left_side">
                                <div class="text">
                                    <div class="bold_style">Адрес склада :</div>
                                    <p>Москва, Амурская улица, д.7 стр. 3</p>
                                </div>
                                <div class="text">
                                    Как только заказ будет принят в обработку,
                                    с Вами свяжется наш менеджер и уточнит
                                    более подробную информацию.
                                </div>
                            </div>
                            <div class="right_side">
                                <div class="map_wrap"><script type="text/javascript" charset="utf-8" src="https://api-maps.yandex.ru/services/constructor/1.0/js/?sid=54BV69ZWkYvy2WmEeWMN4Z3w9i4oTYgf&width=545&height=245"></script></div>
                                <a href="#" class="look_all">назад в корзину</a>
                                <a href="#" class="look_all">учесть при оформлени заказа</a>
                                <p class="help-block"> </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>