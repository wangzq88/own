<?php
/**
 * Created by IntelliJ IDEA.
 * User: luwei
 * Date: 2019/8/2
 * Time: 9:37
 */

namespace app\plugins\allinpay;

use app\helpers\PluginHelper;
use app\models\Model;
use app\models\PayType;
use app\plugins\allinpay\forms\pay\Allinpay;
use app\plugins\allinpay\forms\TemplateInfo;
use app\plugins\allinpay\forms\TemplateSendForm;
use app\plugins\allinpay\models\AllinpayConfig;
use app\plugins\allinpay\models\AllinpayTemplate;

class Plugin extends \app\plugins\Plugin
{
    private $xTtPay;

    public function getMenus()
    {
        return [
            [
                'name' => '基础配置',
                'route' => 'plugin/allinpay/index/setting',
                'icon' => 'el-icon-setting',
            ]
        ];
    }

    public function getIndexRoute()
    {
        return 'plugin/allinpay/index/setting';
    }

    /**
     * 插件唯一id，小写英文开头，仅限小写英文、数字、下划线
     * @return string
     */
    public function getName()
    {
        return 'allinpay';
    }

    /**
     * 插件显示名称
     * @return string
     */
    public function getDisplayName()
    {
        return '通联支付';
    }

    public function getIsPlatformPlugin()
    {
        return true;
    }

    public function getAllinpay()
    {
        if ($this->allinpay) {
            return $this->allinpay;
        }
        $ttappConfig = $this->getTtConfig();	
        $config = [
            'appid' => $ttappConfig->appid,
            'cusid' => $ttappConfig->service_mchid,
            'version' => 11,
            'orgid' => $ttappConfig->mchid,
            'sub_appid' => $ttappConfig->service_appid,
			'public_key' => $ttappConfig->cert_pem,
			'private_key' => $ttappConfig->key_pem
        ];

        $this->xTtPay = new Allinpay($config);
        return $this->xTtPay;
    }

    public function getTtConfig()
    {
        $payType = PayType::findOne(['mall_id' => \Yii::$app->mall->id,'type' => 3]);
        if (!$payType  || !$payType->appid || !$payType->service_appid || !$payType->service_mchid || !$payType->cert_pem || !$payType->key_pem) {
            throw new \Exception('通联支付尚未配置。');
        }
        return $payType;
    }

    /**
     * @param string|array $param
     * @return array|\yii\db\ActiveRecord[]
     * 获取所有模板消息
     */
    public function getTemplateList($param = '*')
    {
        $list = AllinpayTemplate::find()->where(['mall_id' => \Yii::$app->mall->id])->select($param)->all();

        return $list;
    }

    /**
     * @param array $attributes
     * @return bool
     * @throws \Exception
     * 后台保存模板消息
     */
    public function addTemplateList($attributes)
    {
        foreach ($attributes as $item) {
            if (!isset($item['tpl_name'])) {
                throw new \Exception('缺少必要的参数tpl_name');
            }
            if (!isset($item[$item['tpl_name']])) {
                throw new \Exception("缺少必要的参数{$item['tpl_name']}");
            }
            $tpl = AllinpayTemplate::findOne(['mall_id' => \Yii::$app->mall->id, 'tpl_name' => $item['tpl_name']]);
            $tplId = $item[$item['tpl_name']];
            if ($tpl) {
                if ($tpl->tpl_id != $tplId) {
                    $tpl->tpl_id = $tplId;
                    if (!$tpl->save()) {
                        throw new \Exception((new Model())->getErrorMsg($tpl));
                    } else {
                        continue;
                    }
                } else {
                    continue;
                }
            } else {
                $tpl = new AllinpayTemplate();
                $tpl->mall_id = \Yii::$app->mall->id;
                $tpl->tpl_name = $item['tpl_name'];
                $tpl->tpl_id = $tplId;
                if (!$tpl->save()) {
                    throw new \Exception((new Model())->getErrorMsg($tpl));
                } else {
                    continue;
                }
            }
        }
        return true;
    }

    public function templateSender()
    {
        return new TemplateSendForm();
    }

    public function getHeaderNav()
    {
        return [
            'name' => $this->getDisplayName(),
            'url' => \Yii::$app->urlManager->createUrl([$this->getIndexRoute()]),
            'new_window' => true,
        ];
    }

    public function getNotSupport()
    {
        return [
            'navbar' => [
                '/plugins/step/index/index',
                '/plugins/scratch/index/index',
                '/pages/live/index',
                'plugin-private://wx2b03c6e691cd7370/pages/live-player-plugin',
                '/plugins/community/list/list',
                '/plugins/community/recruit/recruit',
                '/plugins/community/index/index',
            ],
            'home_nav' => [
                '/plugins/step/index/index',
                '/plugins/scratch/index/index',
                '/pages/live/index',
                'plugin-private://wx2b03c6e691cd7370/pages/live-player-plugin',
                '/plugins/community/list/list',
                '/plugins/community/recruit/recruit',
                '/plugins/community/index/index',
            ],
            'user_center' => [
                '/plugins/step/index/index',
                '/plugins/scratch/index/index',
                '/pages/live/index',
                'plugin-private://wx2b03c6e691cd7370/pages/live-player-plugin',
                '/plugins/community/list/list',
                '/plugins/community/recruit/recruit',
                '/plugins/community/index/index',
            ],
        ];
    }

    public function getTemplateData($type, $data)
    {
        return (new TemplateInfo($type, $data))->getData();
    }

    // 获取平台图标
    public function getPlatformIconUrl()
    {
        return [
            [
                'key' => $this->getName(),
                'name' => $this->getDisplayName(),
                'icon' => PluginHelper::getPluginBaseAssetsUrl($this->getName()) . '/img/ttapp.png'
            ]
        ];
    }
}
