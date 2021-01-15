<?php


namespace App\Rpc;


interface AssetInvestmentServiceInterface
{
    public function getList($start,$limit): array;
}