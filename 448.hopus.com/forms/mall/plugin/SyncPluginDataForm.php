<?php


namespace app\forms\mall\plugin;


use app\core\response\ApiCode;
use app\models\CorePlugin;
use app\models\Form;
use app\models\PluginCat;
use app\models\PluginCatRel;

class SyncPluginDataForm extends Form
{
    public function sync()
    {
        return [
            'code' => ApiCode::CODE_SUCCESS,
            'msg' => 'sync ok.',
        ];

    }
}