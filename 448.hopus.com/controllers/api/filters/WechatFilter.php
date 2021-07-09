<?php
/**
 * Created by PhpStorm
 * User: 风哀伤
 * Date: 2020/12/2
 * Time: 2:05 下午
 * @copyright: ©2020 浙江禾匠信息科技
 * @link: http://www.zjhejiang.com
 */

namespace app\controllers\api\filters;


use app\core\response\ApiCode;
use yii\base\ActionFilter;

class WechatFilter extends ActionFilter
{
    public function beforeAction($action)
    {
        if (\Yii::$app->appPlatform !== 'wechat') {
            return true;
        }
        try {
            $plugin = \Yii::$app->plugin->getPlugin('wechat');
            if (!method_exists($plugin, 'getOther')) {
                return true;
            }
            $config = $plugin->getOther();
            $routerList = [];
            foreach ($config as $value) {
                if ($value['check'] == 1) {
                    $routerList = array_merge($routerList, $value['router']);
                }
            }
            $router = \Yii::$app->requestedRoute;
            if (!in_array($router, $routerList)) {
                return true;
            }
            if (!method_exists($plugin, 'updateSubscribe')) {
                return true;
            }
            $res = $plugin->updateSubscribe();
            \Yii::warning($res);
            if ($res['code'] == 0 && $res['data']['subscribe'] == 1) {
                return true;
            }
            \Yii::$app->response->data = [
                'code' => ApiCode::CODE_WECHAT_SUBSCRIBE,
                'msg' => '请先关注公众号'
            ];
            return false;
        } catch (\Exception $exception) {
            \Yii::warning($exception);
            return true;
        }
    }
}
