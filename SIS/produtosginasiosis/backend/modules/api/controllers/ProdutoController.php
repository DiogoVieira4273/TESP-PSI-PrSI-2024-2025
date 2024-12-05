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

    public function actionBuscarpornome($nomeProduto)
    {
        $produtosmodel = new $this->modelClass;
        $produtos = $produtosmodel::find()->where(['nomeProduto' => $nomeProduto])->all();
        return $produtos;
    }

    public function actionBuscarportamanho($tamanho_id)
    {
        $produtosmodel = new $this->modelClass;

        // Consulta com join para a tabela intermediária
        $produtos = $produtosmodel::find()->innerJoin('produtos_has_tamanhos', 'produtos_has_tamanhos.produto_id = produtos.id')
            ->where(['produtos_has_tamanhos.tamanho_id' => $tamanho_id])
            ->all();

        if (empty($produtos)) {
         return [
             'status' => 'error',
             'message' => 'Nenhum produto encontrado com o ID de tamanho especificado.'
         ];
        }

        return [
            'status' => 'success',
            'data' => $produtos
            ];
    }

    public function actionBuscarpormarca($marca_id)
    {
        $produtosmodel = new $this->modelClass;
        $produtos = $produtosmodel::find()->where(['marca_id' => $marca_id])->all();

        if(empty($produtos)) {
            return ['message' => 'Nenhum produto encontrado com o ID de marca especificado.'];
        }

        return $produtos;
    }

    public function actionBuscarporcategoria($categoria_id)
    {
        $produtosmodel = new $this->modelClass;
        $produtos = $produtosmodel::find()->where(['categoria_id' => $categoria_id])->all();

        if(empty($produtos)) {
            return ['message' => 'Nenhum produto encontrado com o ID de categoria especificado.'];
        }

        return $produtos;
    }

    public function actionBuscarporgenero($genero_id)
    {
        $produtosmodel = new $this->modelClass;
        $produtos = $produtosmodel::find()->where(['genero_id' => $genero_id])->all();

        if(empty($produtos)) {
            return ['message' => 'Nenhum produto encontrado com o ID de genero especificado.'];
        }

        return $produtos;
    }






}
