<?php

namespace backend\modules\api\controllers;

use yii\filters\auth\QueryParamAuth;
use yii\rest\ActiveController;

class FavoritoController extends ActiveController
{
    public $modelClass = 'common\models\Favorito';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::className(),
            //'only' => ['login'],
        ];
        return $behaviors;
    }

    public function actionCount()
    {
        $favoritosmodel = new $this->modelClass;
        $recs = $favoritosmodel::find()->all();
        return ['count' => count($recs)];

    }

    public function actionFavoritos()
    {
        $favoritosmodel = new $this->modelClass;
        $produtos = $favoritosmodel::find()->all();
        return ['produtos' => $produtos];
    }


}