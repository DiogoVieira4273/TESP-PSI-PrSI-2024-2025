<?php

namespace common\models;

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
 * @property Avaliaco[] $avaliacos
 * @property Carrinhocompra[] $carrinhocompras
 * @property Encomenda[] $encomendas
 * @property Fatura[] $faturas
 * @property Favorito[] $favoritos
 * @property User $user
 * @property Usocupo[] $usocupos
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
        return $this->hasMany(Avaliaco::class, ['profile_id' => 'id']);
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
        return $this->hasMany(Usocupo::class, ['profile_id' => 'id']);
    }
}
