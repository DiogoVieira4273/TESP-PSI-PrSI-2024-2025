<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Marca;
use common\models\Categoria;
use common\models\Iva;
use common\models\Genero;
use common\models\Tamanho;

/** @var yii\web\View $this */
/** @var common\models\Produto $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="produto-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'nomeProduto')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'preco')->textInput() ?>

    <?= $form->field($model, 'quantidade')->textInput() ?>

    <?= $form->field($model, 'descricaoProduto')->textarea(['rows' => 6]) ?>


    <!-- Campo de seleção para Marca -->
    <?= $form->field($model, 'marca_id')->dropdownList(
        (function() {
            $marcas = Marca::find()->all();
            $marcaOptions = ['' => 'Selecione uma Marca'];
            foreach ($marcas as $marca) {
                $marcaOptions[$marca->id] = $marca->nomeMarca;
            }
            return $marcaOptions;
        })()
    ) ?>

    <!-- Campo de seleção para Categoria -->
    <?= $form->field($model, 'categoria_id')->dropdownList(
        (function() {
            $categorias = Categoria::find()->all();
            $categoriaOptions = ['' => 'Selecione uma Categoria'];
            foreach ($categorias as $categoria) {
                $categoriaOptions[$categoria->id] = $categoria->nomeCategoria;
            }
            return $categoriaOptions;
        })()
    ) ?>

    <!-- Campo de seleção para IVA -->
    <?= $form->field($model, 'iva_id')->dropdownList(
        (function() {
            $ivas = Iva::find()->all();
            $ivaOptions = ['' => 'Selecione um IVA'];
            foreach ($ivas as $iva) {
                $ivaOptions[$iva->id] = $iva->percentagem . '%';
            }
            return $ivaOptions;
        })()
    ) ?>

    <!-- Campo de seleção para Gênero -->
    <?= $form->field($model, 'genero_id')->dropdownList(
        (function() {
            $generos = Genero::find()->all();
            $generoOptions = ['' => 'Selecione um Gênero'];
            foreach ($generos as $genero) {
                $generoOptions[$genero->id] = $genero->referencia;
            }
            return $generoOptions;
        })()
    ) ?>

    <!-- Campo de seleção para Tamanho -->
    <?= $form->field($model, 'tamanho_id')->dropdownList(
        (function() {
            $tamanhos = Tamanho::find()->all();
            $tamanhoOptions = ['' => 'Selecione um Tamanho'];
            foreach ($tamanhos as $tamanho) {
                $tamanhoOptions[$tamanho->id] = $tamanho->referencia;
            }
            return $tamanhoOptions;
        })()
    ) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
