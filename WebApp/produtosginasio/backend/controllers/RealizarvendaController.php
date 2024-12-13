<?php

namespace backend\controllers;

use common\models\Produto;
use common\models\ProdutosHasTamanho;
use common\models\Tamanho;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

class RealizarvendaController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::class,
                    'only' => ['index', 'adicionarproduto'],
                    'rules' => [
                        [
                            'actions' => ['index', 'adicionarproduto'],
                            'allow' => true,
                            'roles' => ['admin', 'funcionario'],
                        ],
                    ],
                ],

                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        //'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    public function actionIndex()
    {
        $session = Yii::$app->session;
        $carrinho = $session->get('carrinho', []);
        $produtos = Produto::find()->all();

        //$produtos = Produto::find()->with('produtosHasTamanhos.tamanho')->all();

        //para cada produto, obter o relacionamento de tamanhos
        /*foreach ($produtos as $produto) {
            // Obtemos os tamanhos e as quantidades associadas ao produto
            $produto = $produto->getProdutosHasTamanhos()->all();
        }*/

        return $this->render('index', [
            'produtos' => $produtos,
            'carrinho' => $carrinho,
        ]);
    }

    /*public function actionObterQuantidadeMaxima()
    {
        $tamanhoReferencia = Yii::$app->request->get('tamanho_referencia');
        $produtoId = Yii::$app->request->get('produto_id');

        // Busque o produto pelo ID
        $produto = Produto::findOne($produtoId);
        $quantidadeMax = 0;

        // Encontre a quantidade máxima para o tamanho selecionado
        foreach ($produto->produtosHasTamanhos as $produtosHasTamanho) {
            if ($produtosHasTamanho->tamanho->referencia === $tamanhoReferencia) {
                $quantidadeMax = $produtosHasTamanho->quantidade;
                break;
            }
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return ['quantidadeMax' => $quantidadeMax];
    }*/


    public function actionAdicionarproduto()
    {
        //verifica se tem uma sessão ativa
        if (Yii::$app->session->isActive == false) {
            //se não tiver uma sessão ativa, é criada uma nova
            Yii::$app->session->open();
        }

        //verifica se o produto_id foi enviado e se é válido
        if (!isset($_GET['produto_id']) || !is_numeric($_GET['produto_id'])) {
            return 'Produto inválido.';
        }

        $produto_id = $_GET['produto_id'];
        $tamanhoReferencia = isset($_GET['tamanho_referencia']) ? $_GET['tamanho_referencia'] : null;
        $quantidadeSelecionada = isset($_GET['quantidade']) ? intval($_GET['quantidade']) : 1; // Quantidade selecionada na combobox

        $produto = Produto::findOne($produto_id);

        //obter o carrinho
        $carrinho = Yii::$app->session->get('carrinho', []);

        //verifica se o produto já está no carrinho
        $produtoEncontrado = false;

        foreach ($carrinho as $key => $produtoCarrinho) {
            if ($produtoCarrinho['id'] == $produto_id && $produtoCarrinho['tamanho'] == $tamanhoReferencia) {
                //atualiza a quantidade e o preço total
                $carrinho[$key]['quantidade'] += $quantidadeSelecionada;
                $carrinho[$key]['preco'] += $produto->preco * $quantidadeSelecionada;
                $produtoEncontrado = true;
                break;
            }
        }

        //se o produto não foi encontrado no carrinho, adiciona
        if (!$produtoEncontrado) {
            //cria o item para adicionar ao carrinho
            $item = [
                'id' => $produto_id,
                'nomeProduto' => $produto->nomeProduto,
                //preço total inicial baseado na quantidade selecionada
                'preco' => $produto->preco * $quantidadeSelecionada,
                'quantidade' => $quantidadeSelecionada,
                'tamanho' => $tamanhoReferencia
            ];
            $carrinho[] = $item;
        }

        //atualiza o carrinho na sessão
        Yii::$app->session->set('carrinho', $carrinho);

        Yii::$app->session->setFlash('success', 'Produto adicionado ao carrinho com sucesso!');
        return $this->redirect(['realizarvenda/index']);
    }

    public function actionEditarquantidade()
    {
        if (Yii::$app->session->isActive == false) {
            Yii::$app->session->open();
        }

        if (!isset($_POST['id']) || !isset($_POST['quantidade']) || !is_numeric($_POST['quantidade'])) {
            Yii::$app->session->setFlash('error', 'Parâmetros inválidos.');
            return $this->redirect(['realizarvenda/index']);
        }

        $produto_id = $_POST['id'];
        $tamanho_referencia = isset($_POST['tamanho']) ? $_POST['tamanho'] : null;
        $quantidadeNova = intval($_POST['quantidade']);

        $carrinho = Yii::$app->session->get('carrinho', []);

        foreach ($carrinho as $key => $produtoCarrinho) {
            if ($produtoCarrinho['id'] == $produto_id && $produtoCarrinho['tamanho'] == $tamanho_referencia) {
                $produto = Produto::findOne($produto_id);

                if ($produto === null) {
                    Yii::$app->session->setFlash('error', 'Produto não encontrado.');
                    return $this->redirect(['realizarvenda/index']);
                }

                //converte a string da referência do tamanho para o tamanho_id
                $tamanho_id = null;
                if ($tamanho_referencia !== null) {
                    $tamanho = Tamanho::find()->where(['referencia' => $tamanho_referencia])->one();
                    if ($tamanho !== null) {
                        $tamanho_id = $tamanho->id;
                    } else {
                        Yii::$app->session->setFlash('error', 'Tamanho não encontrado.');
                        return $this->redirect(['realizarvenda/index']);
                    }
                }

                if ($quantidadeNova <= $produtoCarrinho['quantidade']) {
                    //repõe a quantidade diminuída no stock
                    if ($tamanho_id !== null) {
                        $produtoTamanho = ProdutosHasTamanho::find()->where(['produto_id' => $produto_id, 'tamanho_id' => $tamanho_id])->one();
                        if ($produtoTamanho) {
                            $produtoTamanho->quantidade += ($produtoCarrinho['quantidade'] - $quantidadeNova);
                            $produtoTamanho->save();
                        }
                    } else {
                        $produto->quantidade += ($produtoCarrinho['quantidade'] - $quantidadeNova);
                        $produto->save();
                    }

                    $carrinho[$key]['quantidade'] = $quantidadeNova;
                    $carrinho[$key]['preco'] = $produto->preco * $quantidadeNova;
                } else {
                    //reduz a quantidade aumentada no stock
                    if ($tamanho_id !== null) {
                        $produtoTamanho = ProdutosHasTamanho::find()->where(['produto_id' => $produto_id, 'tamanho_id' => $tamanho_id])->one();
                        if ($produtoTamanho) {
                            $quantidadeDisponivel = $produtoTamanho->quantidade;
                            $quantidadeAtualNoCarrinho = $produtoCarrinho['quantidade'];
                            $quantidadeMaximaDisponivel = $quantidadeDisponivel + $quantidadeAtualNoCarrinho;

                            if ($quantidadeNova <= $quantidadeMaximaDisponivel) {
                                $produtoTamanho->quantidade = $quantidadeDisponivel - ($quantidadeNova - $quantidadeAtualNoCarrinho);
                                $produtoTamanho->save();

                                $carrinho[$key]['quantidade'] = $quantidadeNova;
                                $carrinho[$key]['preco'] = $produto->preco * $quantidadeNova;
                            } else {
                                Yii::$app->session->setFlash('error', 'Quantidade indisponível em stock.');
                                return $this->redirect(['realizarvenda/index']);
                            }
                        } else {
                            Yii::$app->session->setFlash('error', 'Produto com tamanho não encontrado.');
                            return $this->redirect(['realizarvenda/index']);
                        }
                    } else {
                        $quantidadeDisponivel = $produto->quantidade;
                        $quantidadeAtualNoCarrinho = $produtoCarrinho['quantidade'];
                        $quantidadeMaximaDisponivel = $quantidadeDisponivel + $quantidadeAtualNoCarrinho;

                        if ($quantidadeNova <= $quantidadeMaximaDisponivel) {
                            $produto->quantidade = $quantidadeDisponivel - ($quantidadeNova - $quantidadeAtualNoCarrinho);
                            $produto->save();

                            $carrinho[$key]['quantidade'] = $quantidadeNova;
                            $carrinho[$key]['preco'] = $produto->preco * $quantidadeNova;
                        } else {
                            Yii::$app->session->setFlash('error', 'Quantidade indisponível em estoque.');
                            return $this->redirect(['realizarvenda/index']);
                        }
                    }
                }
                break;
            }
        }

        Yii::$app->session->set('carrinho', $carrinho);
        Yii::$app->session->setFlash('success', 'Quantidade atualizada com sucesso!');
        return $this->redirect(['realizarvenda/index']);
    }


    public function actionRemoverproduto($id)
    {
        // Verifica se tem uma sessão ativa
        if (Yii::$app->session->isActive == false) {
            // Se não tiver uma sessão ativa, é criada uma nova
            Yii::$app->session->open();
        }

        // Obter o carrinho
        $carrinho = Yii::$app->session->get('carrinho', []);

        // Percorrer o carrinho para encontrar e remover o produto
        foreach ($carrinho as $key => $produtoCarrinho) {
            if ($produtoCarrinho['id'] == $id) {
                // Remove o produto do carrinho
                unset($carrinho[$key]);
                break;
            }
        }

        // Atualiza o carrinho na sessão
        Yii::$app->session->set('carrinho', array_values($carrinho)); // Reindexa o array

        Yii::$app->session->setFlash('success', 'Produto removido do carrinho com sucesso!');
        return $this->redirect(['realizarvenda/index']);
    }
}
