<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%jiayou}}".
 *
 * @property int $id
 * @property int $mall_id
 * @property int $store_id
 * @property int $qianghao
 * @property string $youxing
 * @property string $js_money
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property int $is_delete
 * @property int $gz_id
 */
class Jiayou extends ModelActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%jiayou}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mall_id', 'created_at', 'updated_at', 'deleted_at'], 'required'],
            [['mall_id','store_id', 'qianghao', 'is_delete','gz_id'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['js_money','youxing'], 'string', 'max' => 65],
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

            'store_id' => 'store_id',
            'qianghao' => 'qianghao',
            'youxing' => 'youxing',
            'js_money' => 'js_money',
            'gz_id' => 'gz_id',

            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At',
            'is_delete' => 'Is Delete',
        ];
    }

}
