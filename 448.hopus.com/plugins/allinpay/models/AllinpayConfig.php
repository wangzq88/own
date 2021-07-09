<?php

namespace app\plugins\allinpay\models;

use Yii;

/**
 * This is the model class for table "{{%allinpay_config}}".
 *
 * @property int $id
 * @property int $mall_id
 * @property string $cusid 商户号
 * @property string $orgid
 * @property string $version
 * @property string $appid
 * @property string $sub_appid
 * @property string $public_key
 * @property string $private_key
 * @property string $created_at
 * @property string $updated_at
 */
class AllinpayConfig extends \app\models\ModelActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%allinpay_config}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mall_id'], 'required'],
            [['mall_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['public_key', 'private_key'], 'string'],
            [['cusid', 'appid', 'orgid', 'version'], 'string', 'max' => 64],
            [['sub_appid'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mall_id' => 'Mall ID',
            'cusid' => '商户号',
            'appid' => 'Pay App ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
