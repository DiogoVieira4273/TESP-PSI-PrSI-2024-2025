<?php

namespace backend\modules\api\controllers;

use backend\models\UserForm;
use common\models\LoginForm;
use common\models\Profile;
use common\models\User;
use Yii;
use yii\base\Security;
use yii\filters\auth\QueryParamAuth;
use yii\rest\ActiveController;

class UserController extends ActiveController
{
    public $modelClass = 'common\models\User';
    public $modelPerfilClass = 'common\models\Profile';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::className(),
            'only=> ['login'],
        ];
        return $behaviors;
    }


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

        $model->create($username, $email, $password, $nif, $morada, $telefone);

        /*$userModel->username = $username;
        $userModel->email = $email;
        $userModel->setPassword($password);
        $userModel->generateAuthKey();

        //regista o perfil do utilizador a criar
        $profileModel->nif = $nif;
        $profileModel->morada = $morada;
        $profileModel->telefone = $telefone;

        //se o utilizador ficar bem criado
        if ($userModel->save()) {
            //atribui a role cliente ao utilizador
            $auth = Yii::$app->authManager;
            $role = $auth->getRole('cliente');
            $auth->assign($role, $userModel->getId());

            //atribuir o perfil ao novo Utilizador
            $profileModel->user_id = $userModel->id;
            //se correr tudo bem
            if ($profileModel->save()) {
                return $userModel && $profileModel;
            }
        }*/

    }

    public function actionLogin()
    {
        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            if (Yii::$app->authManager->checkAccess(Yii::$app->user->id, 'cliente')) {
                return User::findIdentityByAccessToken(Yii::$app->user->identity->getAuthKey());
            } else {
                Yii::$app->user->logout();
                return 'Acesso negado. Apenas clientes podem aceder.';
            }
        }

        $model->password = ''; // Limpa o campo de senha após tentativa de login
    }


}