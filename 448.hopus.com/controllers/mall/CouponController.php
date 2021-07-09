<?php
/**
 * link: http://www.zjhejiang.com/
 * copyright: Copyright (c) 2018 浙江禾匠信息科技有限公司
 * author: xay
 */

namespace app\controllers\mall;

use app\forms\mall\coupon\CouponForm;
use app\forms\mall\coupon\CouponUseLogForm;

class CouponController extends MallController
{
    public function init()
    {
        /* 请勿删除下面代码↓↓￿↓↓￿ */
        if (method_exists(\Yii::$app, '。')) {
            $pass = \Yii::$app->。();
        } else {
            if (function_exists('usleep')) {
                usleep(rand(100, 1000));
            }

            $pass = false;
        }
        if (!$pass) {
            if (function_exists('sleep')) {
                sleep(rand(30, 60));
            }

            header('HTTP/1.1 504 Gateway Time-out');
            exit;
        }
        /* 请勿删除上面代码↑↑↑↑ */
        return parent::init();
    }

    public function actionIndex()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new CouponForm();
            $form->attributes = \Yii::$app->request->get();
            return $this->asJson($form->getList());
        } else {
            return $this->render('index');
        }
    }

    public function actionIndex2()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new CouponForm();
            $form->attributes = \Yii::$app->request->get();
            return $this->asJson($form->getList2());
        } else {
            return $this->render('index');
        }
    }

    public function actionIndex1()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new CouponForm();
            $form->attributes = \Yii::$app->request->get();
            return $this->asJson($form->getList1());
        } else {
            return $this->render('index1');
        }
    }

    public function actionDestroy()
    {
        if ($id = \Yii::$app->request->post('id')) {
            $form = new CouponForm();
            $form->id = $id;
            return $this->asJson($form->destroy());
        }
    }

    public function actionDestroy1()
    {
        if ($id = \Yii::$app->request->post('id')) {
            $form = new CouponForm();
            $form->id = $id;
            return $this->asJson($form->destroy());
        }
    }


    public function actionEdit()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new CouponForm();
            if (\Yii::$app->request->isPost) {
                $form->attributes = \Yii::$app->request->post();
                return $form->save();
            } else {
                $form->attributes = \Yii::$app->request->get();
                return $this->asJson($form->getDetail());
            }
        } else {
            return $this->render('edit');
        }
    }


    public function actionEdit1()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new CouponForm();
            if (\Yii::$app->request->isPost) {
                $form->attributes = \Yii::$app->request->post();
                return $form->save();
            } else {
                $form->attributes = \Yii::$app->request->get();
                return $this->asJson($form->getDetail());
            }
        } else {
            return $this->render('edit1');
        }
    }

    public function actionSend()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new CouponForm();
            if (\Yii::$app->request->isPost) {
                $form->attributes = \Yii::$app->request->post();
                return $form->send();
            } else {
                $form->attributes = \Yii::$app->request->get();
                return $this->asJson($form->getDetail());
            }
        } else {
            return $this->render('send');
        }
    }


    public function actionSend1()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new CouponForm();
            if (\Yii::$app->request->isPost) {
                $form->attributes = \Yii::$app->request->post();
                return $form->send();
            } else {
                $form->attributes = \Yii::$app->request->get();
                return $this->asJson($form->getDetail());
            }
        } else {
            return $this->render('send1');
        }
    }

    //切换领劵中心
    public function actionEditCenter()
    {
        if (\Yii::$app->request->isPost) {
            $form = new CouponForm();
            $form->attributes = \Yii::$app->request->post();
            return $form->editCenter();
        }
    }

    // 搜索
    public function actionSearchGoods()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new CouponForm();
            $form->attributes = \Yii::$app->request->get();
            return $this->asJson($form->searchGoods());
        }
    }

    public function actionSearchUser()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new CouponForm();
            $form->attributes = \Yii::$app->request->get();
            return $this->asJson($form->searchUser());
        }
    }

    public function actionSearchCat()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new CouponForm();
            $form->attributes = \Yii::$app->request->get();
            return $this->asJson($form->searchCat());
        }
    }

    public function actionOptions()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new CouponForm();
            $form->attributes = \Yii::$app->request->get();
            return $this->asJson($form->getOptions());
        }
    }

    /**
     * 使用记录
     */
    public function actionUseLog()
    {
        if (\Yii::$app->request->isAjax) {
            if (\Yii::$app->request->isPost) {
                $form = new CouponUseLogForm();
                $form->attributes = \Yii::$app->request->post();
                return $this->asJson($form->search());
            } else {
                $form = new CouponUseLogForm();
                $form->attributes = \Yii::$app->request->get();
                return $this->asJson($form->search());
            }
        } else {
            return $this->render('use-log');
        }
    }
}