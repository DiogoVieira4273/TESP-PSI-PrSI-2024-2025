<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\filters\auth\CompositeAuth;
use yii\rest\ActiveController;
use yii\web\ForbiddenHttpException;

class CupaodescontoController extends ActiveController
{
    public $modelClass = 'common\models\Cupaodesconto';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
        ];
        return $behaviors;
    }

    public function checkAccess($action, $model = null, $params = [])
    {
        if (Yii::$app->user->isGuest) {
            throw new ForbiddenHttpException('Não autorizado');
        }
    }

    public function actionCount()
    {
        $cupaodescontomodel = new $this->modelClass;
        $recs = $cupaodescontomodel::find()->all();
        return ['count' => count($recs)];
    }

    public function actionCupaodesconto()
    {
        $cupaodescontomodel = new $this->modelClass;
        $recs = $cupaodescontomodel::find()->all();
        return ['cupaodesconto' => $recs];
    }
}