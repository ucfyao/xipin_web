<?php defined( 'VCW_INDEX' ) or die( '' );

if(!class_exists('VCW_Helpers')) {

    class VCW_Helpers
    {

        static protected $number_dec_point = '.';
        static protected $number_thousands_sep = ',';


        static public function number_format($number, $precision)
        {
            return number_format($number, $precision, self::$number_dec_point, self::$number_thousands_sep);
        }

        static public function price_format($value)
        {
            if(!is_numeric($value))
                return null;

            if($value >= 10000) return self::number_format($value, 0);
            if($value >= 1000) return self::number_format($value, 1);

            $number_1 = floatval($value);
            $exp = log10($number_1); // get log10 of number

            if($exp >= 1) {
                $number_2 = round($number_1, 2); // show at least 2 decimal places
            }
            elseif ($exp >= 0) {
                $number_2 = round($number_1, 3);
            }
            else {
                $_exp = ceil(abs($exp));
                $number_2 = round($number_1, $_exp + 3);
            }

            // repeat for correction
            // because: 0.9999 will be 1.000

            $exp = log10($number_2);

            if($exp >= 1) {
                return self::number_format($number_2, 2);
            }
            elseif ($exp >= 0) {
                return self::number_format($number_2, 3);
            }

            $_exp = ceil(abs($exp));
            return self::number_format($number_2, $_exp + 3);
        }

        static public function big_number($number) {
            return self::number_format($number,0);
        }

        static public function checkArrayValues(&$array, $keys = null, $test = null)
        {
            if(!is_array($array))
                return false;

            if(is_null($keys)) {
                foreach ($array as $key => $value){
                    if($test) {
                        if(!call_user_func($test, $value))
                            return false;
                    }
                    else if(!isset($array[$key])) {
                        return false;
                    }
                }
            }
            else if(is_array($keys)) {
                foreach ($keys as $key){
                    if($test) {
                        if(!array_key_exists($key, $array) || !call_user_func($test, $array[$key]))
                            return false;
                    }
                    else if(!isset($array[$key])) {
                        return false;
                    }
                }
            }
            else {
                return false;
            }

            return true;
        }

        static public function changeString($change, $percent_sign = true)
        {
            if(is_null($change)) {
                return '---';
            }
            else {
                $change_str = self::number_format(abs($change), 2);
                return $percent_sign ? "$change_str %" : $change_str;
            }
        }

        static public function defaultTableWidgetItems($count)
        {
            $f_count = floatval($count);

            $top = VCW_Data::cryptoCurrenciesTop($f_count > 0 ? $f_count : 10);

            if(is_array($top)){
                return array_map(function($c) {
                    return $c['final_code'];
                }, $top);
            }

            return null;
        }

        static public function currencySymbol($code, $default = null)
        {
            if(isset(VCW_Constants::$currency_symbols[$code])) {
                return VCW_Constants::$currency_symbols[$code];
            }

            return $default;
        }

    }

}