<?php

namespace app\controllers\mall;

use app\forms\mall\statistics\SendForm;

class SendStatisticsController extends MallController
{
    public function actionIndex()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new SendForm();
            $form->attributes = \Yii::$app->request->get();
            $form->attributes = \Yii::$app->request->post();
            return $this->asJson($form->search());
        } else {
            return $this->render('index');
        }
    }

    public function actionIndex1()
    {
        if (\Yii::$app->request->isAjax) {
            $form = new SendForm();
            $form->attributes = \Yii::$app->request->get();
            $form->attributes = \Yii::$app->request->post();
            return $this->asJson($form->search1());
        } else {
            if (\Yii::$app->request->post('flag') === 'EXPORT') {
                $form = new SendForm();
                $form->attributes = \Yii::$app->request->post();
                $form->search1();
                return false;
            } else {
                return $this->render('index1');
            }
        }
    }

    public function actionCard()
    {
        return $this->render('card');
    }

    public function actionCardDetailExport()
    {
        $form = new SendForm();
        $form->attributes = \Yii::$app->request->post();
        $res = $form->cardDetail();
        return $this->asJson($res);
    }

}
