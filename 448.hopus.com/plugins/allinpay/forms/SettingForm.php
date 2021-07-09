<?php
/**
 * @copyright ©2019 浙江禾匠信息科技
 * Created by PhpStorm.
 * User: Andy - Wangjie
 * Date: 2019/8/14
 * Time: 9:22
 */

namespace app\plugins\allinpay\forms;

use app\core\response\ApiCode;
use app\models\Model;
use app\plugins\allinpay\models\AllinpayConfig;

class SettingForm extends Model
{
    public $orgid;
    public $version;
    public $cusid;
    public $appid;
    public $sub_appid;
    public $public_key;
    public $private_key;

    public function rules()
    {
        return [
            [['orgid', 'version', 'cusid', 'appid','sub_appid', 'private_key','public_key'], 'trim'],
            [['version', 'cusid', 'appid','sub_appid', 'private_key','public_key'], 'required'],
            ['public_key', function () {
                $this->public_key = $this->addBeginAndEnd(
                    '-----BEGIN PUBLIC KEY-----',
                    '-----END PUBLIC KEY-----',
                    $this->public_key
                );
            }],
            ['private_key', function () {
                $this->private_key = $this->addBeginAndEnd(
                    '-----BEGIN RSA PRIVATE KEY-----',
                    '-----END RSA PRIVATE KEY-----',
                    $this->private_key
                );
            }],
        ];
    }

    private function addBeginAndEnd($beginStr, $endStr, $data)
    {
        $data = $this->pregReplaceAll('/---.*---/', '', $data);
        $data = trim($data);
        $data = str_replace("\n", '', $data);
        $data = str_replace("\r\n", '', $data);
        $data = str_replace("\r", '', $data);
        $data = wordwrap($data, 64, "\r\n", true);

        if (mb_stripos($data, $beginStr) === false) {
            $data = $beginStr . "\r\n" . $data;
        }
        if (mb_stripos($data, $endStr) === false) {
            $data = $data . "\r\n" . $endStr;
        }
        return $data;
    }

    private function pregReplaceAll($find, $replacement, $s)
    {
        while (preg_match($find, $s)) {
            $s = preg_replace($find, $replacement, $s);
        }
        return $s;
    }

    public function save()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $model = AllinpayConfig::findOne([
            'mall_id' => \Yii::$app->mall->id,
        ]);
        if (!$model) {
            $model = new AllinpayConfig();
            $model->mall_id = \Yii::$app->mall->id;
        }
        $model->attributes = $this->attributes;
        if ($model->save()) {
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '保存成功。',
            ];
        } else {
            return $this->getErrorResponse($model);
        }
    }
}