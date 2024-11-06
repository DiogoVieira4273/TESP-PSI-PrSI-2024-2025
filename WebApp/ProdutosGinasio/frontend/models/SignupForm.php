<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'O nome de utilizador já existe.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'O email já existe.'],

            ['password', 'required'],
            ['password', 'string', 'min' => Yii::$app->params['user.passwordMinLength'], 'message' => 'A senha deve ter no mínimo 12 caracteres.'],
            ['password', 'string', 'max' => Yii::$app->params['user.passwordMaxLength'], 'message' => 'A senha deve ter no máximo 16 caracteres.'],
            [['password'], 'match', 'pattern' => Yii::$app->params['user.passwordPattern'], 'message' => 'A senha deve conter pelo menos uma letra maiúscula, números e símbolos especiais.'],
        ];
    }

    /**
     * Signs user up.
     *
     * @return bool whether the creating new account was successful and email was sent
     */
    public function signup()
    {
        if ($this->validate()) {
            if ($this->username !== 'cliente') {
                $user = new User();
                $user->username = $this->username;
                $user->email = $this->email;
                $user->setPassword($this->password);
                $user->generateAuthKey();
                $user->save(false);

                $auth = Yii::$app->authManager;
                $cliente = $auth->getRole('cliente');
                $auth->assign($cliente, $user->getId());

                $user->status = User::STATUS_ACTIVE;

                return $user->save();
            }
            else {
                Yii::$app->session->setFlash('error', 'Campo username inválido!');
            }
        }
        return null;
    }

    /**
     * Sends confirmation email to user
     * @param User $user user model to with email should be send
     * @return bool whether the email was sent
     */
    protected function sendEmail($user)
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Account registration at ' . Yii::$app->name)
            ->send();
    }
}
