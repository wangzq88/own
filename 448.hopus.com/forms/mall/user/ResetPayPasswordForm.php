<?php
/**
 * link: http://www.zjhejiang.com/
 * copyright: Copyright (c) 2020 浙江禾匠信息科技有限公司
 * author: wxf
 */

namespace app\forms\mall\user;

use app\core\response\ApiCode;
use app\models\Model;
use app\models\User;

class ResetPayPasswordForm extends Model
{
    public $user_id;

    public function rules()
    {
        return [
            [['user_id' ], 'required'],
            [['user_id'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'user_id' => '用户ID',
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        };

        try {

            $user = $this->getUser();
            
            if (!$user) {
                throw new \Exception('会员不存在');
            }

            $userInfo = $user->userInfo;
            $userInfo->pay_password = '';
            $res = $userInfo->save();

            if (!$res) {
                throw new \Exception($this->getErrorMsg($userInfo));
            }
            
            return [
                'code' => ApiCode::CODE_SUCCESS,
                'msg' => '重置成功',
            ];
        }catch(\Exception $exception) {
            return [
                'code' => ApiCode::CODE_ERROR,
                'msg' => $exception->getMessage(),
                'line' => $exception->getLine()
            ];
        }
    }

    public function getUser()
    {
        $user = User::find()->andWhere([
            'mall_id' => \Yii::$app->mall->id,
            'id' => $this->user_id,
            'is_delete' => 0
        ])
            ->with('userInfo')
            ->one();

        return $user;
    }
}
