<?php


namespace app\forms\mall\statistics;


use app\core\response\ApiCode;
use app\forms\mall\export\CardDetailExport;
use app\forms\mall\export\SendStatisticsExport;
use app\models\Coupon;
use app\models\GoodsCardClerkLog;
use app\models\Jiayou;
use app\models\Order;
use app\models\JiayouOrder;
use app\models\Model;
use app\models\Store;
use app\models\UserCard;
use app\models\UserCoupon;
use app\models\UserInfo;

class SendForm extends Model
{
    public $name;
    public $order;
    public $type = 'coupon';//默认查询优惠券

    public $date_start;
    public $date_end;

    public $page;
    public $limit;

    public $flag;
    public $fields;

    public $platform;

    public $date;
    public $card_id;


    public function rules()
    {
        return [
            [['flag', 'type', 'platform','date'], 'string'],
            [['page', 'limit', 'card_id'], 'integer'],
            [['page',], 'default', 'value' => 1],
            [['name', 'order'], 'string'],
            [['date_start', 'date_end', 'fields'], 'trim'],
        ];
    }

    public function search()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        if ($this->type == 'card') {
            $query = $this->card_where();
        } else {
            $query = $this->coupon_where();
        }

        if ($this->flag == "EXPORT") {
            $new_query = clone $query;
            $this->export($new_query);
            return false;
        }

        $all_data = $query->asArray()->one();


        $maNum = UserCoupon::find()->where(['is_delete'=>0,'is_saoma'=>1])->count();


        $all_data = [
            'all_num' => !empty($all_data['all_num']) ? $all_data['all_num'] : 0,
            'unuse_num' => !empty($all_data['unuse_num']) ? $all_data['unuse_num'] : 0,
            'use_num' => !empty($all_data['use_num']) ? $all_data['use_num'] : 0,
            'end_num' => !empty($all_data['end_num']) ? $all_data['end_num'] : 0,
            'maNum' => $maNum?$maNum:0
        ];

        $list = $query->groupBy('`date`,name')
            ->page($pagination)
            ->asArray()
            ->all();



        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'pagination' => $pagination,
                'all_data' => $all_data,
                'list' => $list,
            ]
        ];
    }


    public function search1()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        if ($this->type == 'card') {
            $query = $this->card_where();
        } else {
            $query = $this->coupon_where();
        }



        //总成交
        $where = [];

        //时间查询
        if ($this->date_start) {
            $where['c.created_at'] = ['>=',$this->date_start . ' 00:00:00'];
        }

        if ($this->date_end) {
            $where['c.created_at'] = ['<=',$this->date_end . ' 23:59:59'];
        }

        $where['c.is_pay'] = 1;



        if ($this->flag == "EXPORT") {
            $this->export1($where);
            return false;
        }



        $jiayouOrderInfo = JiayouOrder::find()
            ->alias('a')
            ->leftJoin(['b' => Jiayou::tableName()], 'b.id=a.jiayou_id')
            ->leftJoin(['c' => Order::tableName()], 'c.id=a.order_id')
            ->where($where)
            ->select('b.youxing,sum(c.total_pay_price) as price')
            ->asArray()
            ->groupBy('youxing')
            ->all();

        //具体订单
        $list = JiayouOrder::find()
            ->alias('a')
            ->leftJoin(['b' => Jiayou::tableName()], 'b.id=a.jiayou_id')
            ->leftJoin(['c' => Order::tableName()], 'c.id=a.order_id')
            ->leftJoin(['d' => Store::tableName()], 'd.id=b.store_id')
            ->select('a.*,c.created_at as date,b.youxing,b.store_id,b.qianghao,b.youxing,b.js_money,c.total_pay_price,c.coupon_discount_price,d.name as storeName')
            ->where($where)
            ->page($pagination)
            ->orderBy('a.id desc')
            ->asArray()
            ->all();

        return [
            'code' => ApiCode::CODE_SUCCESS,
            'data' => [
                'pagination' => $pagination,
                'all_data' => $jiayouOrderInfo,
                'list' => $list,
            ]
        ];
    }


    protected function coupon_where()
    {
        $query = UserCoupon::find()->alias('uc')
            ->select("DATE_FORMAT(uc.`created_at`,'%Y-%m-%d') AS `date`,c.`name`,
                                COUNT(uc.`created_at`) AS all_num,
                                SUM(CASE uc.`is_use` WHEN 0 THEN 1 ELSE 0 END) AS `unuse_num`,
                                SUM(CASE uc.`is_use` WHEN 1 THEN 1 ELSE 0 END) AS `use_num`,
                                SUM(CASE WHEN uc.`is_saoma`= 1 AND uc.`is_delete` = 0 THEN 1 ELSE 0 END) AS `ma_num`,
                                SUM(CASE WHEN uc.`end_time`< NOW() AND uc.`is_use` = 0 THEN 1 ELSE 0 END) AS `end_num`")
            ->leftJoin(['c' => Coupon::tableName()], 'c.id = uc.coupon_id')
            ->leftJoin(['i' => UserInfo::tableName()], 'i.user_id = uc.user_id')
            ->where(['uc.mall_id' => \Yii::$app->mall->id, 'uc.is_delete' => 0]);

        if ($this->name) {
            $query->andWhere(['like', 'c.name', $this->name]);
        }
        //时间查询
        if ($this->date_start) {
            $query->andWhere(['>=', 'uc.created_at', $this->date_start . ' 00:00:00']);
        }

        if ($this->date_end) {
            $query->andWhere(['<=', 'uc.created_at', $this->date_end . ' 23:59:59']);
        }
        //平台标识查询
        if ($this->platform) {
            $query->andWhere(['i.platform' => $this->platform]);
        }
        $query->orderBy(!empty($this->order) ? $this->order : '`date` desc,uc.`created_at` desc');

        return $query;
    }

    protected function card_where()
    {
        $query = UserCard::find()->alias('uc')
            ->select("DATE_FORMAT(uc.`created_at`,'%Y-%m-%d') AS `date`,uc.`name`,
                                COUNT(`uc`.`created_at`) AS all_num,
                                SUM(CASE uc.`is_use` WHEN 0 THEN (`uc`.`number`-`uc`.`use_number`) ELSE 0 END) AS `unuse_num`,
                                SUM(CASE WHEN ISNULL(clerkCardLog.id) THEN 0 ELSE clerkCardLog.use_number END) AS `use_num`,
                                SUM(CASE WHEN uc.`end_time`< NOW() AND uc.`is_use` = 0 THEN (`uc`.`number`-`uc`.`use_number`) ELSE 0 END) AS `end_num`,
                                uc.card_id,
                                COUNT(`clerkCardLog` . `id`) AS log_num")
            ->leftJoin(['i' => UserInfo::tableName()], 'i.user_id = uc.user_id')
            ->leftJoin(['clerkCardLog' => GoodsCardClerkLog::tableName()], 'clerkCardLog.user_card_id = uc.id')
            ->where(['uc.mall_id' => \Yii::$app->mall->id, 'uc.is_delete' => 0]);

        if ($this->name) {
            $query->andWhere(['like', 'uc.name', $this->name]);
        }
        //时间查询
        if ($this->date_start) {
            $query->andWhere(['>=', 'uc.created_at', $this->date_start . ' 00:00:00']);
        }

        if ($this->date_end) {
            $query->andWhere(['<=', 'uc.created_at', $this->date_end . ' 23:59:59']);
        }
        //平台标识查询
        if ($this->platform) {
            $query->andWhere(['i.platform' => $this->platform]);
        }
        $query->orderBy(!empty($this->order) ? $this->order : '`date` desc,uc.`created_at` desc');

        return $query;
    }


    protected function export($query)
    {
        $exp = new SendStatisticsExport();
        $exp->fieldsKeyList = $this->fields;
        $exp->type = $this->type;
        $exp->export($query);
    }


    protected function export1($query)
    {
        $exp = new SendStatisticsExport();
        $exp->fieldsKeyList = $this->fields;
        $exp->type = $this->type;
        $exp->export1($query);
    }

    public function cardDetail()
    {
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }

        $query = UserCard::find()->with('clerkLog.store', 'clerkLog.user');
        $query->andWhere(['card_id' => $this->card_id, 'mall_id' => \Yii::$app->mall->id]);
        $query->andWhere(['>=', 'created_at' , $this->date . ' 00:00:00']);
        $query->andWhere(['<=', 'created_at' , $this->date . ' 23:59:59']);

        $exp = new CardDetailExport();
        $exp->fieldsKeyList = $this->fields;
        $exp->export($query);
    }
}