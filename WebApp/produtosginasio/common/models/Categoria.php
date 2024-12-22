<?php

namespace common\models;

use mosquitto\phpMQTT;

/**
 * This is the model class for table "categorias".
 *
 * @property int $id
 * @property string $nomeCategoria
 *
 * @property Produto[] $produtos
 */
class Categoria extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'categorias';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nomeCategoria'], 'required'],
            [['nomeCategoria'], 'unique','message'=>'Esta categoria ja esta criada'],
            [['nomeCategoria'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nomeCategoria' => 'Nome Categoria',
        ];
    }

    /**
     * Gets query for [[Produtos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProdutos()
    {
        return $this->hasMany(Produto::class, ['categoria_id' => 'id']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $id = $this->id;
        $nomeCategoria = $this->nomeCategoria;

        $myObj = new \stdClass();
        $myObj->id = $id;
        $myObj->nomeCategoria = $nomeCategoria;

        if ($insert)
        {
            $myJSON = "Foi inserido uma categoria: ".json_encode($myObj->nomeCategoria);
            $this->FazPublishNoMosquitto("INSERT_CATEGORIA", $myJSON);
        }
        else
        {
            $myJSON = "Foi atualizado uma categoria: ".json_encode($myObj->nomeCategoria);
            $this->FazPublishNoMosquitto("UPDATE_CATEGORIA", $myJSON);
        }
    }

    public function afterDelete()
    {
        parent::afterDelete();

        $cat_id = $this->id;
        $myObj = new \stdClass();
        $myObj->id = $cat_id;
        $myJSON = "Foi eliminado uma categoria: ".json_encode($myObj->nomeCategoria);

        $this->FazPublishNoMosquitto("DELETE_CATEGORIA", $myJSON);
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
