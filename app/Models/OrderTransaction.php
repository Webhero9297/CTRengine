<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OrderBook;
use App\Models\Common;
use DB;

class OrderTransaction extends Model
{
    //
    protected $table = 'order_transaction';

    public function FillOrdersToMainOrder($main_order, $orders) {
      $orderBookModel = new OrderBook();
      // dd($orders);
      foreach( $orders as $order ) {

        $filled_out_amount = $order['amount'] - $this->getFilledAmountB($order['id']);
        $filling_amount = $main_order['amount'] - $this->getFilledAmountA($main_order['id']);
        $amount = min($filling_amount, $filled_out_amount);
        if ( $filled_out_amount == 0 ) {
          $orderBookModel->updateOrderStatus($order['id'], 'closed');
          continue;
        }
        if ( $filling_amount == 0 ) {
          $orderBookModel->updateOrderStatus($main_order['id'], 'closed');
          return;
        }
        else {
          $this->insert(['a_order_id'=>$main_order['id'], 'a_amount'=>$amount, 'b_order_id'=>$order['id'], 'b_amount'=>$amount, 'trade_price'=>$order['limit_price'], 'created_at'=>date('Y-m-d H:i:s.u'), 'updated_at'=>date('Y-m-d H:i:s.u')]);
        }
      }
    }
    public function getLastTradePrice($timestamp=null) {
      if ( is_null($timestamp) ) $timestamp = date("Y-m-d H:i:s");
      return DB::table($this->table)
            ->select('trade_price')->where('updated_at', '<', $timestamp)->orderBy('updated_at', 'desc')->orderBy('transaction_id', 'desc')->first()->trade_price;
    }
    public function getFilledAmountB($b_order_id) {
      $data = DB::table($this->table)
              ->select(DB::raw('sum(b_amount) as filled_amount'))->where('b_order_id',$b_order_id)->groupBy('b_order_id')->first();
      if ( is_null($data) ) return 0;
      return $data->filled_amount;
    }
    public function getFilledAmountA( $a_order_id ) {
      $data = DB::table($this->table)
              ->select(DB::raw('sum(a_amount) as filled_amount'))->where('a_order_id',$a_order_id)->groupBy('a_order_id')->first();
      if ( is_null($data) ) return 0;
      return $data->filled_amount;
    }
    public function addOrderTransaction($row_arr) {
      $row_arr['created_at'] = date('Y-m-d H:i:s');
      $row_arr['updated_at'] = date('Y-m-d H:i:s');
      $this->insert($row_arr);
      return true;
    }
    public function getTradePrice($want_asset,$offer_asset) {
      $data = DB::table($this->table)->select('trade_price')->where('offer_asset', $offer_asset)->where('want_asset', $want_asset)->orderBy('updated_at', 'desc')->first();
      if ( is_null($data) ) {
        $price = Common::changellyAPI($want_asset, $offer_asset);
        return floatval($price);
      }
      return $data->trade_price;
    }
    public function getTradeHistoryData($offer_asset, $want_asset) {
        $data = DB::select("select ot.transaction_id trade_id, ot.a_amount size, ot.trade_price price, ob.order_side side, ob.order_date, ob.updated_at time
                          from order_transaction ot
                          join orderbook ob
                          on ob.order_id = ot.a_order_id
                          where ob.order_status = 'closed' and ob.offer_asset = '$offer_asset' and ob.want_asset = '$want_asset'
                          union
                          select ot.transaction_id trade_id, ot.a_amount size, ot.trade_price price, ob.order_side side, ob.order_date, ob.updated_at time
                          from order_transaction ot
                          join orderbook ob
                          on ob.order_id = ot.b_order_id
                          where ob.order_status = 'closed' and ob.offer_asset = '$offer_asset' and ob.want_asset = '$want_asset'
                          order by time;");
        return $data;
    }
}
