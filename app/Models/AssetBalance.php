<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetBalance extends Model
{
    //
    protected $table = 'tbl_asset_balance';

    public function resetBalance( $action, $asset_rows ) {
        foreach( $asset_rows as $asset_row ) {
            $data = $this->where('customer_id', $asset_row['customer_id'])->where('asset', $asset_row['asset'])->first();
            $new_row = array();
            if ( is_null($data) ) {
                if ( $action != 'deposit' ) continue;
                $new_row['customer_id'] = $asset_row['customer_id'];
                $new_row['asset'] = $asset_row['asset'];
                $new_row['balance'] = $asset_row['deposit_amount'];
                $new_row['created_at'] = date('Y-m-d H:i:s');
                $new_row['updated_at'] = date('Y-m-d H:i:s');
                $this->insert($new_row);
            }
            else {
                if ( $action == 'deposit' )
                    $new_row['balance'] = $data->balance*1 + $asset_row['deposit_amount'];
                else
                    $new_row['balance'] = $data->balance*1 - $asset_row['withdrawal_amount'];
                $new_row['updated_at'] = date('Y-m-d H:i:s');
                $this->where('customer_id', $asset_row['customer_id'])->where('asset', $asset_row['asset'])->update($new_row);
            }
        }
    }
    public function getCustomerAssetBalance( $customer_id, $asset ) {
        $data = $this->where('customer_id', $customer_id)->where('asset', $asset)->first();
        if ( is_null($data) ) return 0;
        return $data->balance;
    }
    public function getCustomerAssetBalanceInfo( $customer_id, $asset ) {
        $data = $this->where('customer_id', $customer_id)->where('asset', $asset)->first();
        if ( is_null($data) ) return array('asset_balance'=>0, 'frozen_balance'=>0);
        return array('asset_balance'=>$data->balance, 'frozen_balance'=>$data->frozen_balance);
    }
}
