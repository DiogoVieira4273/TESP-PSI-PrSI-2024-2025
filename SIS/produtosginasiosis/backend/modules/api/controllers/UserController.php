<?php

namespace backend\modules\api\controllers;

use backend\models\UserForm;
use backend\modules\api\components\CustomAuth;
use common\models\LoginForm;
use common\models\User;
use Yii;
use yii\base\Security;
use yii\rest\ActiveController;

class UserController extends ActiveController
{
    public $modelClass = 'common\models\User';
    public $modelPerfilClass = 'common\models\Profile';

    /*public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::className(),
            //'only' => ['login'],
        ];
        return $behaviors;
    }*/


    public function actionCriaruser()
    {
        //$userModel = new User();
        //$profileModel = new Profile();

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
            return 'Criado com sucesso!';
        }

        return 'Falha na criação de um novo Cliente';

    }

    public function actionLogin()
    {
        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            if (Yii::$app->authManager->checkAccess(Yii::$app->user->id, 'cliente')) {
                $auth_key = User::findIdentityByAccessToken(Yii::$app->user->identity->getAuthKey());
                return $auth_key;
            } else {
                Yii::$app->user->logout();
                return 'Acesso negado. Apenas clientes podem aceder.';
            }
        }

        $model->password = ''; // Limpa o campo de senha após tentativa de login
    }

}