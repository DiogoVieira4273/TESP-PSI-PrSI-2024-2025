<?php

namespace common\models;

use mosquitto\phpMQTT;
use PhpParser\Comment\Doc;

/**
 * This is the model class for table "marcas".
 *
 * @property int $id
 * @property string $nomeMarca
 *
 * @property Fornecedor[] $fornecedores
 * @property Produto[] $produtos
 */
class Marca extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'marcas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nomeMarca'], 'required'],
            [['nomeMarca'], 'unique','message'=>'Esta marca ja esta criada'],
            [['nomeMarca'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nomeMarca' => 'Nome Marca',
        ];
    }

    /**
     * Gets query for [[Fornecedores]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFornecedores()
    {
        return $this->hasMany(Fornecedor::class, ['marca_id' => 'id']);
    }

    /**
     * Gets query for [[Produtos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProdutos()
    {
        return $this->hasMany(Produto::class, ['marca_id' => 'id']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $id = $this->id;
        $nomeMarca = $this->nomeMarca;

        $myObj = new \stdClass();
        $myObj->id = $id;
        $myObj->nomeMarca = $nomeMarca;

        if ($insert)
        {
            $myJSON = "Foi criada uma marca ".json_encode($myObj->nomeMarca);
            $this->FazPublishNoMosquitto("INSERT_MARCA", $myJSON);
        }
        else
        {
            $myJSON = "Foi atualizada uma marca ".json_encode($myObj->nomeMarca);
            $this->FazPublishNoMosquitto("UPDATE_MARCA", $myJSON);
        }
    }

    public function afterDelete()
    {
        parent::afterDelete();

        $marca_id = $this->id;

        $myObj = new \stdClass();
        $myObj->id = $marca_id;
        $myJSON = "Foi removida uma marca.";

        $this->FazPublishNoMosquitto("DELETE_MARCA", $myJSON);
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
