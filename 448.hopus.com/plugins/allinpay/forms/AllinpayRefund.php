<?php
/**
 * Created by PhpStorm.
 * User: 王志强
 * Date: 2019/2/18
 * Time: 16:05
 * @copyright: ©2019 wangzq
 * @link: http://www.zjhejiang.com
 */

namespace app\plugins\allinpay\forms;


use app\core\payment\PaymentException;
use app\forms\common\refund\BaseRefund;
use app\models\PaymentRefund;
use app\plugins\allinpay\Plugin;

class AllinpayRefund extends BaseRefund
{
    /**
     * @param PaymentRefund $paymentRefund
     * @param \app\models\PaymentOrderUnion $paymentOrderUnion
     * @return bool|mixed
     * @throws PaymentException
     */
    public function refund($paymentRefund, $paymentOrderUnion)
    {
        $t = \Yii::$app->db->beginTransaction();
        try {
            $plugin = new Plugin();
            $allinPay = $plugin->getAllinpay();
            //判断是否当天交易
            $udate = date('Y-m-d',strtotime($paymentOrderUnion->updated_at));
            $tdate = date('Y-m-d');
            if($udate == $tdate)
            {
                // 只能撤销当天的交易，全额退款，实时返回退款结果
                $result = $allinPay->cancel([
                    'oldreqsn' => $paymentRefund->out_trade_no,
                    'reqsn' => $paymentRefund->order_no,
                    'trxamt' => $paymentRefund->amount * 100              
                ]);
            }
            else
            {
                // 支持部分金额退款，隔天交易退款
                $result = $allinPay->refund([
                    'oldreqsn' => $paymentRefund->out_trade_no,
                    'reqsn' => $paymentRefund->order_no,
                    'trxamt' => $paymentRefund->amount * 100              
                ]);
            }
            \Yii::warning('退款交易相应');
            \Yii::warning($result);            
            if($result['retcode'] == 'SUCCESS' && in_array($result['trxstatus'],['0000','2008','2000']))
            {
                $this->save($paymentRefund);
                $t->commit();
                return true;
            }
            else
            {
                $t->rollBack();
                throw new PaymentException('退款交易异常');                
            }
        } catch (\Exception $e) {
            $t->rollBack();
            throw new PaymentException('请检查支付证书是否填写正确');
        }
    }

    /**
     * @param PaymentRefund $paymentRefund
     * @throws \Exception
     */
    private function save($paymentRefund)
    {
        $paymentRefund->is_pay = 1;
        $paymentRefund->pay_type = 1;
        if (!$paymentRefund->save()) {
            throw new \Exception($this->getErrorMsg($paymentRefund));
        }
    }
}
