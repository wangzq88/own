<?php


namespace App\Rpc;


interface ImportExportServiceInterface
{
    public function getImportExport($start,$limit): array;
}