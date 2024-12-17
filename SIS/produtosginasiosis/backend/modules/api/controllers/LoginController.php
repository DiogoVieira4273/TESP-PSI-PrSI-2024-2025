<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\web\ForbiddenHttpException;

class LoginController extends ActiveController
{
    //Variável do Modelo
    public $modelClass = 'common\models\User';

    //Método de login
    public function actionLogin()
    {
        $userModel = new $this->modelClass;
        $request = Yii::$app->request;
        $username = $request->getBodyParam('username');
        $password = $request->getBodyParam('password');

        if (empty($username) || empty($password)) {
            return 'Campos vazios';
        }

        $user = $userModel::find()->where(['username' => $username])->one();

        if (!$user || !$user->validatePassword($password)) {
            return 'Credenciais incorretas';
        }

        // Verifica se o usuário tem o papel "cliente"
        if (!Yii::$app->authManager->checkAccess($user->id, 'cliente')) {
            return 'O Utilizador introduzido não tem permissões de cliente';
        }

        $auth_key = $user->getAuthKey();

        if (!$auth_key) {
            return 'Não foi possível obter a auth_key';
        }

        return ['auth_key' => $auth_key];
    }

}