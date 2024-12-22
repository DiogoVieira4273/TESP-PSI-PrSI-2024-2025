<?php

namespace common\models;

use mosquitto\phpMQTT;

/**
 * This is the model class for table "ivas".
 *
 * @property int $id
 * @property float $percentagem
 * @property int $vigor
 *
 * @property Produto[] $produtos
 */
class Iva extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ivas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['percentagem', 'vigor'], 'required'],
            [['percentagem'], 'number'],
            [['vigor'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'percentagem' => 'Percentagem',
            'vigor' => 'Vigor',
        ];
    }

    /**
     * Gets query for [[Produtos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProdutos()
    {
        return $this->hasMany(Produto::class, ['iva_id' => 'id']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $id = $this->id;
        $percentagem = $this->percentagem;
        $vigor = $this->vigor;

        $myObj = new \stdClass();
        $myObj->id = $id;
        $myObj->percentagem = $percentagem;
        $myObj->vigor = $vigor;

        if ($insert)
        {
            $myJSON = "Foi inserido um IVA".json_encode($myObj->percentagem);
            $this->FazPublishNoMosquitto("INSERT_IVA", $myJSON);
        }
        else
        {
            $myJSON = "Foi atualizado um IVA".json_encode($myObj->percentagem);
            $this->FazPublishNoMosquitto("UPDATE_IVA", $myJSON);
        }
    }

    public function afterDelete()
    {
        parent::afterDelete();

        $id = $this->id;
        $myObj = new \stdClass();
        $myObj->id = $id;
        $myJSON = "Foi deletado um IVA";

        $this->FazPublishNoMosquitto("DELETE_IVA", $myJSON);
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
