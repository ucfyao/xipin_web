<?php defined( 'VCW_INDEX' ) or die( '' );

if(!class_exists('VCW_Data'))
{
    class VCW_Data
    {
        static protected $update_interval = 60;
        static protected $data;
        static protected $transient_name = 'vcw_data';
        static protected $symbols_correction = array(
            'iota'                   => array('MIOTA',  'IOT'),
            'nano'                   => array('NANO',   'XRB'),
            'ethos'                  => array('ETHOS',  'BQX'),
            'smartmesh'              => array('SMT',    'SMT*'),
            'bridgecoin'             => array('BCO',    'BCO*'),
            'universa'               => array('UTNP',   'UTN'),
            'blakestar'              => array('ERA',    'BLAS'),
            'paccoin'                => array('$PAC',   'PAC'),
            'revolutionvr'           => array('RVR',    'VOX'),
            'bitconnect'             => array('BCC',    'BCCOIN'),
            'leadcoin'               => array('LDC',    'LDC*'),
            'chips'                  => array('CHIPS',  'CHIPS*'),
            'nubits'                 => array('USNBT',  'NBT'),
            'goldmint'               => array('MNTP',   'MNT'),
            'kolion'                 => array('KLN',    'KOLION'),
            'steem-dollars'          => array('SBD*',   'SBD'),
            'bitsend'                => array('BSD*',   'BSD'),
            'bitclave'               => array('CAT*',   'CAT*'),
            'bytom'                  => array('BTM*',   'BTM*'),
            'sharpe-platform-token'  => array('SHP*',   'SHP'),
            'autonio'                => array('NIO*',   'NIO'),
            'cpchain'                => array('CPC*',   'CPC*'),
            'profile-utility-token'  => array('PUT*',   'PUT*'),
            'crowdcoin'              => array('CRC*',   'CRC'),
            'bitgem'                 => array('BTG*',   'BTG*'),
            'accelerator-network'    => array('ACC*',   'ACC*'),
            'steneum-coin'           => array('STN*',   'STN'),
            'global-tour-coin'       => array('GTC*',   'GTC*'),
            'allion'                 => array('ALL*',   null),
            'harmonycoin-hmc'        => array('HMC*',   null),
            'nimiq'                  => array('NET*',   'NET*'),
            'blazecoin'              => array('BLZ*',   'BLAZR'),
            'satoshimadness'         => array('MAD*',   'MAD'),
            'icoin'                  => array('ICN*',   null),
            'catcoin'                => array('CAT**',  'CAT1'),
            'polcoin'                => array('PLC*',   null),
            'global-cryptocurrency'  => array('GCC*',   'GCC*'),
            'comet'                  => array('CMT*',   'CMT'),
            'cybermiles'             => array('CMT',    'CMT*'),
            'kingn-coin'             => array('KNC*',   'KNC**'),
            'remicoin'               => array('RMC*',   null),
            'fairgame'               => array('FAIR*',  'FAIR*'),
            'lightning-bitcoin'      => array('LBTC*',  null),
            'content-and-ad-network' => array('CAN*',   null),
            'maggie'                 => array('MAG*',   'MAG**'),
            'infinity-economics'     => array('XIN*',   'XIN'),
            'comsa-xem'              => array('CMS*',   null),
            'entcash'                => array('ENT*',   null),
            'international-diamond'  => array('XID*',   'XID*'),
            'encryptotel-eth'        => array('ETT*',   null),
            'qbao'                   => array('QBT*',   'QBT*'),
            'acchain'                => array('ACC**',  null),
            'huncoin'                => array('HNC*',   'HNC*'),
            'waykichain'             => array('WIC*',   null),
            'batcoin'                => array('BAT*',   null),
            'topcoin'                => array('TOP*',   null),
            'dao-casino'             => array('BET*',   'BET*'),
            'rcoin'                  => array('RCN*',   'RCN*')
        );

        static public function init()
        {
            $data_serialized = get_transient(self::$transient_name);

            if($data_serialized !== false) {
                self::$data = maybe_unserialize($data_serialized);
                return;
            }

            $cmc_data    =  self::requestCoinMarketCap();
            $bitpay_data =  self::requestBitPay();
            $cc_data     =  self::requestCryptoCompare();

            self::buildData($cmc_data, $bitpay_data,$cc_data);

            if(self::dataIsGood()) {
                set_transient(self::$transient_name, maybe_serialize(self::$data), self::$update_interval);
            }
        }

        static protected function request($url)
        {
            $request = wp_remote_get($url);

            if(is_wp_error($request)) {
                return false;
            }

            return json_decode(wp_remote_retrieve_body($request),true);
        }

        static protected function requestCoinMarketCap()
        {
            return self::request('https://api.coinmarketcap.com/v1/ticker/?limit=0');
        }

        static protected function requestBitPay()
        {
            return self::request('https://bitpay.com/api/rates');
        }

        static protected function requestCryptoCompare()
        {
            return self::request('https://min-api.cryptocompare.com/data/all/coinlist');
        }

        static protected function buildData($cmc_data, $bitpay_data, $cc_data)
        {

            $list  = array();
            $rates = array();

            // all rates with bitcoin quotation
            $rates['BTC'] = array(
                'code' => 'BTC',
                'name' => 'Bitcoin',
                'rate' => 1,
                'id'   => 'bitcoin'
            );

            $btc_usd_rate = null;

            // collect all rates in Bitpay (fiat info)
            foreach ($bitpay_data as $currency) {
                $code = $currency['code'];
                $rate = $currency['rate'] !== null ? floatval($currency['rate']) : null; // float value

                if ($code !== 'BTC' && $code !== 'BCH') { // skip cryptocurrencies
                    $rates[$code] = array( // add rate
                        'code' => $code,
                        'name' => $currency['name'],
                        'rate' => $rate
                    );
                }

                if ($code === 'USD') {
                    $btc_usd_rate = $rate; // bitcoin usd value
                }
            }

            if (!$btc_usd_rate) return null;

            foreach ($cmc_data as $cmc_info) {
                $id         = $cmc_info['id'];
                $code       = $cmc_info['symbol'];
                $name       = $cmc_info['name'];
                $price_btc  = $cmc_info['price_btc'] !== null ? floatval($cmc_info['price_btc']) : null;
                $rate       = $price_btc !== null ? 1 / $price_btc : null;

                if(isset(self::$symbols_correction[$id])) { // if needs correction
                    $correction = self::$symbols_correction[$id];
                    $final_code = $correction[0]; // code for rates entry
                    $cc_code    = $correction[1]; // code for CryptoCompare
                }
                else { // no correction
                    $final_code = $code;
                    $cc_code    = $code;
                }

                // crypto info
                // skip duplicated rates (future-proof in case of bad cryptocurrency symbol use)
                if(empty($rates[$final_code]) || $final_code === 'BTC') {

                    if ($code !== 'BTC') { // add rate if not bitcoin (already exists)
                        $rates[$final_code] = array(
                            'code' => $final_code,
                            'name' => $name,
                            'rate' => $rate,
                            'id'   => $id
                        );
                    }

                    // cryptocurrency item
                    // CoinMarketCap info
                    $entry = array(
                        'id'                => $id,
                        'code'              => $code,
                        'final_code'        => $final_code,
                        'cc_code'           => $cc_code,
                        'name'              => $name,
                        'rank'              => $cmc_info['rank'],
                        'price_btc'         => $price_btc,
                        'market_cap_btc'    => $cmc_info['market_cap_usd'] !== null ? floatval($cmc_info['market_cap_usd']) / $btc_usd_rate : null,
                        'volume_24h_btc'    => $cmc_info['24h_volume_usd'] !== null ? floatval($cmc_info['24h_volume_usd']) / $btc_usd_rate : null,
                        'change_1h'         => $cmc_info['percent_change_1h'] !== null ? floatval($cmc_info['percent_change_1h']) : null,
                        'change_24h'        => $cmc_info['percent_change_24h'] !== null ? floatval($cmc_info['percent_change_24h']) : null,
                        'change_7d'         => $cmc_info['percent_change_7d'] !== null ? floatval($cmc_info['percent_change_7d']) : null,
                        'available_units'   => $cmc_info['available_supply'],
                        'max_units'         => $cmc_info['max_supply'],
                        'image'             => null,
                        'algorithm'         => null,
                        'proof_type'        => null
                    );

                    if (isset($cc_data['Data'][$cc_code])) { // if CryptoCompare info found
                        $cc_info = $cc_data['Data'][$cc_code];

                        $entry['algorithm']  = (empty($cc_info['Algorithm']) || $cc_info['Algorithm'] === 'N/A') ? null : $cc_info['Algorithm'];
                        $entry['image']      = empty($cc_info['ImageUrl']) ? null : $cc_data['BaseImageUrl'] . $cc_info['ImageUrl'];
                        $entry['proof_type'] = (empty($cc_info['ProofType']) || $cc_info['ProofType'] === 'N/A') ? null : $cc_info['ProofType'];

                        if (empty($entry['max_units']) && !empty($cc_info['TotalCoinSupply']) && is_numeric($cc_info['TotalCoinSupply'])) {
                            $entry['max_units'] = floatval($cc_info['TotalCoinSupply']);
                        }

                    }

                    $list[] = $entry;
                }
            }

            self::$data = array(
                'list'  => $list,
                'rates' => $rates
            );
        }

        static public function dataIsGood()
        {
            return is_array(self::$data) &&
                isset(self::$data['list']) && is_array(self::$data['list']) &&
                isset(self::$data['rates']) && is_array(self::$data['rates']);
        }

        static public function cryptoCurrency($code, $default = null)
        {
            if(self::dataIsGood()) {
                foreach (self::$data['list'] as $cryptocurrency) {
                    if($cryptocurrency['final_code'] === $code) {
                        return $cryptocurrency;
                    }
                }
            }

            return $default;
        }

        static public function cryptoCurrencies($default = null)
        {
            if(self::dataIsGood()) {
                return self::$data['list'];
            }

            return $default;
        }

        static public function cryptoCurrenciesTop($n = 10)
        {
            $all = self::cryptoCurrencies();

            if(is_array($all)) {
                return array_slice($all, 0, $n);
            }

            return null;
        }

        static public function rate($code, $default = null)
        {
            if(self::dataIsGood()) {
                if(isset(self::$data['rates'][$code])) {
                    return self::$data['rates'][$code];
                }
            }

            return $default;
        }

        static public function rates($default = null)
        {
            if(self::dataIsGood()) {
                return self::$data['rates'];
            }

            return $default;
        }

    }

    VCW_Data::init();
}


