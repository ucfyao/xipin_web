'use strict';

(function ($, VCW, fx) {

    function logger(txt) {
        console.error('[VCW] ' + txt);
    }

    var RATES = {};

    Object.keys(VCW.rates).forEach(function (code) {
        RATES[code] = VCW.rates[code].rate;
    });

    /* MoneyJS Setup */

    fx.rates = RATES;
    fx.base = 'BTC';

    var Converter = {
        convert: function convert(from, to, value) {
            return fx(value).from(from).to(to);
        },
        priceFormat: function priceFormat(value) {
            value = parseFloat(value);

            if (isNaN(value)) return null;

            if (value >= 10000) return value.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            if (value >= 1000) return value.toFixed(1).replace(/\B(?=(\d{3})+(?!\d))/g, ',');

            function format() {
                var exp = Math.log10(value);

                if (exp >= 1) {
                    return value.toFixed(2);
                } else if (exp >= 0) {
                    return value.toFixed(3);
                }

                var _exp = Math.ceil(Math.abs(exp));
                return value.toFixed(_exp + 3);
            }

            var final = format(format(value));

            return final >= 10000 ? final.replace(/\B(?=(\d{3})+(?!\d))/g, ',') : final;
        },
        convertFormatted: function convertFormatted(from, to, value) {
            return this.priceFormat(this.convert(from, to, value));
        }
    };

    var Socket = {
        channels: {},
        io: null,

        connect: function connect() {
            if (Socket.io) return;

            Socket.io = io.connect('https://coincap.io');

            Socket.io.on('trades', function (trade) {
                var code = trade.coin,
                    price = trade.msg.price;

                var channel = Socket.channels[code];

                if (Array.isArray(channel)) {
                    channel.forEach(function (ring) {
                        return ring(price);
                    });
                }
            });
        },
        addToChannel: function addToChannel(code, callback) {
            this.connect();

            Array.isArray(this.channels[code]) ? this.channels[code].push(callback) : this.channels[code] = [callback];
        }
    };

    VCW.compile = function (parent) {
        parent.find('.vcw price-output').vcwPriceOutput();

        parent.find('.vcw-price-label, .vcw-change-label, .vcw-price-big-label, .vcw-change-big-label, .vcw-price-card, .vcw-change-card, .vcw-full-card').vcwLink();

        parent.find('.vcw-converter').vcwConverter();
    };

    $.fn.extend({
        vcwPriceOutput: function vcwPriceOutput() {
            this.each(function () {
                var elem = $(this),
                    code = elem.data('code'),
                    currency = elem.data('currency');

                Socket.addToChannel(code, function (price_usd) {
                    var converted = Converter.priceFormat(Converter.convert('USD', currency, price_usd));
                    elem.fadeOut(function () {
                        $(this).text(converted).fadeIn();
                    });
                });
            });
        },
        vcwLink: function vcwLink() {
            this.each(function () {
                var elem = $(this),
                    url = elem.data('url'),
                    target = elem.data('target');

                if (url) elem.click(function () {
                    return window.open(url, target);
                });
            });
        },
        vcwConverter: function vcwConverter() {
            this.each(function () {
                var elem = $(this),
                    currency_1 = elem.find('.vcw-currency-1'),
                    currency_2 = elem.find('.vcw-currency-2'),
                    value_1 = elem.find('.vcw-value-1'),
                    value_2 = elem.find('.vcw-value-2');

                currency_1.on("change paste keyup", function () {
                    var v = Converter.convertFormatted(currency_2.val(), currency_1.val(), value_2.val());
                    value_1.val(v);
                });

                value_1.on("change paste keyup", function () {
                    var v = Converter.convertFormatted(currency_1.val(), currency_2.val(), value_1.val());
                    value_2.val(v);
                });

                currency_2.on("change paste keyup", function () {
                    var v = Converter.convertFormatted(currency_1.val(), currency_2.val(), value_1.val());
                    value_2.val(v);
                });

                value_2.on("change paste keyup", function () {
                    var v = Converter.convertFormatted(currency_2.val(), currency_1.val(), value_2.val());
                    value_1.val(v);
                });

                value_2.val(Converter.convertFormatted(currency_1.val(), currency_2.val(), value_1.val()));
            });
        }
    });

    $(function () {
        VCW.compile($(document));
    });
})(jQuery, VirtualCoinWidgets, fx);