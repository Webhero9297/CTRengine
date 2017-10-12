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
    public function getData() {
        return $this->where('order_type', 'limit')->get();
    }
}











