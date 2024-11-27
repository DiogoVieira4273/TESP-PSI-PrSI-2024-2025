<?php

namespace backend\models;

use common\models\Imagem;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class ImagemForm extends Model
{
    /**
     * @var UploadedFile[]
     */
    public $imagens;

    public function rules()
    {
        return [
            [['imagens'], 'file', 'maxFiles' => 0, 'extensions' => ['png', 'jpg', 'jpeg']],
        ];
    }

    public function upload($id)
    {
        // Verificar se há imagens para upload e se a validação foi bem-sucedida
        if ($this->validate()) {
            foreach ($this->imagens as $file) {
                // Gerar uma random string para o nome do arquivo
                $key = Yii::$app->getSecurity()->generateRandomString();

                // Caminho completo para salvar a imagem no backend
                $backendPath = Yii::getAlias('@backend/web/uploads/') . $key . '.' . $file->extension;

                // Diretório da pasta de uploads
                $backendDir = dirname($backendPath);

                // Verificar se o diretório existe; caso contrário, criar
                if (!is_dir($backendDir)) {
                    if (!mkdir($backendDir, 0777, true) && !is_dir($backendDir)) {
                        throw new \Exception('Falha ao criar o diretório: ' . $backendDir);
                    }
                }

                // Verificar se o diretório é gravável
                if (!is_writable($backendDir)) {
                    throw new \Exception('O diretório não é gravável: ' . $backendDir);
                }

                // Salvar o arquivo no diretório especificado
                if (!$file->saveAs($backendPath)) {
                    throw new \Exception('Erro ao salvar o arquivo em: ' . $backendPath);
                }

                // Criar o registro no banco de dados
                $imagem = new Imagem();
                $imagem->filename = $key . '.' . $file->extension;
                $imagem->produto_id = $id;

                // Salvar o registro no banco de dados
                if (!$imagem->save()) {
                    throw new \Exception('Erro ao salvar os dados da imagem no banco de dados.');
                }
            }
        }
    }

    public function update($id)
    {
        //se existir imagens para upload
        if ($this->validate()) {

            $imagem = Imagem::findOne($id);
            $file = $this->imagens[0];
            $key = Yii::$app->getSecurity()->generateRandomString();

            //atribuir a imagem na pasta
            $backendPath = Yii::getAlias('@backend/web/uploads/') . $key . '.' . $file->extension;

            //verificar se a imagem antiga a apagar existe na pasta
            $oldBackendPath = Yii::getAlias('@backend/web/uploads/') . $imagem->filename;

            if (file_exists($oldBackendPath)) {
                unlink($oldBackendPath);
            }

            //guardar a imagem
            $file->saveAs($backendPath);

            //alterar o registo na base dados
            $imagem->filename = $key . '.' . $file->extension;

            //guardar o registo na base dados
            $imagem->save();
        }
    }

    public function deleteAll($id)
    {
        //pesquisa na base dados as imagens referentes a um produto
        if ($imagens = Imagem::find()->where(['produto_id' => $id])->all()) {
            //iterar as imagens encontradas
            foreach ($imagens as $imagem) {
                //selecionar a imagem da pasta de imagens
                $backendPath = Yii::getAlias('@backend/web/uploads/') . $imagem->filename;

                //se encontrar a imagem
                if (file_exists($backendPath)) {
                    //apagar a imagem na pasta
                    unlink($backendPath);
                }

                //apagar na base dados
                $imagem->delete();
            }
        }
    }

    public function delete($id)
    {
        //pesquisa na base dados as imagens referentes a um produto
        if ($imagem = Imagem::find()->where(['id' => $id])->one()) {
            //selecionar a imagem na pasta de imagens
            $backendPath = Yii::getAlias('@backend/web/uploads/') . $imagem->filename;

            //se encontrar a imagem
            if (file_exists($backendPath)) {
                //apagar a imagem na pasta
                unlink($backendPath);
            }

            //apagar na base dados
            $imagem->delete();
        }
    }
}