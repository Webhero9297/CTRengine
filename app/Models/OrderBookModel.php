<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;
use DB;

class OrderBookModel extends BaseModel 
{
    //
    private $_origin_table_name = 'orderbook';
    private $_primary_key = 'order_id';

    public function __construct( $year=null, $month=null ) {
        if ( is_null($year) ) $year = date('Y');
        if ( is_null($month) ) $month = date('m');
        $newTableName = $this->_origin_table_name.'_'.$year.'_'.$month;
        if ( !$this->hasTable($newTableName) )
            $this->createNewTable($this->_origin_table_name, $year, $month);
        $this->setTableName($newTableName);
        $this->setPrimarykey($this->_primary_key);
    }
    public function getPendingData($order_type=null) {

    }
    /**
        ** Store new order to OrderBook
        ****   $sql = "insert into `orderbook_2017_09` select * from orderbook where order_status in ('pending', 'open')"
    */
    public function storeNewOrder( $new_order_data ) {
        $this->insert( $new_order_data );
    }
    /***
        ** $price - trading price
    */
    public function PlaceBuyBid($customer_id, $price, $expiration_date) {
        $tbName = $this->getTableName();
        $buy_data = DB::table($tbName)->where('order_side', 'buy')/*->where('customer_id', '<>',$customer_id)*/->where('order_status', 'pending')->get()->toArray();
        $sqls = array();
        $sqlString = "";
        if ($buy_data) {
            foreach( $buy_data as $record ) {
                if ( $record->order_type == 'limit' ) {
                    if ( $record->time_in_force == 'GTDT' && $record->expiration_date < $expiration_date ) {
                        $sqls[] = "UPDATE  `$tbName` set order_status='cancelled' WHERE order_id='".$record->order_id."'";
                    }
                    if ( $record->limit_price >= $price  ) {
                        $sqls[] = "UPDATE  `$tbName` set order_status='open' WHERE order_id='".$record->order_id."'";
                    }
                }
                if ( $record->order_type == 'stop' ) {
                    if ( $record->time_in_force == 'GTDT' && $record->expiration_date < $expiration_date ) {
                        $sqls[] = "UPDATE  `$tbName` set order_status='cancelled' WHERE order_id='".$record->order_id."'";
                    }
                    if ( $record->stop_price <= $price  ) {
                        $sqls[] = "UPDATE  `$tbName` set order_status='open' WHERE order_id='".$record->order_id."'";
                    }
                }
                if ( $record->order_type == 'stoplimit' ) {
                    if ( $record->time_in_force == 'GTDT' && $record->expiration_date < $expiration_date ) {
                        $sqls[] = "UPDATE  `$tbName` set order_status='cancelled' WHERE order_id='".$record->order_id."'";
                    }
                    if ( $record->stop_price <= $price  ) {
                        $sqls[] = "UPDATE  `$tbName` set order_status='open' WHERE order_id='".$record->order_id."'";
                    }
                }
            }
            $sqlString = implode(';', $sqls);
        }
        if (!empty($sqlString))
            DB::connection()->getPdo()->exec($sqlString);
    }
    public function PlaceSellAsk($customer_id, $price, $expiration_date) {
        $tbName = $this->getTableName();
        $buy_data = DB::table($tbName)->where('order_side', 'sell')/*->where('customer_id', '<>',$customer_id)*/->where('order_status', 'pending')->get()->toArray();
        $sqls = array();
        $sqlString = "";
        if ($buy_data) {
            foreach( $buy_data as $record ) {
                if ( $record->order_type == 'limit' ) {
                    if ( $record->time_in_force == 'GTDT' && $record->expiration_date < $expiration_date ) {
                        $sqls[] = "UPDATE  `$tbName` set order_status='cancelled' WHERE order_id='".$record->order_id."'";
                    }
                    if ( $record->limit_price <= $price  ) {
                        $sqls[] = "UPDATE  `$tbName` set order_status='open' WHERE order_id='".$record->order_id."'";
                    }
                }
                if ( $record->order_type == 'stop' ) {
                    if ( $record->time_in_force == 'GTDT' && $record->expiration_date < $expiration_date ) {
                        $sqls[] = "UPDATE  `$tbName` set order_status='cancelled' WHERE order_id='".$record->order_id."'";
                    }
                    if ( $record->stop_price >= $price  ) {
                        $sqls[] = "UPDATE  `$tbName` set order_status='open' WHERE order_id='".$record->order_id."'";
                    }
                }
                if ( $record->order_type == 'stoplimit' ) {
                    if ( $record->time_in_force == 'GTDT' && $record->expiration_date < $expiration_date ) {
                        $sqls[] = "UPDATE  `$tbName` set order_status='cancelled' WHERE order_id='".$record->order_id."'";
                    }
                    if ( $record->stop_price >= $price  ) {
                        $sqls[] = "UPDATE  `$tbName` set order_status='open' WHERE order_id='".$record->order_id."'";
                    }
                }
            }
            $sqlString = implode(';', $sqls);
        }
        if (!empty($sqlString))
            DB::connection()->getPdo()->exec($sqlString);
    }
    public function getOpenOrderData($customer_id, $want_asset, $offer_asset) {
        $orderbook_table = $this->getTableName();
        $sql = "select
        a.quantity size, a.price, 0 fee, a.updated_at time, a.order_status status, a.order_side,
        a.order_type, a.expiration_date, a.order_id, a.want_asset, a.offer_asset, a.customer_id, a.time_in_force,
        a.stop_price, a.order_date, if( b.filled_amount is null, 0, b.filled_amount ) filled
        from `$orderbook_table` a
        left join (select sum(a_amount) filled_amount, a_order_id from order_transaction group by a_order_id) b
        on a.order_id = b.a_order_id
        where a.order_status='open' and a.offer_asset='$offer_asset' and a.want_asset='$want_asset' and a.customer_id = $customer_id;";
        $data = DB::select($sql);
        return $data;
    }
    public function getFilledOrderList( $customer_id, $want_asset, $offer_asset ) {
        $orderbook_table = $this->getTableName();
        $sql = "SELECT ob.order_id,ot.a_amount size,ot.trade_price price, ot.a_commission*ot.trade_price fee,
        ot.updated_at time, ob.want_asset, ob.offer_asset, concat(ob.offer_asset, '-', ob.want_asset) product, ob.order_side, ob.order_type FROM `order_transaction` ot
              join `$orderbook_table` ob
              on ot.a_order_id = ob.order_id
              where ob.customer_id = $customer_id and ot.offer_asset='$offer_asset' and ot.want_asset = '$want_asset'";
        $data = DB::select($sql);
        return $data;
    }
    public function getOrderBookList($customer_id, $want_asset, $offer_asset, $order_side, $aggregation) {
        $orderbook_table = $this->getTableName();
        $sql = "select ob.order_id ,  ob.price, ob.quantity size, if( ot.filled_size is null, 0, ot.filled_size) filled_size, if( c.b_amount is null, '-', c.b_amount) my_size, ob.order_date
                from `$orderbook_table` ob
                left join (select sum(a_amount) filled_size, a_order_id 
                from order_transaction 
                where offer_asset='$offer_asset' and want_asset='$want_asset'
                group by a_order_id) ot
                on ob.order_id = ot.a_order_id
                left join (select sum(a.b_amount) b_amount, a.b_order_id
                from order_transaction a
                join `$orderbook_table` b
                on a.b_order_id=b.order_id
                where b.customer_id=$customer_id and b.offer_asset = '$offer_asset' and b.want_asset='$want_asset'
                group by a.b_order_id) c
                on c.b_order_id=ob.order_id
                where ob.order_status = 'open' and ob.order_type in ('limit', 'stop', 'market') and ob.order_side='$order_side' and ob.offer_asset='$offer_asset' and ob.want_asset='$want_asset' and ob.customer_id<>$customer_id /* and (ob.price mod 10) mod $aggregation=0 */
                order by order_date desc
                limit 0,50;
                ";
                // echo $sql;exit;
        $data = DB::select($sql);
        return $data;
    }
    public function OrderMatch($order) {

    }
    public function getMatchingDataForOrder( $order ) {
        $tbName = $this->getTableName();
        if ( $order['order_side'] == 'buy' ) {   // if order is buy-side, will get sell-side order book
            if ( $order['order_type'] == 'limit' ) {
                $data = DB::table($tbName)->where('customer_id', '<>', $order['customer_id'])->where('order_side','=', 'sell')->where('limit_price', '>=', $order['limit_price'])->where('order_status', '=', 'open')->orderBy('price', 'asc')->orderBy('updated_at', 'asc');
            }
            $data = DB::table($tbName)->where('customer_id', '<>', $order['customer_id'])->where('order_side','=', 'sell')->where('price', '<=', $price)->where('order_status', '=', 'open')->orderBy('price', 'asc')->orderBy('updated_at', 'asc');
        }
        else {  // if order is sell-side, will get buy-side order book
            $data = DB::table($tbName)->where('customer_id', '<>', $order['customer_id'])->where('order_side','=', 'buy')->where('price', '>=', $price)->where('order_status', '=', 'open')->orderBy('price', 'asc')->orderBy('updated_at', 'asc');
        }
    }
    public function getData() {
        return $this->where('order_type', 'limit')->get();
    }
}











