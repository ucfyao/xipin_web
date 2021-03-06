<?php defined( 'VCW_INDEX' ) or die( '' );

if(!class_exists('VCW_Constants')) {

    class VCW_Constants {

        static public $color_schemas = array(
            'red'           => 'Red',
            'pink'          => 'Pink',
            'purple'        => 'Purple',
            'deep-purple'   => 'Deep Purple',
            'indigo'        => 'Indigo',
            'blue'          => 'Blue',
            'light-blue'    => 'Light Blue',
            'cyan'          => 'Cyan',
            'teal'          => 'Teal',
            'green'         => 'Green',
            'light-green'   => 'Light Green',
            'lime'          => 'Lime',
            'yellow'        => 'Yellow',
            'amber'         => 'Amber',
            'orange'        => 'Orange',
            'deep-orange'   => 'Deep Orange',
            'brown'         => 'Brown',
            'grey'          => 'Grey',
            'blue-grey'     => 'Blue Grey',
            'black'         => 'Black',
            'white'         => 'White'
        );

        static public $widgets_change_headers = array(
            'change_1h'     => '% 1h',
            'change_24h'    => '% 24h',
            'change_7d'     => '% 7d',
        );

        static public $table_widget_cols = array(
            'name'          => 'CryptoCurrency',
            'change_1h'     => 'Change 1h',
            'change_24h'    => 'Change 24h',
            'change_7d'     => 'Change 7d',
        );

        static public $small_table_widget_cols = array(
            'symbol'        => 'Symbol',
            'change_1h'     => '% 1h',
            'change_24h'    => '% 24h',
            'change_7d'     => '% 7d',
        );

        static public $converter_widget_priority = array(
            'BTC','ETH','USD','EUR','GBP','KRW','XRP','BCH'
        );

        static public $currency_symbols = array(
            'ALL' => 'Lek',
            'AFN' => '؋',
            'ARS' => '$',
            'AWG' => 'ƒ',
            'AUD' => '$',
            'AZN' => '₼',
            'BSD' => '$',
            'BBD' => '$',
            'BYN' => 'Br',
            'BZD' => 'BZ$',
            'BMD' => '$',
            'BOB' => '$b',
            'BAM' => 'KM',
            'BWP' => 'P',
            'BGN' => 'лв',
            'BRL' => 'R$',
            'BND' => '$',
            'KHR' => '៛',
            'CAD' => '$',
            'KYD' => '$',
            'CLP' => '$',
            'CNY' => '¥',
            'COP' => '$',
            'CRC' => '₡',
            'HRK' => 'kn',
            'CUP' => '₱',
            'CZK' => 'Kč',
            'DKK' => 'kr',
            'DOP' => 'RD$',
            'XCD' => '$',
            'EGP' => '£',
            'SVC' => '$',
            'EUR' => '€',
            'FKP' => '£',
            'FJD' => '$',
            'GHS' => '¢',
            'GIP' => '£',
            'GTQ' => 'Q',
            'GGP' => '£',
            'GYD' => '$',
            'HNL' => 'L',
            'HKD' => '$',
            'HUF' => 'Ft',
            'ISK' => 'kr',
            'INR' => '',
            'IDR' => 'Rp',
            'IRR' => '﷼',
            'IMP' => '£',
            'ILS' => '₪',
            'JMD' => 'J$',
            'JPY' => '¥',
            'JEP' => '£',
            'KZT' => 'лв',
            'KPW' => '₩',
            'KRW' => '₩',
            'KGS' => 'лв',
            'LAK' => '₭',
            'LBP' => '£',
            'LRD' => '$',
            'MKD' => 'ден',
            'MYR' => 'RM',
            'MUR' => '₨',
            'MXN' => '$',
            'MNT' => '₮',
            'MZN' => 'MT',
            'NAD' => '$',
            'NPR' => '₨',
            'ANG' => 'ƒ',
            'NZD' => '$',
            'NIO' => 'C$',
            'NGN' => '₦',
            'NOK' => 'kr',
            'OMR' => '﷼',
            'PKR' => '₨',
            'PAB' => 'B/.',
            'PYG' => 'Gs',
            'PEN' => 'S/.',
            'PHP' => '₱',
            'PLN' => 'zł',
            'QAR' => '﷼',
            'RON' => 'lei',
            'RUB' => '₽',
            'SHP' => '£',
            'SAR' => '﷼',
            'RSD' => 'Дин.',
            'SCR' => '₨',
            'SGD' => '$',
            'SBD' => '$',
            'SOS' => 'S',
            'ZAR' => 'R',
            'LKR' => '₨',
            'SEK' => 'kr',
            'CHF' => 'CHF',
            'SRD' => '$',
            'SYP' => '£',
            'TWD' => 'NT$',
            'THB' => '฿',
            'TTD' => 'TT$',
            'TRY' => '',
            'TVD' => '$',
            'UAH' => '₴',
            'GBP' => '£',
            'USD' => '$',
            'UYU' => '$U',
            'UZS' => 'лв',
            'VEF' => 'Bs',
            'VND' => '₫',
            'YER' => '﷼',
            'ZWD' => 'Z$'
        );

    }
}