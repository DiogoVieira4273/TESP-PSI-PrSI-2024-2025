<?php

namespace backend\modules\api\controllers;

use backend\modules\api\components\CustomAuth;
use common\models\Fatura;
use common\models\Profile;
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
        $faturaModel = new $this->modelClass;
        $recs = $faturaModel::find()->all();
        return ['count' => count($recs)];
    }

    public function actionCriarfatura()
    {

        $request = Yii::$app->request;

        $token = $request->getBodyParam('auth_key');

        if ($user = User::find()->where(['auth_key' => $token])->one()) {
            // Verifica se o usuário tem o papel "cliente"
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
}