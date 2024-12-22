<?php

namespace common\models;

use mosquitto\phpMQTT;

/**
 * This is the model class for table "generos".
 *
 * @property int $id
 * @property string $referencia
 *
 * @property Produto[] $produtos
 */
class Genero extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'generos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['referencia'], 'required'],
            [['referencia'], 'unique','message'=>'Este genero ja esta criado'],
            [['referencia'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'referencia' => 'Referencia',
        ];
    }

    /**
     * Gets query for [[Produtos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProdutos()
    {
        return $this->hasMany(Produto::class, ['genero_id' => 'id']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $id = $this->id;
        $referencia = $this->referencia;

        $myObj = new \stdClass();
        $myObj->id = $id;
        $myObj->referencia = $referencia;

        if ($insert)
        {
            $myJSON = "Foi inserido um género: ".json_encode($myObj->referencia);
            $this->FazPublishNoMosquitto("INSERT_GENERO", $myJSON);
        }
        else
        {
            $myJSON = "Foi atualizado um género: ".json_encode($myObj->referencia);
            $this->FazPublishNoMosquitto("UPDATE_GENERO", $myJSON);
        }
    }

    public function afterDelete()
    {
        parent::afterDelete();

        $id = $this->id;

        $myObj = new \stdClass();
        $myObj->id = $id;

        $myJSON = "Foi eliminado um género.";

        $this->FazPublishNoMosquitto("DELETE_GENERO", $myJSON);
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
