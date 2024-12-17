<?php

namespace backend\modules\api\controllers;

use backend\modules\api\components\CustomAuth;
use common\models\User;
use Yii;
use yii\rest\ActiveController;

class ProdutoController extends ActiveController
{
    public $modelClass = 'common\models\Produto';

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
        $userID = Yii::$app->params['id'];

        if ($user = User::find()->where(['id' => $userID])->one()) {
            // Verifica se o utilizador tem o papel "cliente"
            if (!Yii::$app->authManager->checkAccess($user->id, 'cliente')) {
                return 'O Utilizador introduzido não tem permissões de cliente';
            } else {
                $produtosmodel = new $this->modelClass;
                $recs = $produtosmodel::find()->all();
                return ['count' => count($recs)];
            }
        }
        return 'Não foi possivel contar os produtos.';

    }

    public function actionProdutos()
    {
        $userID = Yii::$app->params['id'];

        if ($user = User::find()->where(['id' => $userID])->one()) {
            // Verifica se o utilizador tem o papel "cliente"
            if (!Yii::$app->authManager->checkAccess($user->id, 'cliente')) {
                return 'O Utilizador introduzido não tem permissões de cliente';
            } else {
                $produtosmodel = new $this->modelClass;
                $produtos = $produtosmodel::find()->all();
                return ['produtos' => $produtos];
            }
        }
        return 'Não foi possível obter os produtos.';

    }

    public function actionBuscarpornome($nomeProduto)
    {
        $userID = Yii::$app->params['id'];

        if ($user = User::find()->where(['id' => $userID])->one()) {
            // Verifica se o utilizador tem o papel "cliente"
            if (!Yii::$app->authManager->checkAccess($user->id, 'cliente')) {
                return 'O Utilizador introduzido não tem permissões de cliente';
            } else {
                $produtosmodel = new $this->modelClass;
                $produtos = $produtosmodel::find()->where(['nomeProduto' => $nomeProduto])->all();
                return $produtos;
            }
        }
        return 'Não foi possivel obter os produtos pelo nome pretendido.';
    }

    public function actionBuscarportamanho($tamanho_id)
    {
        $userID = Yii::$app->params['id'];

        if ($user = User::find()->where(['id' => $userID])->one()) {
            // Verifica se o utilizador tem o papel "cliente"
            if (!Yii::$app->authManager->checkAccess($user->id, 'cliente')) {
                return 'O Utilizador introduzido não tem permissões de cliente';
            } else {
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
        }
        return 'Não foi possivel obter os produtos pelo tamanho selecionado.';
    }

    public function actionBuscarpormarca($marca_id)
    {
        $userID = Yii::$app->params['id'];

        if ($user = User::find()->where(['id' => $userID])->one()) {
            // Verifica se o utilizador tem o papel "cliente"
            if (!Yii::$app->authManager->checkAccess($user->id, 'cliente')) {
                return 'O Utilizador introduzido não tem permissões de cliente';
            } else {
                $produtosmodel = new $this->modelClass;
                $produtos = $produtosmodel::find()->where(['marca_id' => $marca_id])->all();

                if (empty($produtos)) {
                    return ['message' => 'Nenhum produto encontrado com o ID de marca especificado.'];
                }

                return $produtos;
            }
        }
        return 'Não foi possivel obter os produtos pela marca selecionada.';
    }

    public function actionBuscarporcategoria($categoria_id)
    {
        $userID = Yii::$app->params['id'];

        if ($user = User::find()->where(['id' => $userID])->one()) {
            // Verifica se o utilizador tem o papel "cliente"
            if (!Yii::$app->authManager->checkAccess($user->id, 'cliente')) {
                return 'O Utilizador introduzido não tem permissões de cliente';
            } else {
                $produtosmodel = new $this->modelClass;
                $produtos = $produtosmodel::find()->where(['categoria_id' => $categoria_id])->all();

                if (empty($produtos)) {
                    return ['message' => 'Nenhum produto encontrado com o ID de categoria especificado.'];
                }

                return $produtos;
            }
        }
        return 'Não foi possivel obter os produtos pela categoria selecionada.';
    }

    public function actionBuscarporgenero($genero_id)
    {
        $userID = Yii::$app->params['id'];

        if ($user = User::find()->where(['id' => $userID])->one()) {
            // Verifica se o utilizador tem o papel "cliente"
            if (!Yii::$app->authManager->checkAccess($user->id, 'cliente')) {
                return 'O Utilizador introduzido não tem permissões de cliente';
            } else {
                $produtosmodel = new $this->modelClass;
                $produtos = $produtosmodel::find()->where(['genero_id' => $genero_id])->all();

                if (empty($produtos)) {
                    return ['message' => 'Nenhum produto encontrado com o ID de genero especificado.'];
                }

                return $produtos;
            }
        }
        return 'Não foi possivel obter os produtos pela genero selecionado.';
    }

    public function actionImagens($produto_id)
    {
        $userID = Yii::$app->params['id'];

        if ($user = User::find()->where(['id' => $userID])->one()) {
            // Verifica se o utilizador tem o papel "cliente"
            if (!Yii::$app->authManager->checkAccess($user->id, 'cliente')) {
                return 'O Utilizador introduzido não tem permissões de cliente';
            } else {
                $produtomodel = new $this->modelClass;

                $produtos = $produtomodel::findOne($produto_id);

                if (!$produtos) {
                    return [
                        'status' => 'error',
                        'message' => 'Produto não encontrado.'
                    ];
                }

                $imagens = $produtos->imagens;

                if (empty($imagens)) {
                    return [
                        'status' => 'error',
                        'message' => 'Nenhuma imagem encontrada para o produto especificado.'
                    ];
                }

                return [
                    'status' => 'success',
                    'data' => $imagens
                ];
            }
        }
        return 'Não foi possivel obter os imagems do produto selecionado.';
    }

    public function actionDetalhes($id)
    {
        $userID = Yii::$app->params['id'];

        if ($user = User::find()->where(['id' => $userID])->one()) {
            // Verifica se o utilizador tem o papel "cliente"
            if (!Yii::$app->authManager->checkAccess($user->id, 'cliente')) {
                return 'O Utilizador introduzido não tem permissões de cliente';
            } else {
                $produtomodel = new $this->modelClass;
                //$produto = $produtomodel::findOne(['id' => $id]);
                $produto = $produtomodel::find()->with('imagens')->where(['id' => $id])->one();
                if (!$produto) {
                    return [
                        'status' => 'error',
                        'message' => 'Nenhum produto encontrado.'
                    ];
                }
                if ($produto) {
                    $imagens = $produto->imagens;
                    foreach ($imagens as $imagem) {
                        //
                    }
                }

                return [
                    'status' => 'success',
                    'data' => $produto,
                    'images' => $imagens
                ];
            }
        }
        return 'Não foi possível obter os detalhes do produto pretendido.';
    }
}
