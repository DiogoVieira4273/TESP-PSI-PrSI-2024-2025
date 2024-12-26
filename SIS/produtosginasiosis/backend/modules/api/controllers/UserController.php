<?php

namespace backend\modules\api\controllers;

use backend\models\UserForm;
use backend\modules\api\components\CustomAuth;
use common\models\Profile;
use common\models\User;
use Yii;
use yii\rest\ActiveController;

class UserController extends ActiveController
{
    public $modelClass = 'common\models\User';
    public $modelProfileClass = 'common\models\Profile';

    public function behaviors()
    {
        Yii::$app->params['id'] = 0;
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => CustomAuth::className(),
        ];
        return $behaviors;
    }

    public function actionAtualizaruser()
    {
        $userID = Yii::$app->params['id'];

        if ($user = User::find()->where(['id' => $userID])->one()) {
            // Verifica se o utilizador tem o papel "cliente"
            if (!Yii::$app->authManager->checkAccess($user->id, 'cliente')) {
                return 'O Utilizador introduzido não tem permissões de cliente';
            } else {
                //instancia o UserForm
                $model = new UserForm();

                $request = Yii::$app->request;

                $username = $request->getBodyParam('username');
                $email = $request->getBodyParam('email');
                $password = $request->getBodyParam('password');
                $nif = $request->getBodyParam('nif');
                $morada = $request->getBodyParam('morada');
                $telefone = $request->getBodyParam('telefone');

                if ($model->update($user->id, $username, $email, $password, $nif, $morada, $telefone)) {

                }
                return 'Cliente atualizado com sucesso!';

                return 'Falha na atualização do cliente pretendido';
            }
        }
        return 'Não foi realizado a atualização dos dados.';

    }

    public function actionDadosuserprofile()
    {
        $userID = Yii::$app->params['id'];

        if ($user = User::find()->where(['id' => $userID])->one()) {
            // Verifica se o utilizador tem o papel "cliente"
            if (!Yii::$app->authManager->checkAccess($user->id, 'cliente')) {
                return 'O Utilizador introduzido não tem permissões de cliente';
            } else {

                $cliente = User::find()->where(['id' => $userID])->one();
                $profile = Profile::find()->where(['user_id' => $userID])->one();

                if ($cliente != null && $profile != null) {
                    return ['username' => $cliente->username, 'email' => $cliente->email, 'nif' => $profile->nif, 'morada' => $profile->morada, 'telefone' => $profile->telefone];
                } else {
                    return 'Falha no obter dos dados do cliente especifico';
                }
            }
        }
        return 'Não foi possivel os dados do utilizador';
    }
}