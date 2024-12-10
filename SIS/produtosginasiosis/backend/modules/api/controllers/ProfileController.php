<?php

namespace backend\modules\api\controllers;

use yii\rest\ActiveController;

class ProfileController extends ActiveController
{
    public $modelClass = 'common\models\Profile';

    public function actionProfile($userID)
    {
        $profileModel = new $this->modelClass;

        $profile = $profileModel::find()->where(['user_id' => $userID])->one();

        return $profile;
    }
}