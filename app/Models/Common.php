<?php

namespace App\Models;

class Common
{
    //
    public function __construct() {

    }
    public static function udate($format, $utimestamp = null) {
      if (is_null($utimestamp))
        $utimestamp = microtime(true);

      $timestamp = floor($utimestamp);
      $milliseconds = round(($utimestamp - $timestamp) * 1000000);

      return date(preg_replace('`(?<!\\\\)u`', $milliseconds, $format), $timestamp);
    }
    public static function UTCToTimestamp($utc_datetime_str)
    {
        preg_match_all('/(.+?)T(.+?)\.(.*?)Z/i', $utc_datetime_str, $matches_arr);
        $datetime_str = $matches_arr[1][0]." ".$matches_arr[2][0];
    
        return strtotime($datetime_str);
    }
      
      
    
    public static function getRateOfAsset($product) {
      $url = 'https://shapeshift.io/rate/'.$product;
      $url = 'https://shapeshift.io/getcoins';
      // $url = 'https://api.exmo.com/v1/trades/?pair=BTC-USDT';
      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $rate_data = json_decode(curl_exec($ch));
      var_dump(print_r($rate_data, true));exit;
      curl_close($ch);
    }
    public static function changellyAPI($want_asset, $offer_asset) {
      $apiKey = '59201ddd08c849228dcd6bacf4cf4279';
      $apiSecret ='57a1d9376a85b231b3ab8df0afb7c7119976a9c34661cedbdc83c8fb21304bbe';
      $apiUrl = 'https://api.changelly.com';
      $message = json_encode(array('jsonrpc'=>'2.0', 'id'=>1, 'method'=>'getExchangeAmount', 'params'=>array('from'=>strtolower($want_asset),'to'=>strtolower($offer_asset),'amount'=>1)));
      $sign = hash_hmac('sha512', $message, $apiSecret);
      $requestHeaders = [
          'api-key:' . $apiKey,
          'sign:' . $sign,
          'Content-type: application/json'
      ];
      $ch = curl_init($apiUrl);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $message);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

      $response = json_decode(curl_exec($ch));
      curl_close($ch);
      return $response->result;
    }
    public static function TetherAPI() {
      define("BASE_URL", 'https://wallet.tether.to/api/v1');
      // $BASE_URL = 'https://wallet.tether.to/api/v1';
      $apiUrl = BASE_URL . "/exchange_rates";
      $message = json_encode(array('source_currency'=>'BTC', 'target_currency'=>'USDT', 'amount'=>1));
      $requestHeaders = [

          'Content-type: application/json'
      ];
      $ch = curl_init($apiUrl);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $message);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);

      $response = curl_exec($ch);
      curl_close($ch);
      var_dump(print_r($response, true));exit;
    }
    public static function BittrexAPI() {
      $apiKey = '59201ddd08c849228dcd6bacf4cf4279';
      $apiSecret = '57a1d9376a85b231b3ab8df0afb7c7119976a9c34661cedbdc83c8fb21304bbe';
      $apiUrl = 'https://bittrex.com/api/v1.1/public/getmarkets';
      $message = json_encode(array('jsonrpc'=>'2.0', 'id'=>1, 'method'=>'getExchangeAmount', 'params'=>array('from'=>'btc','to'=>'usdt','amount'=>1)));
      $sign = hash_hmac('sha512', $message, $apiSecret);
      $requestHeaders = [
          'api-key:' . $apiKey,
          'sign:' . $sign,
          'Content-type: application/json'
      ];
      $ch = curl_init($apiUrl);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $message);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);

      $response = curl_exec($ch);
      curl_close($ch);
      var_dump(print_r(json_decode($response), true));exit;
    }
    public static function hitbitAPI() {
      define("BASE_URL", 'http://api.hitbit.com/api/1/public');
      // $BASE_URL = 'https://wallet.tether.to/api/v1';
      $apiUrl = BASE_URL . "/ticker";

      $ch = curl_init($apiUrl);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $btc_data_str = json_decode(curl_exec($ch));
      curl_close($ch);
var_dump(print_r($btc_data_str, true));exit;
      $ch = curl_init($apiUrl);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

      $response = curl_exec($ch);
      curl_close($ch);
      var_dump(print_r($response, true));exit;
    }
    public static function coinmktAPI() {
      define("BASE_URL", 'https://api.coinmarketcap.com/v1/ticker');
      // $BASE_URL = 'https://wallet.tether.to/api/v1';
      $apiUrl = BASE_URL . "/ticker/";

      $ch = curl_init($apiUrl);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $btc_data_str = json_decode(curl_exec($ch));
      curl_close($ch);
var_dump(print_r($btc_data_str, true));exit;
      $ch = curl_init($apiUrl);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

      $response = curl_exec($ch);
      curl_close($ch);
      var_dump(print_r($response, true));exit;
    }
}
