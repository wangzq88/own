<?php
declare(strict_types=1);

namespace App\Controller;


use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Contract\RequestInterface;

/**
 * @AutoController()
 */
class ImportexportController extends AbstractController
{
    /**
     * @Inject()
     * @var \App\Rpc\ImportExportServiceInterface
     */
    private $importExportService;

    public function index(RequestInterface $request)
    {
        $param = $request->all();
        if($param['page'] < 1)
        {
            $param['page'] = 1;
        }
        $start = ($param['page'] - 1)*$param['limit'];
        $result = $this->importExportService->getImportExport($start,$param['limit']);
        return $result;
    }
}