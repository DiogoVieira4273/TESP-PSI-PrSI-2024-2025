<?php

namespace backend\modules\api\controllers;

use backend\modules\api\components\CustomAuth;
use Yii;
use yii\rest\ActiveController;

class CategoriaController extends ActiveController
{
    public $modelClass = 'common\models\Categoria';

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
        $categoriaModel = new $this->modelClass;
        $recs = $categoriaModel::find()->all();
        return ['count' => count($recs)];
    }

    public function actionCategorias()
    {
        $categoriaModel = new $this->modelClass;
        $recs = $categoriaModel::find()->all();
        return ['categorias' => $recs];
    }
}