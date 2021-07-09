<?php
/**
 * @copyright ©2019 浙江禾匠信息科技
 * Created by PhpStorm.
 * User: Andy - Wangjie
 * Date: 2019/8/14
 * Time: 9:20
 */

namespace app\plugins\allinpay\controllers;

use app\core\response\ApiCode;
use app\plugins\allinpay\forms\SettingForm;
use app\plugins\Controller;
use app\plugins\allinpay\models\AllinpayConfig;

class IndexController extends Controller
{
    public function actionSetting()
    {
        if (\Yii::$app->request->isAjax) {
            if (\Yii::$app->request->isPost) {
                $form = new SettingForm();
                $form->attributes = \Yii::$app->request->post();
                return $form->save();
            } else {
                $config = AllinpayConfig::find()
                    ->where([
                        'mall_id' => \Yii::$app->mall->id,
                    ])->asArray()->one();
                return [
                    'code' => ApiCode::CODE_SUCCESS,
                    'data' => $config ? $config : null,
                ];
            }
        } else {
            return $this->render('setting');
        }
    }

}