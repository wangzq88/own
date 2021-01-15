<?php


namespace App\Rpc;


interface GlassActivitiesServiceInterface
{
    public function getInventory($start,$limit): array;
}