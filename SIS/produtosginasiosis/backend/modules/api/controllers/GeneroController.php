<?php

namespace backend\modules\api\controllers;

use backend\modules\api\components\CustomAuth;
use Yii;
use yii\rest\ActiveController;

class GeneroController extends ActiveController
{
    public $modelClass = 'common\models\Genero';

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
        $generoModel = new $this->modelClass;
        $recs = $generoModel::find()->all();
        return ['count' => count($recs)];
    }

    public function actionGeneros()
    {
        $generoModel = new $this->modelClass;
        $recs = $generoModel::find()->all();
        return ['generos' => $recs];
    }
}