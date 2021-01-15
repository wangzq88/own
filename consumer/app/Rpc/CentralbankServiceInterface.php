<?php


namespace App\Rpc;


interface CentralbankServiceInterface
{
    public function assets($start,$limit): array;
}