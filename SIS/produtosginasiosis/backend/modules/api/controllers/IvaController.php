<?php

namespace backend\modules\api\controllers;

use backend\modules\api\components\CustomAuth;
use Yii;
use yii\rest\ActiveController;

class IvaController extends ActiveController
{
    public $modelClass = 'common\models\Iva';

    public function behaviors()
    {
        Yii::$app->params['id'] = 0;
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => CustomAuth::className(),
        ];
        return $behaviors;
    }

    public function actionCount()
    {
        $ivaModel = new $this->modelClass;
        $recs = $ivaModel::find()->all();
        return ['count' => count($recs)];
    }

    public function actionIvas()
    {
        $ivaModel = new $this->modelClass;
        $recs = $ivaModel::find()->all();
        return ['ivas' => $recs];
    }
}