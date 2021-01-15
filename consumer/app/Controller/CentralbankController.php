<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\Controller;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Contract\RequestInterface;
/**
 * @AutoController()
 */
class CentralbankController extends AbstractController
{
    /**
     * @Inject()
     * @var \App\Rpc\CentralbankServiceInterface
     */
    private $centralbankService;

    public function assets(RequestInterface $request)
    {
        $param = $request->all();
        if($param['page'] < 1)
        {
            $param['page'] = 1;
        }
        $start = ($param['page'] - 1)*$param['limit'];
        $response = $this->centralbankService->assets($start,$param['limit']);
        $result['list'] = $response['list'];
        $result['page'] = $param['page'];
        $result['total_page'] = $response['total_page'];
        return $result;
    }
}