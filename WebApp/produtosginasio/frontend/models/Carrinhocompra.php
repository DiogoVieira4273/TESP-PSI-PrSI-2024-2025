<?php

namespace frontend\models;

use common\models\Profile;

/**
 * This is the model class for table "carrinhocompras".
 *
 * @property int $id
 * @property string $dataVenda
 * @property int $quantidade
 * @property float $valorTotal
 * @property float $ivaTotal
 * @property int $profile_id
 *
 * @property Linhacarrinho[] $linhascarrinhos
 * @property Profile $profile
 */
class Carrinhocompra extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'carrinhocompras';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dataVenda', 'quantidade', 'valorTotal', 'ivaTotal', 'profile_id'], 'required'],
            [['dataVenda'], 'safe'],
            [['quantidade', 'profile_id'], 'integer'],
            [['valorTotal', 'ivaTotal'], 'number'],
            [['profile_id'], 'exist', 'skipOnError' => true, 'targetClass' => Profile::class, 'targetAttribute' => ['profile_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dataVenda' => 'Data Venda',
            'quantidade' => 'Quantidade',
            'valorTotal' => 'Valor Total',
            'ivaTotal' => 'Iva Total',
            'profile_id' => 'Profile ID',
        ];
    }

    /**
     * Gets query for [[Linhascarrinhos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLinhascarrinhos()
    {
        return $this->hasMany(Linhacarrinho::class, ['carrinhocompras_id' => 'id']);
    }

    /**
     * Gets query for [[Profile]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::class, ['id' => 'profile_id']);
    }
}
