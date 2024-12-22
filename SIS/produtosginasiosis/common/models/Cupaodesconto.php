<?php

namespace common\models;

use mosquitto\phpMQTT;

/**
 * This is the model class for table "cupoesdescontos".
 *
 * @property int $id
 * @property string $codigo
 * @property float $desconto
 * @property string $dataFim
 *
 * @property Usocupao[] $usocupos
 */
class Cupaodesconto extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cupoesdescontos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['codigo', 'desconto', 'dataFim'], 'required'],
            [['desconto'], 'number'],
            [['dataFim'], 'safe'],
            [['codigo'], 'string', 'max' => 50],
            [['codigo'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'codigo' => 'Codigo',
            'desconto' => 'Desconto',
            'dataFim' => 'Data Fim',
        ];
    }

    /**
     * Gets query for [[Usocupos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsocupos()
    {
        return $this->hasMany(Usocupao::class, ['cupaodesconto_id' => 'id']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $id = $this->id;
        $desconto = $this->desconto;
        $dataFim = $this->dataFim;

        $myObj = new \stdClass();
        $myObj->desconto = $desconto;
        $myObj->dataFim = $dataFim;

        if ($insert)
        {
            $myJSON = "Existe um novo cupão";
            $this->FazPublishNoMosquitto("INSERT_CUPAODESDESCONTO", $myJSON);
        }
        else
        {
            $myJSON = "Existe um cupão atualizado";
            $this->FazPublishNoMosquitto("UPDATE_CUPAODESDESCONTO", $myJSON);
        }
    }

    public function afterDelete()
    {
        parent::afterDelete();

        $id = $this->id;

        $myObj = new \stdClass();
        $myObj->id = $id;

        $myJSON = "Foi eliminado um cupão";

        $this->FazPublishNoMosquitto("DELETE_CUPAODESDESCONTO", $myJSON);
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
