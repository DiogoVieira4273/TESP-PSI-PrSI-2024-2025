<?php

namespace backend\modules\api\controllers;

use backend\modules\api\components\CustomAuth;
use common\models\Usocupao;
use Yii;
use yii\rest\ActiveController;
use yii\web\ForbiddenHttpException;

class UsocupaoController extends ActiveController
{

    public function behaviors()
    {
        Yii::$app->params['id'] = 0;
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => CustomAuth::className(),
        ];
        return $behaviors;
    }

    public function checkAccess($action, $model = null, $params = [])
    {
        if (Yii::$app->user->isGuest) {
            throw new ForbiddenHttpException('Proibido');
        }
    }

    public function actionUsocupao()
    {
        $request = Yii::$app->request;

        // Verificar se o cupão foi enviado
        $cupaoCodigo = $request->post('cupao');
        $cliente = $request->post('cliente');

        if ($request->post('cliente') != null && $cliente != null) {
            if ($cupaoCodigo) {
                // Buscar o cupão na base de dados
                $cupao = Usocupao::class->cupaodesconto::find()->where(['codigo' => $cupaoCodigo])->one();
                $profile = Usocupao::class->profile::find()->where(['user_id' => $cliente])->one();

                // Verifica se o cupão é válido e não expirou
                if ($cupao == null) {
                    // Se o cupão for inválido, exibe mensagem de erro
                    return 'Cupão inexistente.';
                } else if ($cupao && strtotime($cupao->dataFim) < time()) {
                    //Mensagem de erro, caso o cupão tenha expirado
                    return 'Cupão expirado.';
                } else if (Usocupao::class::find()->where(['cupaodesconto_id' => $cupao, 'profile_id' => $profile])->exists()) {
                    // Se o cupão for inválido, exibe mensagem de erro
                    return 'Cupão inválido.';
                } else if ($cupao && strtotime($cupao->dataFim) >= time()) {
                    $usocupao = new Usocupao();
                    $usocupao->cupaodesconto_id = $usocupao->cupaodesconto->id;
                    $usocupao->profile_id = $usocupao->profile->id;
                    $usocupao->dataUso = date('Y-m-d');
                    $usocupao->save();
                }
            }
        }
    }
}