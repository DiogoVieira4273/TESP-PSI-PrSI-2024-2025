<?php

namespace backend\modules\api\controllers;

use backend\modules\api\components\CustomAuth;
use Yii;
use yii\rest\ActiveController;

class TamanhoController extends ActiveController
{
    public $modelClass = 'common\models\Tamanho';

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
        $tamanhoModel = new $this->modelClass;
        $recs = $tamanhoModel::find()->all();
        return ['count' => count($recs)];
    }

    public function actionTamanhos()
    {
        $tamanhoModel = new $this->modelClass;
        $recs = $tamanhoModel::find()->all();
        return ['tamanhos' => $recs];
    }
}