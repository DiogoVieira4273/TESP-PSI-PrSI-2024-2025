<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var array $favoritos */

$this->title = 'Favoritos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product_section layout_padding">

    <div class="container">
        <h1><?= Html::encode($this->title) ?></h1>

        <?php if (empty($favoritos)): ?>
            <p>Você ainda não possui produtos favoritados.</p>
        <?php else: ?>
            <div class="product_container">
                <div class="row">
                    <?php foreach ($favoritos as $favorito): ?>
                        <div class="col-sm-6 col-md-4">
                            <div class="box">
                                <a href="#" class="p_cart">
                                    <i class="fa fa-cart-plus" aria-hidden="true"></i>
                                </a>
                                <div class="img-box">
                                    <?php if (!empty($favorito->produto->imagens) && isset($favorito->produto->imagens[0])): ?>
                                        <img src="<?= htmlspecialchars("./../../backend/web/uploads/" . $favorito->produto->imagens[0]->filename) ?>"
                                             alt="Imagem do Produto">
                                    <?php endif; ?>
                                </div>
                                <div class="detail-box">
                                    <a href="<?= Url::to(['produto/detalhes', 'id' => $favorito->produto->id]) ?>"
                                       class="p_name">
                                        <?= Html::encode($favorito->produto->nomeProduto) ?>
                                    </a>
                                    <h6 class="p_price">
                                    <span class="new_price">
                                        <?= Html::encode(number_format($favorito->produto->preco, 2, ',', '.')) ?>€
                                    </span>
                                    </h6>
                                </div>
                                <div class="actions">
                                    <?= Html::a('Remover', ['favorito/delete', 'produto_id' => $favorito->produto->id], [
                                        'class' => 'btn btn-danger',
                                        'data' => [
                                            'confirm' => 'Tem certeza que deseja remover este produto dos favoritos?',
                                            'method' => 'post',
                                        ],
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
