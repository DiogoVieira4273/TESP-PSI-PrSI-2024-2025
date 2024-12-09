<?php

namespace backend\modules\api\controllers;

use backend\models\UserForm;
use backend\modules\api\components\CustomAuth;
use Yii;
use yii\rest\ActiveController;

class UserController extends ActiveController
{
    public $modelClass = 'common\models\User';

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // Verifique se as ações a executar são estas, se forem não precisa de validação de autenticação
        if ($this->action->id == 'login' || $this->action->id == 'criaruser') {
            unset($behaviors['authenticator']);
        } else {
            //caso contrário, precisa de validação de autenticação para efetuar as ações pretendidas
            $behaviors['authenticator'] = [
                'class' => CustomAuth::className(),
            ];
        }

        return $behaviors;
    }

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


    public function actionCriaruser()
    {
        //instancia o UserForm
        $model = new UserForm();

        //definir o cenário de criação
        $model->scenario = UserForm::SCENARIO_CREATE;

        $request = Yii::$app->request;

        $username = $request->getBodyParam('username');
        $email = $request->getBodyParam('email');
        $password = $request->getBodyParam('password');
        $nif = $request->getBodyParam('nif');
        $morada = $request->getBodyParam('morada');
        $telefone = $request->getBodyParam('telefone');

        if ($model->create($username, $email, $password, $nif, $morada, $telefone)) {
            return 'Cliente criado com sucesso!';
        }

        return 'Falha na criação de um novo cliente';

    }

}