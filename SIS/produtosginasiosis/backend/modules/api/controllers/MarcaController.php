<?php

namespace backend\modules\api\controllers;

use backend\modules\api\components\CustomAuth;
use Yii;
use yii\rest\ActiveController;

class MarcaController extends ActiveController
{
    public $modelClass = 'common\models\Marca';

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
        $marcaModel = new $this->modelClass;
        $recs = $marcaModel::find()->all();
        return ['count' => count($recs)];
    }

    public function actionMarcas()
    {
        $marcaModel = new $this->modelClass;
        $recs = $marcaModel::find()->all();
        return ['marcas' => $recs];
    }
}