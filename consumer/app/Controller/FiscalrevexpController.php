<?php
declare(strict_types=1);

namespace App\Controller;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Logger\LoggerFactory;

/**
 * @AutoController()
 */

class FiscalrevexpController extends AbstractController
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    /**
     * @Inject()
     * @var \App\Rpc\FiscalRevExpServiceInterface
     */
    private $fiscalRevExpService;

    public function __construct(LoggerFactory $loggerFactory)
    {
        // 第一个参数对应日志的 name, 第二个参数对应 config/autoload/logger.php 内的 key
        $this->logger = $loggerFactory->get('log', 'default');
    }

    public function revexp(RequestInterface $request)
    {
        $param = $request->all();
        if($param['page'] < 1)
        {
            $param['page'] = 1;
        }
        $start = ($param['page'] - 1)*$param['limit'];
        $result = $this->fiscalRevExpService->getRevenueExpenditure($start,$param['limit']);
        return $result;
    }
}