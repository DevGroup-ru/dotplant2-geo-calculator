$(function () {
    "use strict";
    (function ($) {
        //init maps
        var $gcMap, $warehouse, $destination, $route, $distance, $suggestion;
        function init(){
            $gcMap = new ymaps.Map ("gc-map", {
                center: [$latitude, $longitude],
                zoom: 13
            });
            $gcMap.controls.add('zoomControl');
            $gcMap.controls.add('typeSelector');
            $gcMap.controls.add('mapTools');
            $warehouse = new ymaps.Placemark([$latitude, $longitude], {
                hintContent: 'Красная площадь'
            });
            //$gcMap.geoObjects.add($warehouse);
            $("body").on("click", '[data-gc-mode="calc"]', function () {
                var $area = $(this).data('gc-area');
                var $input = $('input[data-gc-area="' + $area + '"]');
                var $address = $input.val();
                var $this = $(this);
                if (typeof $destination === 'object') {
                    $gcMap.geoObjects.remove($destination);
                }
                $.post(
                    $getPosition,
                    {"gc-address": $address},
                    function (responce) {
                        $this.prev('.form-group:eq(0)').removeClass('has-error');
                        $input.next('.help-block').html('');
                        $('.gc-pre-price').html('');
                        if (typeof responce.error !== 'undefined') {
                            $this.prev('.form-group:eq(0)').addClass('has-error');
                            $input.next('.help-block').html(responce.error);
                        } else {
                            $this.prev('.form-group:eq(0)').removeClass('has-error');
                            if (typeof responce.suggestion !== 'undefined') {
                                $suggestion = responce.suggestion;
                                $input.next('.help-block').html($suggestion);
                            }
                            $address = responce.address;
                            $('[data-gc-mode="confirm"]').data('gc-address', $address);
                            $destination = new ymaps.Placemark([responce.latitude, responce.longitude], {
                                hintContent: $address,
                                balloonContent: $address
                            });
                            $gcMap.setCenter([responce.latitude, responce.longitude], 12);
                            $gcMap.geoObjects.add($destination);
                            ymaps.route([
                                [$latitude, $longitude], [responce.latitude, responce.longitude]], {
                                mapStateAutoApply: true
                            }).then(function (router) {
                                $route && $gcMap.geoObjects.remove($route);
                                $route = router;
                                //$gcMap.geoObjects.add($route);
                                $distance = Math.ceil($route.getLength()/1000);
                                $('input[name="gc-hidden-distance"]').val($distance);
                                $('[data-gc-mode="confirm"]').data('gc-distance', $distance);
                                $.post(
                                    $getPrice,
                                    {"distance": $distance},
                                    function(responce){
                                        if (typeof responce.error !== 'undefined') {
                                            $this.prev('.form-group:eq(0)').addClass('has-error');
                                            $input.next('.help-block').html(responce.error);
                                        } else {
                                            $('.gc-pre-price').html("Предварительная стоимость доставки: " + responce + " рублей.");
                                        }
                                    });
                            }, function (error) {
                                console.log("Возникла ошибка: " + error.message);
                            });
                        }
                    },
                    "json");
                return false;
            });
        }
        ymaps.ready(init);
        $("body").on("click", '[data-gc-mode="confirm"]', function(){
            var $address = $(this).data('gc-address');
            var $distance = $(this).data('gc-distance');
            var $this = $(this);
            if ($this.hasClass('confirmed')) {
                return false;
            }
            $this.parents('.right_side:eq(0)').removeClass('has-error').children('.help-block').html('');
            $.post(
                $confirmDistance,
                {"gc-address": $address, "gc-distance" : $distance},
                function (responce) {
                    if (typeof responce.error !== 'undefined') {
                        $this.parents('.right_side:eq(0)').addClass('has-error').children('.help-block').html(responce.error);
                    } else {
                        $this.addClass('confirmed').text("Данные учтены");
                    }
                },"json");
            return false;
        });
    })(jQuery);
});
