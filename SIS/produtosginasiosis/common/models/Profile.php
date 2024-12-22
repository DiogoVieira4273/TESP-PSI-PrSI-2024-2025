<?php

namespace common\models;

use mosquitto\phpMQTT;
use Yii;

/**
 * This is the model class for table "profiles".
 *
 * @property int $id
 * @property int $nif
 * @property string $morada
 * @property int $telefone
 * @property int $user_id
 *
 * @property Avaliacao[] $avaliacos
 * @property Carrinhocompra[] $carrinhocompras
 * @property Encomenda[] $encomendas
 * @property Fatura[] $faturas
 * @property Favorito[] $favoritos
 * @property User $user
 * @property Usocupao[] $usocupos
 */
class Profile extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'profiles';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nif', 'morada', 'telefone', 'user_id'], 'required'],
            [['nif', 'telefone', 'user_id'], 'integer'],
            [['morada'], 'string'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nif' => 'Nif',
            'morada' => 'Morada',
            'telefone' => 'Telefone',
            'user_id' => 'User ID',
        ];
    }

    /**
     * Gets query for [[Avaliacos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAvaliacos()
    {
        return $this->hasMany(Avaliacao::class, ['profile_id' => 'id']);
    }

    /**
     * Gets query for [[Carrinhocompras]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCarrinhocompras()
    {
        return $this->hasMany(Carrinhocompra::class, ['profile_id' => 'id']);
    }

    /**
     * Gets query for [[Encomendas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEncomendas()
    {
        return $this->hasMany(Encomenda::class, ['profile_id' => 'id']);
    }

    /**
     * Gets query for [[Faturas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFaturas()
    {
        return $this->hasMany(Fatura::class, ['profile_id' => 'id']);
    }

    /**
     * Gets query for [[Favoritos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFavoritos()
    {
        return $this->hasMany(Favorito::class, ['profile_id' => 'id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Gets query for [[Usocupos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsocupos()
    {
        return $this->hasMany(Usocupao::class, ['profile_id' => 'id']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $id = $this->id;
        $nif = $this->nif;
        $morada = $this->morada;
        $telefone = $this->telefone;
        $user = $this->user->username;

        $myObj = new \stdClass();
        $myObj->id = $id;
        $myObj->nif = $nif;
        $myObj->morada = $morada;
        $myObj->telefone = $telefone;
        $myObj->user = $user;

        if ($insert)
        {
            $myJSON = "Foi criado um perfil com o NIF ". $myObj->nif.", com a morada ". $myObj->morada." e com o telefone: ". $myObj->telefone. " para o utilizador com o nome de utilizador: ". $myObj->user;
            $this->FazPublishNoMosquitto("INSERT_PROFILE", $myJSON);
        }
        else
        {
            $myJSON = "Foi atualizado um perfil para o user ". $myObj->user;
            $this->FazPublishNoMosquitto("UPDATE_PROFILE", $myJSON);
        }
    }

    public function afterDelete()
    {
        parent::afterDelete();

        $id = $this->id;

        $myObj = new \stdClass();
        $myObj->id = $id;

        $myJSON = "Foi excluido um perfil para o user ". $myObj->user;
        $this->FazPublishNoMosquitto("DELETE_PROFILE", $myJSON);
    }

    public function FazPublishNoMosquitto($canal, $msg)
    {
        $server = "127.0.0.1";
        $port = 1883;
        $username = "";
        $password = "";
        $cliente_id = "phpMQTT-publisher";
        $mqtt = new phpMQTT($server, $port, $cliente_id);
        if ($mqtt->connect(true, NULL, $username, $password))
        {
            $mqtt->publish($canal, $msg, 0);
            $mqtt->close();
        }
        else
        {
            file_put_contents("debug.output", "Time out");
        }
    }
}
