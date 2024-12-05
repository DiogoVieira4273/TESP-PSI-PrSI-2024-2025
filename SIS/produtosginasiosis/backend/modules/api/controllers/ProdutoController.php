<?php

namespace backend\modules\api\controllers;

use yii\rest\ActiveController;

class ProdutoController extends ActiveController
{
    public $modelClass = 'common\models\Produto';

    public function actionCount()
    {
        $produtosmodel = new $this->modelClass;
        $recs = $produtosmodel::find()->all();
        return ['count' => count($recs)];

    }

    public function actionProdutos()
    {
        $produtosmodel = new $this->modelClass;
        $produtos = $produtosmodel::find()->all();
        return ['produtos' => $produtos];

    }
}
