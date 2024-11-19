<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Produto $model */

$this->title = $model->nomeProduto;
\yii\web\YiiAsset::register($this);
?>
<div class="container">

    <h1><?= Html::encode($this->title) ?></h1>


    <div class="container-produto">
        <div class="produto-imagens">
        </div>
        <div class="produto-atributos">
            <p><label>Preço: <?= Html::encode($model->preco) ?> €</label></p>
            <p><label>Marca: <?= Html::encode($model->marca->nomeMarca) ?></label></p>
            <p><label>Categoria: <?= Html::encode($model->categoria->nomeCategoria) ?></label></p>
            <p><label>Iva: <?= Html::encode($model->iva->percentagem) ?></label></p>
            <p><label>Género: <?= Html::encode($model->genero->referencia) ?></label></p>
            <p><label>Tamanho: <?= Html::encode($model->tamanho->referencia) ?></label></p>
        </div>
    </div>
    <hr>
    <div class="produto-descricao">
        <h3>Descrição Produto</h3>
        <p><?= Html::encode($model->descricaoProduto) ?></p>
    </div>

</div>
