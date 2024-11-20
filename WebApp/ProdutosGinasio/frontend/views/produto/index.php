<?php

use common\models\Produto;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\ProdutoSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Produtos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product_section layout_padding">
    <div class="container">
        <div class="container">
            <h1><?= Html::encode($this->title) ?></h1>

            <!-- Verificação se existem produtos -->
            <?php if (empty($produtos)): ?>
                <p>Sem produtos disponíveis</p>
            <?php else: ?>
                <div class="product_container">
                    <div class="row">
                        <?php foreach ($produtos as $produto): ?>
                            <div class="col-sm-6 col-md-4">
                                <div class="box">
                                    <!-- Ícone de Adicionar ao Carrinho -->
                                    <a href="#" class="p_cart">
                                        <i class="fa fa-cart-plus" aria-hidden="true"></i>
                                    </a>
                                    <!-- Imagem do Produto -->
                                    <div class="img-box">
                                        <?php if (!empty($produto->imagens) && isset($produto->imagens[0])): ?>
                                            <img src="<?= htmlspecialchars("./../../backend/web/uploads/" . $produto->imagens[0]->filename) ?>"
                                                 alt="Imagem do Produto">
                                        <?php endif; ?>
                                    </div>
                                    <!-- Detalhes do Produto -->
                                    <div class="detail-box">
                                        <!-- Nome do Produto -->
                                        <a href="<?= Yii::$app->urlManager->createUrl(['produto/detalhes', 'id' => $produto->id]) ?>"
                                           class="p_name">
                                            <?= Html::encode($produto->nomeProduto) ?>
                                        </a>
                                        <!-- Preço do Produto -->
                                        <h6 class="p_price">
                                    <span class="new_price">
                                        <?= number_format($produto->preco, 2, ',', '.') ?>€
                                    </span>
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
