<?php

namespace backend\modules\api\controllers;

use backend\modules\api\components\CustomAuth;
use common\models\Fatura;
use common\models\Linhafatura;
use common\models\Produto;
use common\models\ProdutosHasTamanho;
use common\models\Profile;
use common\models\Tamanho;
use common\models\User;
use Yii;
use yii\rest\ActiveController;

class FaturaController extends ActiveController
{
    public $modelClass = 'common\models\Fatura';

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
                $faturaModel = new $this->modelClass;
                $recs = $faturaModel::find()->all();
                return ['count' => count($recs)];
            }
        }
        return 'Não foi possível contar as faturas.';
    }

    public function actionCriarfatura()
    {

        $request = Yii::$app->request;
        $userID = Yii::$app->params['id'];

        if ($user = User::find()->where(['id' => $userID])->one()) {
            // Verifica se o utilizador tem o papel "cliente"
            if (!Yii::$app->authManager->checkAccess($user->id, 'cliente')) {
                return 'O Utilizador introduzido não tem permissões de cliente';
            } else {
                $profile = Profile::find()->where(['user_id' => $user->id])->one();

                $fatura = new Fatura();

                $nif = $request->getBodyParam('nif');
                $metodoPagamentoId = $request->getBodyParam('metodo_pagamento');
                $metodoEntregaId = $request->getBodyParam('metodo_entrega');
                $encomenda = $request->getBodyParam('encomenda');

                $fatura->dataEmissao = date('Y-m-d');
                $fatura->horaEmissao = date('H:i:s');
                $fatura->valorTotal = 0.00;
                $fatura->ivaTotal = 0.00;
                //se o campo nif estiver preenchido
                if ($nif != null) {
                    $fatura->nif = $nif;
                }
                $fatura->metodopagamento_id = $metodoPagamentoId;
                $fatura->metodoentrega_id = $metodoEntregaId;
                $fatura->encomenda_id = $encomenda;
                $fatura->profile_id = $profile->id;
                $fatura->save();

                return 'Fatura criada com sucesso!';
            }
        }

        return 'Não foi criada a Fatura.';
    }

    public function actionCriarlinhafatura()
    {
        $request = Yii::$app->request;
        $faturaID = $request->getBodyParam('fatura');
        $produtoID = $request->getBodyParam('produto');
        $tamanho = $request->getBodyParam('tamanho');
        $quantidade = $request->getBodyParam('quantidade');

        $userID = Yii::$app->params['id'];

        if ($user = User::find()->where(['id' => $userID])->one()) {
            // Verifica se o utilizador tem o papel "cliente"
            if (!Yii::$app->authManager->checkAccess($user->id, 'cliente')) {
                return 'O Utilizador introduzido não tem permissões de cliente';
            } else {
                if (Fatura::find()->where(['id' => $faturaID])->one()) {
                    $produto = Produto::find()->where(['id' => $produtoID])->one();

                    //cria linha da fatura
                    $linhaFatura = new LinhaFatura();
                    $linhaFatura->dataVenda = date('Y-m-d');

                    //se for um produto que contenha tamanho associado
                    if ($tamanho != null) {
                        //vai buscar à tabela Tamanhos o tamanho inserido
                        if ($tamanhoID = Tamanho::find()->where(['referencia' => $tamanho])->one()) {
                            //se existir o tamanho selecionado ao produto pretendido
                            if (ProdutosHasTamanho::find()->where(['produto_id' => $produto->id, 'tamanho_id' => $tamanhoID->id])->one()) {
                                //acrescenta no nome do produto o respetivo tamanho
                                $linhaFatura->nomeProduto = $produto->nomeProduto . " - " . $tamanho;
                            } else {
                                return 'O tamanho introduzido não existe no produto escolhido.';
                            }
                        } else {
                            return 'O tamanho inserido não existe.';
                        }
                    } else {
                        //caso contrario, atribui apenas o nome do produto
                        $linhaFatura->nomeProduto = $produto->nomeProduto;
                    }
                    $linhaFatura->quantidade = $quantidade;
                    $linhaFatura->precoUnit = $produto->preco;
                    $linhaFatura->valorIva = $produto->iva->percentagem;
                    $linhaFatura->valorComIva = round($produto->preco * $quantidade + ($produto->iva->percentagem / 100), 2);
                    $linhaFatura->subtotal = round($produto->preco * $quantidade + ($produto->iva->percentagem / 100), 2);
                    $linhaFatura->fatura_id = $faturaID;
                    $linhaFatura->produto_id = $produto->id;
                    //se correr tudo bem com a criação da linha de fatura
                    if ($linhaFatura->save()) {
                        //atualiza os dados totais da fatura
                        $fatura = Fatura::find()->where(['id' => $faturaID])->one();
                        $fatura->valorTotal += round($produto->preco * $quantidade + ($produto->iva->percentagem / 100), 2);
                        $fatura->ivaTotal += $produto->iva->percentagem;
                        $fatura->save();
                    }
                    return 'Linha Fatura criada com sucesso!';
                }
            }
        }

        return 'Não foi possível criar a Linha da Fatura.';
    }
}