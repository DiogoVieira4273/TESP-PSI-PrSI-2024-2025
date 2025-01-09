<?php

namespace backend\modules\api\controllers;

use backend\modules\api\components\CustomAuth;
use common\models\Carrinhocompra;
use common\models\Linhacarrinho;
use common\models\Produto;
use common\models\ProdutosHasTamanho;
use common\models\Profile;
use Yii;
use yii\filters\auth\QueryParamAuth;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UnauthorizedHttpException;

class CarrinhocompraController extends ActiveController
{
    public $modelClass = 'common\models\Carrinhocompra';

    public function behaviors()
    {
        Yii::$app->params['id'] = 0;
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => CustomAuth::className(),
        ];
        return $behaviors;
    }

    public function actionCarrinho()
    {
        // Vai obter o ID do user autenticado
        $userId = Yii::$app->params['id'];

        // Vai buscar o perfil associado ao usuário
        $profile = Profile::findOne(['user_id' => $userId]);
        if (!$profile) {
            Yii::$app->response->statusCode = 400;
            return ['message' => 'Perfil não encontrado.'];
        }

        // Vai buscar o carrinho associado ao perfil
        $carrinho = Carrinhocompra::findOne(['profile_id' => $profile->id]);
        if (!$carrinho) {
            Yii::$app->response->statusCode = 400;
            return ['message' => 'Carrinho não encontrado.'];
        }

        $baseUrl = 'http://172.22.21.204' . Yii::getAlias('@web/uploads/');

        // Estruturar os dados para resposta
        $linhasCarrinho = [];
        foreach ($carrinho->linhascarrinhos as $linha) {
            $produto = $linha->produto;
            $tamanho = $linha->tamanho;

            $linhaDados = [
                'id' => $linha->id,
                'quantidade' => $linha->quantidade,
                'precoUnit' => $linha->precoUnit,
                'valorIva' => $linha->valorIva,
                'valorComIva' => $linha->valorComIva,
                'subtotal' => $linha->subtotal,
                'carrinhocompras_id' => $carrinho->id,
                'produto_nome' => $produto ? $produto->nomeProduto : 'Produto não encontrado',
                'tamanho_nome' => $tamanho ? $tamanho->referencia : 'N/A',
                'imagem' => null
            ];

            // Verifica se o produto tem imagens associadas
            if (!empty($produto->imagens)) {
                // Vai buscar a primeira imagem
                $primeiraImagem = $produto->imagens[0];

                // Monta a URL completa da imagem
                $linhaDados['imagem'] = $baseUrl . $primeiraImagem->filename;
            }

            $linhasCarrinho[] = $linhaDados;
        }

        return [
            'quantidade_total' => $carrinho->quantidade,
            'valorTotal' => $carrinho->valorTotal,
            'linhasCarrinho' => $linhasCarrinho,
        ];
    }

    public function actionAdicionarprodutocarrinho()
    {
        $userId = Yii::$app->params['id'];

        $profile = Profile::findOne(['user_id' => $userId]);
        if (!$profile) {
            Yii::$app->response->statusCode = 400;
            return ['message' => 'Perfil não encontrado.'];
        }

        $carrinho = Carrinhocompra::findOne(['profile_id' => $profile->id]);
        if (!$carrinho) {
            Yii::$app->response->statusCode = 400;
            return ['message' => 'Carrinho não encontrado.'];
        }

        $request = Yii::$app->request;
        $produto = $request->getBodyParam('produto');

        $produto = Produto::find()->where(['id' => $produto])->one();

        if ($produto == null) {
            Yii::$app->response->statusCode = 400;
            return ['message' => 'Produto não encontrado.'];
        }

        if ($produto->quantidade > 0) {
            //atualizar a quantidade do Produto
            $produto->quantidade -= 1;
            $produto->save();

            $linhaCarrinho = Linhacarrinho::find()->where(['carrinhocompras_id' => $carrinho->id, 'produto_id' => $produto->id])->one();

            if ($linhaCarrinho) {
                $linhaCarrinho->quantidade += 1;
            } else {
                $linhaCarrinho = new Linhacarrinho();
                $linhaCarrinho->quantidade = 1;
                $linhaCarrinho->carrinhocompras_id = $carrinho->id;
                $linhaCarrinho->produto_id = $produto->id;
            }

            $linhaCarrinho->precoUnit = $produto->preco;
            $linhaCarrinho->valorIva = $produto->iva->percentagem;

            $percentualIva = $produto->iva->percentagem * 100;
            $subtotalSemIva = $linhaCarrinho->precoUnit * $linhaCarrinho->quantidade;
            $valorIvaAplicado = $subtotalSemIva * ($percentualIva / 100);
            $subtotalComIva = $subtotalSemIva + $valorIvaAplicado;

            $linhaCarrinho->valorComIva = round($subtotalComIva, 2);
            $linhaCarrinho->subtotal = round($subtotalComIva, 2);

            $linhaCarrinho->save();

            $carrinho->quantidade += 1;
            $carrinho->valorTotal += $linhaCarrinho->subtotal;

            return [
                'linhaCarrinho' => $linhaCarrinho,
            ];
        } else {
            Yii::$app->response->statusCode = 400;
            return ['message' => 'Stock insuficiente.'];
        }

        Yii::$app->response->statusCode = 400;
        return ['message' => 'Não foi possivel adicionar o produto ao carrinho.'];
    }

    public function actionAumentarquantidade()
    {
        $request = Yii::$app->request;
        $linha = $request->getBodyParam('linhaCarrinho');

        //encontrar a linha do carrinho
        $linhaCarrinho = Linhacarrinho::findOne($linha);

        if (!$linhaCarrinho) {
            Yii::$app->response->statusCode = 400;
            return ['message' => 'Produto não encontrado no carrinho.'];
        }

        //verifica se o produto tem tamanho associado
        $temTamanho = ProdutosHasTamanho::find()
            ->where(['produto_id' => $linhaCarrinho->produto_id])
            ->andWhere(['tamanho_id' => $linhaCarrinho->tamanho_id])
            ->exists();

        if ($temTamanho) {
            //produtos com tamanho associado
            $produtostamanho = ProdutosHasTamanho::findOne([
                'produto_id' => $linhaCarrinho->produto_id,
                'tamanho_id' => $linhaCarrinho->tamanho_id,
            ]);

            if ($produtostamanho && $produtostamanho->quantidade > 0) {
                $produtostamanho->quantidade -= 1;

                //atualiza a quantidade total na tabela Produtos
                $produto = Produto::findOne($linhaCarrinho->produto_id);
                $produto->quantidade -= 1;

                $produtostamanho->save();
                $produto->save();
            } else {
                Yii::$app->response->statusCode = 400;
                return ['message' => 'Stock insuficiente para aumentar a quantidade.'];
            }
        } else {
            //produtos sem tamanho associado
            $produto = Produto::findOne($linhaCarrinho->produto_id);

            if ($produto->quantidade > 0) {
                $produto->quantidade -= 1;
                $produto->save();
            } else {
                Yii::$app->response->statusCode = 400;
                return ['message' => 'Stock insuficiente para aumentar a quantidade.'];
            }
        }

        //aumentar quantidade do produto no carrinho
        $linhaCarrinho->quantidade += 1;

        $subtotalSemIva = $linhaCarrinho->precoUnit * $linhaCarrinho->quantidade;
        $percentualIva = $linhaCarrinho->produto->iva->percentagem;
        $valorIvaAplicado = $subtotalSemIva * $percentualIva / 100;
        $subtotalComIva = $subtotalSemIva + $valorIvaAplicado;

        $linhaCarrinho->subtotal = round($subtotalComIva, 2);
        $linhaCarrinho->valorComIva = round($subtotalComIva, 2);

        if ($linhaCarrinho->save()) {
            $this->updateCarrinhoTotal($linhaCarrinho->carrinhocompras_id);

            return [
                'status' => 'success',
                'message' => 'Quantidade aumentada com sucesso.',
            ];
        } else {
            Yii::$app->response->statusCode = 400;
            return ['message' => 'Erro ao atualizar a linha do carrinho.'];
        }
    }

    public function actionDiminuirquantidade()
    {
        $request = Yii::$app->request;
        $linha = $request->getBodyParam('linhaCarrinho');

        $linhaCarrinho = Linhacarrinho::findOne($linha);

        if (!$linhaCarrinho) {
            Yii::$app->response->statusCode = 400;
            return ['message' => 'Produto não encontrado no carrinho.'];
        }

        //verificar se a quantidade é maior que 1
        if ($linhaCarrinho->quantidade <= 1) {
            Yii::$app->response->statusCode = 400;
            return ['message' => 'A quantidade não pode ser menor que 1.'];
        }

        //verifica se o produto tem tamanho associado
        $temTamanho = ProdutosHasTamanho::find()
            ->where(['produto_id' => $linhaCarrinho->produto_id])
            ->andWhere(['tamanho_id' => $linhaCarrinho->tamanho_id])
            ->exists();

        if ($temTamanho) {
            //produtos com tamanho associado
            $produtostamanho = ProdutosHasTamanho::findOne([
                'produto_id' => $linhaCarrinho->produto_id,
                'tamanho_id' => $linhaCarrinho->tamanho_id,
            ]);

            if ($produtostamanho) {
                $produtostamanho->quantidade += 1;

                //atualiza a quantidade total na tabela Produtos
                $produto = Produto::findOne($linhaCarrinho->produto_id);
                $produto->quantidade += 1;

                if ($produtostamanho->save() && $produto->save()) {
                    // Continuação do cálculo do carrinho
                } else {
                    Yii::$app->response->statusCode = 400;
                    return ['message' => 'Erro ao atualizar o stock.'];
                }
            } else {
                Yii::$app->response->statusCode = 400;
                return ['message' => 'Tamanho não encontrado no stock para este produto.'];
            }
        } else {
            //produtos sem tamanho associado
            $produto = Produto::findOne($linhaCarrinho->produto_id);

            if ($produto) {
                $produto->quantidade += 1;

                if (!$produto->save()) {
                    Yii::$app->response->statusCode = 400;
                    return ['message' => 'Erro ao atualizar o stock do produto.'];
                }
            } else {
                Yii::$app->response->statusCode = 400;
                return ['message' => 'Produto não encontrado no stock.'];
            }
        }

        //diminuir a quantidade do produto no carrinho
        $linhaCarrinho->quantidade -= 1;

        $subtotalSemIva = $linhaCarrinho->precoUnit * $linhaCarrinho->quantidade;
        $percentualIva = $linhaCarrinho->produto->iva->percentagem;
        $valorIvaAplicado = $subtotalSemIva * $percentualIva / 100;
        $subtotalComIva = $subtotalSemIva + $valorIvaAplicado;

        $linhaCarrinho->subtotal = round($subtotalComIva, 2);
        $linhaCarrinho->valorComIva = round($subtotalComIva, 2);

        if ($linhaCarrinho->save()) {
            $this->updateCarrinhoTotal($linhaCarrinho->carrinhocompras_id);

            return [
                'status' => 'success',
                'message' => 'Quantidade diminuída com sucesso.',
            ];
        } else {
            Yii::$app->response->statusCode = 400;
            return ['message' => 'Erro ao atualizar a linha do carrinho.'];
        }
    }

    public function actionApagarprodutocarrinho()
    {
        $userId = Yii::$app->params['id'];

        $profile = Profile::findOne(['user_id' => $userId]);
        if (!$profile) {
            Yii::$app->response->statusCode = 400;
            return ['message' => 'Perfil não encontrado.'];
        }

        $carrinho = Carrinhocompra::findOne(['profile_id' => $profile->id]);
        if (!$carrinho) {
            Yii::$app->response->statusCode = 400;
            return ['message' => 'Carrinho não encontrado.'];
        }

        $request = Yii::$app->request;
        $linha = $request->getBodyParam('linhaCarrinho');

        $linhaCarrinho = Linhacarrinho::find()->where(['id' => $linha, 'carrinhocompras_id' => $carrinho->id])->one();
        if ($linhaCarrinho == null) {
            Yii::$app->response->statusCode = 400;
            return ['message' => 'Não foi possivel obter a linha do carrinho.'];
        }

        //verifica se o produto tem tamanho associado
        $temTamanho = ProdutosHasTamanho::find()
            ->where(['produto_id' => $linhaCarrinho->produto_id])
            ->andWhere(['tamanho_id' => $linhaCarrinho->tamanho_id])
            ->exists();

        if ($temTamanho) {
            //produtos com tamanho associado
            $produtostamanho = ProdutosHasTamanho::findOne([
                'produto_id' => $linhaCarrinho->produto_id,
                'tamanho_id' => $linhaCarrinho->tamanho_id,
            ]);

            if ($produtostamanho) {
                $produtostamanho->quantidade += $linhaCarrinho->quantidade;

                //atualiza a quantidade total na tabela Produtos
                $produto = Produto::findOne($linhaCarrinho->produto_id);
                $produto->quantidade += $linhaCarrinho->quantidade;

                $produtostamanho->save();
                $produto->save();
            } else {
                Yii::$app->response->statusCode = 400;
                return ['message' => 'Tamanho não encontrado no stock para este produto.'];
            }
        } else {
            //produtos sem tamanho associado
            $produto = Produto::findOne($linhaCarrinho->produto_id);

            if ($produto) {
                $produto->quantidade += $linhaCarrinho->quantidade;

                if (!$produto->save()) {
                    Yii::$app->response->statusCode = 400;
                    return ['message' => 'Erro ao atualizar o stock do produto.'];
                }
            } else {
                Yii::$app->response->statusCode = 400;
                return ['message' => 'Produto não encontrado no stock.'];
            }
        }

        if ($linhaCarrinho->delete()) {
            $this->updateCarrinhoTotal($carrinho->id);

            return [
                'status' => 'success',
                'message' => 'Produto removido com sucesso!',
            ];
        } else {
            Yii::$app->response->statusCode = 500;
            return ['message' => 'Erro ao remover o produto do carrinho.'];
        }
    }

    //-------------------------------------------------SIS------------------------------------------------------------------------------

    public function actionAdicionarcarrinho($produto_id, $tamanho_id)
    {

        // Verificar se o produto existe
        $produto = Produto::findOne($produto_id);
        if (!$produto) {
            Yii::$app->response->statusCode = 400;
            return ['message' => 'Produto não encontrado.'];
        }

        // Verifica se o tamanho existe
        $tamanho = \common\models\Tamanho::findOne($tamanho_id);
        if (!$tamanho) {
            Yii::$app->response->statusCode = 400;
            return ['message' => 'Tamanho não encontrado.'];
        }

        // vai buscar a quantidade do produto no tamanho selecionado na tabela ProdutosHasTamanho
        $produtostamanho = \common\models\ProdutosHasTamanho::findOne([
            'produto_id' => $produto_id,
            'tamanho_id' => $tamanho_id,
        ]);

        if (!$produtostamanho) {
            Yii::$app->response->statusCode = 400;
            return ['message' => 'Stock do produto para o tamanho selecionado não encontrado.'];
        }

        // Vai obter o user autenticado
        $userId = Yii::$app->params['id'];

        // Vai obter o perfil associado ao user
        $profile = Profile::findOne(['user_id' => $userId]);
        if (!$profile) {
            Yii::$app->response->statusCode = 400;
            return ['message' => 'Perfil não encontrado.'];
        }

        // Vai obter o carrinho do perfil
        $carrinho = Carrinhocompra::findOne(['profile_id' => $profile->id]);
        if (!$carrinho) {
            Yii::$app->response->statusCode = 400;
            return ['message' => 'Carrinho não encontrado.'];
        }

        // Verifica se o produto já está no carrinho com o tamanho especificado
        $linhaCarrinho = Linhacarrinho::findOne([
            'carrinhocompras_id' => $carrinho->id,
            'produto_id' => $produto_id,
            'tamanho_id' => $tamanho_id,
        ]);

        // Capturar os parâmetros da requisição
        $request = Yii::$app->request;
        $quantidade = $request->getBodyParam('quantidade');
        //$tamanhoId = $request->getBodyParam('tamanho_id');

        if ($linhaCarrinho) {
            // Produto já está no carrinho
            Yii::$app->response->statusCode = 400;
            return ['message' => 'O produto já está adicionado ao carrinho.',];
        }

        // Verifica se há estoque suficiente antes de adicionar ao carrinho
        if ($produtostamanho->quantidade < $quantidade) {
            Yii::$app->response->statusCode = 400;
            return ['message' => 'Quantidade insuficiente para o tamanho selecionado.'];
        }

        // Atualiza a quantidade do produto no tamanho selecionado
        $produtostamanho->quantidade -= $quantidade;
        if (!$produtostamanho->save()) {
            Yii::$app->response->statusCode = 400;
            return ['message' => 'Erro ao atualizar o estoque do produto no tamanho selecionado.'];
        }

        // Adiciona o produto como nova linha no carrinho
        $linhaCarrinho = new Linhacarrinho();
        $linhaCarrinho->carrinhocompras_id = $carrinho->id;
        $linhaCarrinho->produto_id = $produto_id;
        $linhaCarrinho->tamanho_id = $tamanho_id;
        $linhaCarrinho->quantidade = $quantidade;
        $linhaCarrinho->precoUnit = $produto->preco;

        // Calcula os valores com IVA
        $percentualIva = $produto->iva->percentagem * 100;
        $subtotalSemIva = $linhaCarrinho->precoUnit * $linhaCarrinho->quantidade;
        $valorIvaAplicado = $subtotalSemIva * ($percentualIva / 100);
        $subtotalComIva = $subtotalSemIva + $valorIvaAplicado;

        $linhaCarrinho->valorIva = round($valorIvaAplicado, 2);
        $linhaCarrinho->valorComIva = round($subtotalComIva, 2);
        $linhaCarrinho->subtotal = round($subtotalComIva, 2);

        if (!$linhaCarrinho->save()) {
            Yii::$app->response->statusCode = 400;
            return ['message' => 'Erro ao adicionar o produto ao carrinho.'];
        }

        // Atualiza o total do carrinho
        $carrinho->quantidade += $quantidade;
        $carrinho->valorTotal += $linhaCarrinho->subtotal;

        if (!$carrinho->save()) {
            Yii::$app->response->statusCode = 400;
            return ['message' => 'Erro ao atualizar o carrinho.'];
        }

        // Retorna a resposta de sucesso
        return [
            'status' => 'success',
            'message' => 'Produto adicionado ao carrinho com sucesso.',
        ];
    }

    public function actionApagarlinhacarrinho($id)
    {
        // Verifica se a linha do carrinho existe
        $linhaCarrinho = Linhacarrinho::findOne($id);
        if (!$linhaCarrinho) {
            Yii::$app->response->statusCode = 400;
            return ['message' => 'Linha do carrinho não encontrada.'];
        }

        // Vai obter o carrinho relacionado à linha do carrinho
        $carrinho = Carrinhocompra::findOne($linhaCarrinho->carrinhocompras_id);
        if (!$carrinho) {
            return ['message' => 'Carrinho não encontrado.'];
        }

        // Atualiza a quantidade do tamanho do produto correspondente
        $produtostamanho = \common\models\ProdutosHasTamanho::findOne([
            'produto_id' => $linhaCarrinho->produto_id,
            'tamanho_id' => $linhaCarrinho->tamanho_id,
        ]);

        if ($produtostamanho) {
            $produtostamanho->quantidade += $linhaCarrinho->quantidade;
            if (!$produtostamanho->save()) {
                return ['message' => 'Erro ao atualizar o estoque do produto.'];
            }
        }

        // Atualiza o total do carrinho antes de remover a linha
        $carrinho->quantidade -= $linhaCarrinho->quantidade;
        $carrinho->valorTotal -= $linhaCarrinho->subtotal;

        if (!$carrinho->save()) {
            Yii::$app->response->statusCode = 400;
            return ['message' => 'Erro ao atualizar os totais do carrinho.'];
        }

        if (!$linhaCarrinho->delete()) {
            Yii::$app->response->statusCode = 400;
            return ['message' => 'Erro ao apagar a linha do carrinho.'];
        }

        return [
            'status' => 'success',
            'message' => 'Linha do carrinho apagada com sucesso.',
        ];
    }

    public function actionDiminuir($id)
    {
        $linhaCarrinho = Linhacarrinho::findOne($id);

        if (!$linhaCarrinho) {
            return ['message' => 'Produto não encontrado no carrinho.'];
        }

        $produtostamanho = ProdutosHasTamanho::findOne([
            'produto_id' => $linhaCarrinho->produto_id,
            'tamanho_id' => $linhaCarrinho->tamanho_id,
        ]);

        if (!$produtostamanho) {
            Yii::$app->response->statusCode = 400;
            return ['message' => 'Tamanho não encontrado no estoque para este produto.'];
        }

        if ($linhaCarrinho->quantidade > 1) {
            $linhaCarrinho->quantidade -= 1;

            $subtotalSemIva = $linhaCarrinho->precoUnit * $linhaCarrinho->quantidade;
            $percentualIva = $linhaCarrinho->produto->iva->percentagem;
            $valorIvaAplicado = $subtotalSemIva * $percentualIva;
            $subtotalComIva = $subtotalSemIva + $valorIvaAplicado;

            $linhaCarrinho->subtotal = round($subtotalComIva, 2);
            $linhaCarrinho->valorComIva = round($subtotalComIva, 2);

            if ($linhaCarrinho->save()) {
                $produtostamanho->quantidade += 1;
                $produtostamanho->save();

                $this->updateCarrinhoTotal($linhaCarrinho->carrinhocompras_id);

                return [
                    'status' => 'success',
                    'message' => 'Quantidade diminuída com sucesso.',
                ];
            } else {
                return ['message' => 'Erro ao atualizar a linha do carrinho.'];
            }
        } else {
            return ['message' => 'A quantidade não pode ser menor que 1. Use a rota de remoção para excluir o produto.'];
        }
    }


    public function actionAumentar($id)
    {
        $linhaCarrinho = Linhacarrinho::findOne($id);

        if (!$linhaCarrinho) {
            return ['message' => 'Produto não encontrado no carrinho.'];
        }

        $produtostamanho = ProdutosHasTamanho::findOne([
            'produto_id' => $linhaCarrinho->produto_id,
            'tamanho_id' => $linhaCarrinho->tamanho_id,
        ]);

        if (!$produtostamanho) {
            Yii::$app->response->statusCode = 400;
            return ['message' => 'Tamanho não encontrado no estoque para este produto.'];
        }

        if ($produtostamanho->quantidade > 0) {
            $linhaCarrinho->quantidade += 1;

            $subtotalSemIva = $linhaCarrinho->precoUnit * $linhaCarrinho->quantidade;
            $percentualIva = $linhaCarrinho->produto->iva->percentagem;
            $valorIvaAplicado = $subtotalSemIva * $percentualIva;
            $subtotalComIva = $subtotalSemIva + $valorIvaAplicado;

            $linhaCarrinho->subtotal = round($subtotalComIva, 2);
            $linhaCarrinho->valorComIva = round($subtotalComIva, 2);

            if ($linhaCarrinho->save()) {
                $produtostamanho->quantidade -= 1;
                $produtostamanho->save();

                $this->updateCarrinhoTotal($linhaCarrinho->carrinhocompras_id);

                return [
                    'status' => 'success',
                    'message' => 'Quantidade aumentada com sucesso.',
                ];
            } else {
                return ['message' => 'Erro ao atualizar a linha do carrinho.'];
            }
        } else {
            return ['message' => 'Stock insuficiente para aumentar a quantidade.'];
        }
    }


    public function updateCarrinhoTotal($carrinhocompras_id)
    {
        $carrinho = Carrinhocompra::findOne($carrinhocompras_id);

        if ($carrinho) {
            $carrinho->quantidade = 0;
            $carrinho->valorTotal = 0;

            foreach ($carrinho->linhascarrinhos as $linha) {
                $carrinho->quantidade += $linha->quantidade;
                $carrinho->valorTotal += $linha->subtotal;
            }

            if (!$carrinho->save()) {
                return ['message' => 'Erro ao atualizar o total do carrinho.'];
            }
        }
    }

}
