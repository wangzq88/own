<?php
/**
 * link: http://www.zjhejiang.com/
 * copyright: Copyright (c) 2020 浙江禾匠信息科技有限公司
 * author: xay
 */

namespace app\forms\api\index;


use app\forms\common\CommonAppConfig;
use app\plugins\diy\Plugin;

class TemplateForm extends NewIndexForm
{


    public function getData()
    {
        try {
            /* @var Plugin $plugin */
            $plugin = \Yii::$app->plugin->getPlugin('diy');
            $this->type = 'diy';
            $this->store_id;
            $page = $plugin->getTemplatePage1($this->page_id,$this->store_id);
        } catch (\Exception $exception) {
            \Yii::warning('diy页面报错');
            \Yii::warning($exception);
            $homePages = CommonAppConfig::getHomePageConfig();
            $homePages[] = [
                'key' => 'fxhb',
                'name' => '裂变红包'
            ];
            //$this->store_id = 11;
            $page = $this->getDefault($homePages);
            //$page = $plugin->getPage1($this->page_id,$this->store_id);
            $this->type = 'mall';
        }
        return [
            'home_pages' => $page,
            'type' => $this->type,
            'time' => date('Y-m-d H:i:s', time())
        ];
    }


    // 获取原始数据
    public function getDataOld()
    {
        try {
            /* @var Plugin $plugin */
            $plugin = \Yii::$app->plugin->getPlugin('diy');
            $this->type = 'diy';
            if (!is_callable([$plugin, 'getTemplatePage'])) {
                throw new \Exception('插件未更新');
            }
            $page = $plugin->getTemplatePage($this->page_id);
        } catch (\Exception $exception) {
            $exception->getMessage();
            \Yii::warning('diy页面报错');
            \Yii::warning($exception);
            $homePages = CommonAppConfig::getHomePageConfig();
            $homePages[] = [
                'key' => 'fxhb',
                'name' => '裂变红包'
            ];
            $page = $this->getDefault($homePages);
            $this->type = 'mall';
        }
        return [
            'home_pages' => $page,
            'type' => $this->type,
            'time' => date('Y-m-d H:i:s', time())
        ];
    }


}