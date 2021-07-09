<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%jiayou}}".
 *
 * @property int $id
 * @property int $mall_id
 * @property int $type
 * @property string $zk
 * @property string $name
 * @property string $js_money
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property int $is_delete
 */
class JiayouGz extends ModelActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%jiayou_gz}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mall_id', 'created_at', 'updated_at', 'deleted_at'], 'required'],
            [['mall_id','is_delete'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at','type','name'], 'safe'],
            [['js_money','zk'], 'string', 'max' => 65],
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

            'name' => 'name',
            'type' => 'type',
            'zk' => 'zk',

            'js_money' => 'js_money',

            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At',
            'is_delete' => 'Is Delete',
        ];
    }

}
