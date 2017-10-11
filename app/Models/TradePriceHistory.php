<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OrderTransaction;

class TradePriceHistory extends Model
{
    //
    protected $table = 'trade_price_history';

    /**
      *** Param current datetime
      *** return: Array(Open, high, low, close)
    */
    public function getCurrentTradePriceData( $product, $datetime = null ) {

      if ( is_null($datetime) ) $datetime = date('Y-m-d H:i:s');
      $row_data = $this->where('created_at', '=', $datetime)->get()->first();

      if (is_null($row_data)) {
        $url = 'https://api.gdax.com/products/'.$product.'/ticker';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $agents = array(
         'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:7.0.1) Gecko/20100101 Firefox/7.0.1',
         'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.1.9) Gecko/20100508 SeaMonkey/2.0.4',
         'Mozilla/5.0 (Windows; U; MSIE 7.0; Windows NT 6.0; en-US)',
         'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_7; da-dk) AppleWebKit/533.21.1 (KHTML, like Gecko) Version/5.0.5 Safari/533.21.1'

        );
        curl_setopt($ch,CURLOPT_USERAGENT,$agents[array_rand($agents)]);
        $product_data = json_decode(curl_exec($ch));
        curl_close($ch);
        return floatval($product_data->price);
      }
      else {
        $orderTransactionModel = new OrderTransaction();

        return $orderTransactionModel->getLastTradePrice();
      }
    }
}
