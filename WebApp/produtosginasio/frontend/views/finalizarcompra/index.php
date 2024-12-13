<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

$this->title = 'Finalizar Compra';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="finalizar-compra container mt-4">
    <h1><?= Html::encode($this->title) ?></h1>
    <!-- Exibir flash message de sucesso -->
    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success">
            <?= Yii::$app->session->getFlash('success') ?>
        </div>
    <?php elseif (Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger">
            <?= Yii::$app->session->getFlash('error') ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-8">

            <!-- Secção do Cupão -->
            <div class="card shadow-sm mb-4">
                <div class="card-header" style="background-color: #007bff; color: white;">
                    <h4 class="mb-0">Aplicar Cupão</h4>
                </div>
                <div class="card-body">
                    <?php if ($cupao && strtotime($cupao->dataFim) >= time()): ?>
                        <!-- Exibe o cupão válido -->
                        <p style="color: #28a745; font-weight: bold;">
                            <strong>Cupão Aplicado:</strong> <?= Html::encode($cupao->codigo) ?>
                        </p>
                    <?php else: ?>
                        <!-- Exibe o formulário de cupão -->
                        <?php $formCupao = ActiveForm::begin(['action' => ['finalizarcompra/index', 'carrinho_id' => $carrinho->id], 'method' => 'post']); ?>
                        <?= Html::textInput('cupao', '', ['placeholder' => 'Código do cupão', 'class' => 'form-control mb-2']) ?>
                        <button type="submit" class="btn btn-primary">Aplicar</button>
                        <?php ActiveForm::end(); ?>

                        <!-- Mensagem de erro, caso o cupão tenha expirado -->
                        <?php if ($cupao && strtotime($cupao->dataFim) < time()): ?>
                            <div class="alert alert-danger mt-2" role="alert">
                                <strong>Erro!</strong> Este cupão está expirado.
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Secção do Método de Entrega -->
            <div class="card shadow-sm mb-4">
                <div class="card-header" style="background-color: #007bff; color: white;">
                    <h4 class="mb-0">Escolha o Método de Entrega</h4>
                </div>
                <div class="card-body">
                    <?php $formEntrega = ActiveForm::begin(['action' => ['finalizarcompra/index', 'carrinho_id' => $carrinho->id], 'method' => 'post']); ?>
                    <?= Html::hiddenInput('cupao', $cupao ? $cupao->codigo : null) ?>
                    <?= Html::dropDownList(
                        'metodo_entrega',
                        Yii::$app->request->post('metodo_entrega') ?? null,
                        \yii\helpers\ArrayHelper::map($metodosEntrega, 'id', 'descricao'),
                        [
                            'prompt' => 'Selecione um método de entrega',
                            'class' => 'form-control',
                            'onchange' => 'this.form.submit()',
                        ]
                    ) ?>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>

            <!-- Secção do Método de Pagamento -->
            <div class="card shadow-sm mb-4">
                <div class="card-header" style="background-color: #007bff; color: white;">
                    <h4 class="mb-0">Escolha o Método de Pagamento</h4>
                </div>
                <div class="card-body">
                    <?= Html::dropDownList('metodo_pagamento', null,
                        \yii\helpers\ArrayHelper::map($metodosPagamento, 'id', 'metodoPagamento'),
                        [
                            'prompt' => 'Selecione um método de pagamento',
                            'class' => 'form-control',
                            'id' => 'metodo-pagamento',  // Atribuindo um id para controle via JS
                            'onchange' => 'mostrarFormularioPagamento()'  // Chamando a função JS ao mudar
                        ]
                    ) ?>

                    <!-- Formulário para dados do cartão ou outros dados de pagamento -->
                    <div id="dados-pagamento" style="display:none; margin-top: 20px;">
                        <div id="cartao-formulario" style="display:none;">
                            <div class="form-group">
                                <label for="numero_cartao">Número do Cartão:</label>
                                <input type="text" class="form-control" id="numero_cartao" placeholder="Digite o número do cartão">
                            </div>
                            <div class="form-group">
                                <label for="data_validade">Data de Validade:</label>
                                <input type="month" class="form-control" id="data_validade">
                            </div>
                            <div class="form-group">
                                <label for="codigo_seguranca">Código de Segurança (CVV):</label>
                                <input type="text" class="form-control" id="codigo_seguranca" placeholder="Digite o código CVV">
                            </div>
                        </div>
                        <div id="paypal-formulario" style="display:none;">
                            <div class="form-group">
                                <label for="email_paypal">E-mail do PayPal:</label>
                                <input type="email" class="form-control" id="email_paypal" placeholder="Digite seu e-mail do PayPal">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumo do Carrinho -->
        <div class="col-lg-4">
            <div class="card shadow-sm position-fixed"
                 style="bottom: 80px; right: 20px; width: 320px; max-height: calc(100vh - 120px); overflow-y: auto;">
                <div class="card-header" style="background-color: #007bff; color: white;">
                    <h4 class="mb-0">Resumo do Carrinho</h4>
                </div>
                <div class="card-body">
                    <p><strong>Total de Produtos:</strong> <?= Html::encode(number_format($valorProdutos, 2, ',', '.')) ?>€</p>
                    <p><strong>Desconto:</strong> <?= Html::encode(number_format($desconto, 2, ',', '.')) ?>%</p>
                    <p><strong>Custo de Envio:</strong> <?= Html::encode(number_format($custoEnvio, 2, ',', '.')) ?>€</p>
                    <p><strong>Valor Poupado:</strong> <?= Html::encode(number_format($ValorPoupado, 2, ',', '.')) ?>€</p>
                    <p class="h5"><strong>Total Final:</strong> <?= Html::encode(number_format($valorFinal, 2, ',', '.')) ?>€</p>
                </div>
                <div class="card-footer">
                    <a href="<?= Url::to(['pedido/criar', 'carrinho_id' => $carrinho->id]) ?>" class="btn btn-success w-100">
                        Confirmar Compra
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Função JavaScript que mostra ou esconde o formulário de pagamento
    function mostrarFormularioPagamento() {
        var metodoPagamento = document.getElementById('metodo-pagamento').value;
        var formularioPagamento = document.getElementById('dados-pagamento');
        var cartaoFormulario = document.getElementById('cartao-formulario');
        var paypalFormulario = document.getElementById('paypal-formulario');

        // Esconde todos os formulários inicialmente
        cartaoFormulario.style.display = 'none';
        paypalFormulario.style.display = 'none';

        // Exibe o formulário de pagamento apenas se um método de pagamento específico for selecionado
        if (metodoPagamento) {
            formularioPagamento.style.display = 'block';  // Exibe a secção de dados de pagamento

            // Exibir o formulário correto com base no método de pagamento selecionado
            if (metodoPagamento === '1') {
                paypalFormulario.style.display = 'block';  // Exibe formulário para PayPal
            } else if (metodoPagamento === '2') {
                cartaoFormulario.style.display = 'block';  // Exibe formulário para Cartão de Crédito
            }
        } else {
            formularioPagamento.style.display = 'none';  // Esconde a secção de dados de pagamento
        }
    }
</script>








