<?php

namespace backend\modules\api\components;

use common\models\User;
use Yii;
use yii\filters\auth\AuthMethod;

class CustomAuth extends AuthMethod
{
    public function authenticate($user, $request, $response)
    {
        $authToken = $request->getQueryParam('auth_key');

        if (empty($authToken)) {
            return 'Token de acesso inválido';
        }

        $user = User::findIdentityByAccessToken($authToken);

        if (!$user) {
            return 'Não autenticado';
        }

        Yii::$app->params['id'] = $user->id;
        return $user;
    }
}