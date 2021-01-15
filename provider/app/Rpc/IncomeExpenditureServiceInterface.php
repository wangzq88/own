<?php


namespace App\Rpc;


interface IncomeExpenditureServiceInterface
{
    public function getList($start,$limit): array;
}