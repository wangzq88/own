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

use Hyperf\HttpServer\Annotation\AutoController;
use function Hyperf\ViewEngine\view;
/**
 * @AutoController()
 */
class IndexController extends AbstractController
{
    public function index()
    {
        $data = ['breadcrumb' => [['text' => '央行历年资产表','href' => '']]];
        return (string) view('central',$data);
    }

    public function glass()
    {
        $data = ['breadcrumb' => [['text' => '玻璃库存','href' => '']]];
        return (string) view('glass',$data);
    }

    public function fiscal()
    {
        $data = ['breadcrumb' => [['text' => '财政收支数据','href' => '']]];
        return (string) view('fiscal',$data);
    }

    public function impexp()
    {
        $data = ['breadcrumb' => [['text' => '中国吸纳外资全口径数据演变','href' => '']]];
        return (string) view('impexp',$data);
    }

    public function investment()
    {
        $data = ['breadcrumb' => [['text' => '历年固定资产投资','href' => '']]];
        return (string) view('investment',$data);
    }

    public function industrial()
    {
        $data = ['breadcrumb' => [['text' => '三类规模以上工业企业利润增幅演变','href' => '']]];
        return (string) view('industrial',$data);
    }

    public function incomeexpend()
    {
        $data = ['title' => '中国城镇居民国民收支计算表','breadcrumb' => [['text' => '中国城镇居民国民收支计算表','href' => '']]];
        return (string) view('incomeexpend',$data);
    }
}
