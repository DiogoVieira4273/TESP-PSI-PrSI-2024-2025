<?php

namespace backend\modules\api\controllers;

use backend\modules\api\components\CustomAuth;
use common\models\User;
use Yii;
use yii\base\Security;
use yii\rest\ActiveController;

class UserController extends ActiveController
{
    public $modelClass = 'common\models\User';
    public $modelPerfilClass = 'common\models\Profile';


    public function actionCriaruser()
    {
        $userModel = new $this->modelClass;
        $profileModel = new $this->modelPerfilClass;
        $request = Yii::$app->request;

        $username = $request->getBodyParam('username');
        $email = $request->getBodyParam('email');
        $password = $request->getBodyParam('password_hash');
        $nif = $request->getBodyParam('nif');
        $morada = $request->getBodyParam('morada');
        $telefone = $request->getBodyParam('telefone');

        $userModel->username = $username;
        $userModel->email = $email;
        $userModel->password_hash = User::class->setPassword($password);
        $userModel->generateAuthKey();

        //atribui a role
        $auth = Yii::$app->authManager;
        $role = $auth->getRole('cliente');
        $auth->assign($role, $userModel->id);

        //regista o perfil do utilizador a criar
        $profileModel->nif = $nif;
        $profileModel->morada = $morada;
        $profileModel->telefone = $telefone;

        //se o utilizador ficar bem criado
        if ($userModel->save()) {
            $profileModel->user_id = $userModel->id;
            //se correr tudo bem
            if ($profileModel->save()) {
                return $userModel && $profileModel;
            }
        }

    }

}