<?php


namespace App\Rpc;


interface IndustrialProfitServiceInterface
{
    public function getList($start,$limit): array;
}