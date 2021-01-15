<?php

declare (strict_types=1);
namespace App\Model;

use Hyperf\DbConnection\Model\Model;
/**
 * @property int $id 
 * @property float $all_assets 
 * @property float $foreign_exchange 
 * @property float $blank_claims 
 * @property float $lending_rates 
 * @property float $exchange_rate 
 * @property int $timestamp 
 */
class CentralBankAsset extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'central_bank_assets';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'all_assets' => 'float', 'foreign_exchange' => 'float', 'blank_claims' => 'float', 'lending_rates' => 'float', 'exchange_rate' => 'float', 'timestamp' => 'integer'];
}