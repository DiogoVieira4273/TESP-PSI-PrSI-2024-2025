<?php

namespace backend\models;

use common\models\Carrinhocompra;
use Yii;
use yii\base\Model;
use common\models\User;
use common\models\Profile;

class UserForm extends Model
{

    public $modelClass = 'common\models\User';

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

            ['status', 'default', 'value' => 10],

            ['nif', 'required'],
            ['nif', 'unique', 'targetClass' => '\common\models\Profile', 'message' => 'O nif inserido já existe.'],

            ['morada', 'required'],

            ['telefone', 'required'],
            ['telefone', 'unique', 'targetClass' => '\common\models\Profile', 'message' => 'O telefone inserido já existe.'],
        ];
    }

    public function create($username, $email, $password, $nif, $morada, $telefone)
    {
        //cria um novo Utilizador
        $user = new User();

        //atribui os respetivos os dados ao novo user
        $user->username = $username;
        $user->email = $email;
        $user->setPassword($password);
        $user->generateAuthKey();

        //se correu tudo bem ao gravar os dados do user
        if ($user->save(false)) {

            //atribui a role
            $auth = Yii::$app->authManager;
            $cliente = $auth->getRole('cliente');
            $auth->assign($cliente, $user->getId());

            //cria um novo perfil ao utilizador criado
            $profile = new Profile();

            //atribui o respetivo valor para cada campo do perfil
            $profile->nif = $nif;
            $profile->morada = $morada;
            $profile->telefone = $telefone;
            $profile->user_id = $user->id;

            //se o registo do perfil foi concluído
            if ($profile->save()) {
                $carrinhoCompras = new Carrinhocompra();
                $carrinhoCompras->quantidade = 0;
                $carrinhoCompras->valorTotal = 0.00;
                $carrinhoCompras->profile_id = $profile->id;
                $carrinhoCompras->save();
                return true;
            }
        }
        return null;
    }

    public function update($UserID, $username, $email, $password, $nif, $morada, $telefone)
    {
        //seleciona o user a editar
        $user = User::findOne(['id' => $UserID]);

        //altera os respetivos os dados do user a editar
        $user->username = $username;
        $user->email = $email;

        //se o campo da password não estiver vazia, edita a password
        if ($user->password != null) {
            $user->setPassword($password);
        }

        $user->generateAuthKey();

        //se a alteração dos dados do user foram gravados com sucesso
        if ($user->save(false)) {

            //seleciona o perfil do user a editar
            $profile = Profile::findOne(['user_id' => $user->id]);

            //altera os respetivos os dados do perfil do user a editar
            $profile->nif = $nif;
            $profile->morada = $morada;
            $profile->telefone = $telefone;

            //se o registo do perfil foi concluído
            if ($profile->save()) {
                return true;
            }
        }
        return null;
    }
}