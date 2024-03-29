<?php

namespace app\controllers\api;

use app\controllers\api\filters\LoginFilter;
use app\forms\api\StoreForm;

class StoreController extends ApiController
{


    public function actionList()
    {
        $form = new StoreForm();
        $form->attributes = \Yii::$app->request->get();
        return $form->search();
    }

    public function actionDetail()
    {
        $form = new StoreForm();
        $form->attributes = \Yii::$app->request->get();
        return $form->detail();
    }


    public function actionStore(){
        $form = new StoreForm();
        $form->attributes = \Yii::$app->request->get();
        return $this->asJson($form->getLatStore());
    }

    public function actionAllStore(){
       
        $form = new StoreForm();
        $form->attributes = \Yii::$app->request->get();
        return $this->asJson($form->getAllStore());
    }

}
