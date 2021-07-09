<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%jiayou_order}}".
 *
 * @property int $id
 * @property int $order_id
 * @property int $jiayou_id
 * @property string $js_discount_price
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property int $is_delete
 */
class JiayouOrder extends ModelActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%jiayou_order}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at', 'deleted_at'], 'required'],
            [['order_id','jiayou_id'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['js_discount_price'], 'string', 'max' => 65],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'jiayou_id' => 'jiayou_id',

            'order_id' => 'order_id',

            'js_discount_price' => 'js_discount_price',

            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At',
            'is_delete' => 'Is Delete',
        ];
    }

}
