<?php


namespace App\Controller;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Contract\RequestInterface;
/**
 * @AutoController()
 */
class IncomeexpenditureController extends AbstractController
{
    /**
     * @Inject()
     * @var \App\Rpc\IncomeExpenditureServiceInterface
     */
    private $incomeExpenditureService;

    public function list(RequestInterface $request)
    {
        $param = $request->all();
        if($param['page'] < 1)
        {
            $param['page'] = 1;
        }
        $start = ($param['page'] - 1)*$param['limit'];
        $response = $this->incomeExpenditureService->getList($start,$param['limit']);
        $result['list'] = $response['list'];
        $result['page'] = $param['page'];
        $result['total_page'] = $response['total_page'];
        $result['count'] = $response['count'];
        return $result;
    }
}