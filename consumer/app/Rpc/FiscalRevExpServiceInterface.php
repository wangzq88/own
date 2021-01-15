<?php


namespace App\Rpc;


interface FiscalRevExpServiceInterface
{
    public function getRevenueExpenditure($start,$limit): array;
}