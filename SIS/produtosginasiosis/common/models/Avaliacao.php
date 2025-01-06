<?php

namespace common\models;


use common\mosquitto\phpMQTT;

/**
 * This is the model class for table "avaliacoes".
 *
 * @property int $id
 * @property string $descricao
 * @property int $produto_id
 * @property int $profile_id
 *
 * @property Produto $produto
 * @property Profile $profile
 */
class Avaliacao extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'avaliacoes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['descricao', 'produto_id', 'profile_id'], 'required'],
            [['descricao'], 'string'],
            [['produto_id', 'profile_id'], 'integer'],
            [['produto_id'], 'exist', 'skipOnError' => true, 'targetClass' => Produto::class, 'targetAttribute' => ['produto_id' => 'id']],
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
            'descricao' => 'Descricao',
            'produto_id' => 'Produto ID',
            'profile_id' => 'Profile ID',
        ];
    }

    /**
     * Gets query for [[Produto]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProduto()
    {
        return $this->hasOne(Produto::class, ['id' => 'produto_id']);
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

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $id = $this->id;
        $descricao = $this->descricao;
        $produto = $this->produto;
        $profile = $this->profile;

        $myObj = new \stdClass();
        $myObj->id = $id;
        $myObj->descricao = $descricao;
        $myObj->produto = $produto;
        $myObj->profile = $profile;

        if ($insert)
        {
            $myJSON = "Foi criada uma avaliação: ".json_encode($myObj->descricao)."para o produto ".json_encode($myObj->produto->id)." pelo profile ".json_encode($myObj->profile->id);
            $this->FazPublishNoMosquitto("INSERT_AVALIACAO", $myJSON);
        }
        else
        {
            $myJSON = "Foi atualizada uma avaliação.";
            $this->FazPublishNoMosquitto("UPDATE_AVALIACAO", $myJSON);
        }
    }

    public function afterDelete()
    {
        parent::afterDelete();

        $id = $this->id;

        $myObj = new \stdClass();
        $myObj->id = $id;

        $myJSON = "Foi eliminada uma avaliação.";
        $this->FazPublishNoMosquitto("DELETE_AVALIACAO", $myJSON);
    }


    public function FazPublishNoMosquitto($canal, $msg)
    {
        try {
            $server = "127.0.0.1";
            $port = 1883;
            $username = "";
            $password = "";
            $cliente_id = "phpMQTT-publisher";
            $mqtt = new phpMQTT($server, $port, $cliente_id);

            if ($mqtt->connect(true, NULL, $username, $password)) {
                $mqtt->publish($canal, $msg, 0);
                $mqtt->close();
            } else {
                file_put_contents("debug.output", "Time out");
            }
        } catch (\Throwable $e) {
            // Log the error for debugging
            file_put_contents("debug.output", "Erro ao publicar no MQTT: " . $e->getMessage());
        }
    }
}
