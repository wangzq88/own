<?php
/**
 * Created by PhpStorm
 * User: 风哀伤
 * Date: 2020/12/21
 * Time: 2:15 下午
 * @copyright: ©2020 浙江禾匠信息科技
 * @link: http://www.zjhejiang.com
 */

namespace app\forms\common\goods;

use app\models\Goods;
use app\models\Model;

class GoodsAuth extends Model
{
    /**
     * @var bool $is_show_and_buy_auth
     * 是否支持会员等级浏览和购买权限
     */
    public $is_show_and_buy_auth = true;

    /**
     * @var bool $is_min_number
     * 是否支持起售
     */
    public $is_min_number = true;

    /**
     * @var bool $is_limit_buy
     * 是否支持限购
     */
    public $is_limit_buy = true;

    public $user;

    public static function create($sign, $config = [])
    {
        if ($sign === '') {
            $sign = 'mall';
        }
        try {
            $plugin = \Yii::$app->plugin->getPlugin($sign);
            $localConfig = $plugin->goodsAuth();
        } catch (\Exception $exception) {
            $localConfig = [
                'is_show_and_buy_auth' => true,
                'is_min_number' => true,
                'is_limit_buy' => true
            ];
        }

        $goodsAuth = new GoodsAuth(array_merge($localConfig, $config));
        $goodsAuth->user = $goodsAuth->user ?: \Yii::$app->user->identity;
        return $goodsAuth;
    }

    protected function getLevel()
    {
        $level = 0;
        if ($this->user) {
            $level = $this->user->identity->member_level;
        }
        return [-1, $level];
    }

    /**
     * @param Goods $goods
     * @param string $key show_goods_auth|buy_goods_auth
     * @return bool
     * @throws \Exception
     */
    public function checkShowBuyAuth($goods, $key)
    {
        if (!$this->is_show_and_buy_auth) {
            return true;
        }
        $levelArr = $this->getLevel();
        if ($goods->is_setting_show_and_buy_auth == 1) {
            $levelAuth = $goods->$key;
        } else {
            $globalAuth = \Yii::$app->mall->getMallSetting([$key]);
            $levelAuth = $globalAuth[$key];
        }
        $levelAuthArr = explode(',', $levelAuth);
        return !empty(array_intersect($levelArr, $levelAuthArr));
    }

    /**
     * @param Goods $goods
     * @return bool
     * @throws \Exception
     * 校验浏览权限
     */
    public function checkShowAuth($goods)
    {
        return $this->checkShowBuyAuth($goods, 'show_goods_auth');
    }

    /**
     * @param Goods $goods
     * @return bool
     * @throws \Exception
     * 校验购买权限
     */
    public function checkBuyAuth($goods)
    {
        return $this->checkShowBuyAuth($goods, 'buy_goods_auth');
    }
}
