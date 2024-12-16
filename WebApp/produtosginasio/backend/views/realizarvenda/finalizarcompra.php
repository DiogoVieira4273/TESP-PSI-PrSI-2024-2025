<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

$this->title = 'Finalizar Compra';
$this->params['breadcrumbs'][] = $this->title;
?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<div class="finalizar-compra container mt-4">
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
                    <!-- Exibe o formulário de cupão -->
                    <?php $formCupao = ActiveForm::begin(['action' => ['compra'], 'method' => 'post']); ?>
                    <?= Html::textInput('cupao', '', ['placeholder' => 'Código do cupão', 'class' => 'form-control mb-2', 'id' => 'cupao']) ?>
                    <?= Html::dropDownList('cliente', null,
                        \yii\helpers\ArrayHelper::map($clientes, 'id', 'username'),
                        [
                            'prompt' => 'Selecione um cliente',
                            'class' => 'form-control',
                        ]
                    ) ?>
                    <button type="submit" class="btn btn-primary">Aplicar</button>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>

            <!-- Secção do Método de Entrega -->
            <div class="card shadow-sm mb-4">
                <div class="card-header" style="background-color: #007bff; color: white;">
                    <h4 class="mb-0">Escolha o Método de Entrega</h4>
                </div>
                <div class="card-body">
                    <?php $formEntrega = ActiveForm::begin(['action' => ['compra'], 'method' => 'post']); ?>
                    <?= Html::hiddenInput('cupao', $cupao ? $cupao->codigo : null) ?>
                    <?= Html::dropDownList(
                        'metodo_entrega',
                        Yii::$app->request->post('metodo_entrega') ?? null,
                        \yii\helpers\ArrayHelper::map($metodosEntrega, 'id', 'descricao'),
                        [
                            'prompt' => 'Selecione um método de entrega',
                            'class' => 'form-control',
                            'id' => 'metodo_entrega',
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
                                <input type="text" class="form-control" id="numero_cartao"
                                       placeholder="Digite o número do cartão" required>

                            </div>
                            <div class="form-group">
                                <label for="data_validade">Data de Validade:</label>
                                <input type="month" class="form-control" id="data_validade" required>
                            </div>
                            <div class="form-group">
                                <label for="codigo_seguranca">Código de Segurança (CVV):</label>
                                <input type="text" class="form-control" id="codigo_seguranca"
                                       placeholder="Digite o código CVV" required>
                            </div>
                        </div>
                        <div id="paypal-formulario" style="display:none;">
                            <div class="form-group">
                                <label for="email_paypal">E-mail do PayPal:</label>
                                <inpu type="email" class="form-control" id="email_paypal"
                                      placeholder="Digite seu e-mail do PayPal" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Secção de seleção cliente -->
            <div class="card shadow-sm mb-4">
                <div class="card-header" style="background-color: #007bff; color: white;">
                    <h4 class="mb-0">Selecione o Cliente</h4>
                </div>
                <div class="card-body">
                    <?= Html::dropDownList('cliente', null,
                        \yii\helpers\ArrayHelper::map($clientes, 'id', 'username'),
                        [
                            'prompt' => 'Selecione um cliente',
                            'class' => 'form-control',
                            'id' => 'cliente',
                        ]
                    ) ?>
                </div>
            </div>
        </div>

        <!-- Resumo do Carrinho -->
        <div class="col-lg-4">
            <div class="card shadow-sm position-fixed"
                 style="bottom: 80px; right: 20px; width: 320px; max-height: calc(100vh - 120px); overflow-y: auto;">
                <div class="card-header" style="background-color: #007bff; color: white;">
                    <h4 class="mb-0">Resumo da Compra</h4>
                </div>
                <div class="card-body">
                    <p><strong>Total de
                            Produtos:</strong> <?= Html::encode(number_format($valorProdutos, 2, ',', '.')) ?>€</p>
                    <p><strong>Desconto:</strong> <?= Html::encode(number_format($desconto, 2, ',', '.')) ?>%</p>
                    <p><strong>Custo de Envio:</strong> <?= Html::encode(number_format($custoEnvio, 2, ',', '.')) ?>€
                    </p>
                    <p><strong>Valor Poupado:</strong> <?= Html::encode(number_format($ValorPoupado, 2, ',', '.')) ?>€
                    </p>
                    <p class="h5"><strong>Total
                            Final:</strong> <?= Html::encode(number_format($valorFinal, 2, ',', '.')) ?>€</p>
                </div>
                <div class="card-footer">
                    <a href="javascript:void(0)" id="confirmar-compra" class="btn btn-success w-100">
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

<script>
    // Quando o botão "Confirmar Compra" for clicado
    $('#confirmar-compra').on('click', function (e) {
        e.preventDefault();  // Impede o comportamento padrão do link

        // Captura os dados dos campos da vista
        //var cupaoCodigo = $('#cupao').val();  // O ID dos inputs
        var metodoEntrega = $('#metodo_entrega').val();
        var metodoPagamento = $('#metodo-pagamento').val();
        var clienteId = $('#cliente').val();

        if (!metodoEntrega) {
            alert('Por favor, selecione um metodo de entrega!');
            return;
        }
        if (!metodoPagamento) {
            alert('Por favor, selecione um metodo de pagamento!');
            return;
        }
        if (!clienteId) {
            alert('Por favor, selecione um cliente!');
            return;
        }

        // Envia os dados via AJAX
        $.ajax({
            url: '<?= Url::to(['realizarvenda/fecharcompra']) ?>',  // A URL do controlador
            type: 'POST',
            data: {
                //cupao: cupaoCodigo,
                metodo_entrega: metodoEntrega,
                metodo_pagamento: metodoPagamento,
                cliente: clienteId,
            },
            success: function (response) {
                // Aqui você pode manipular a resposta do servidor (ex: exibir mensagem de sucesso)
                alert('Compra confirmada com sucesso!');
                window.location.href = 'index';
            },
            error: function () {
                alert('Erro ao processar a compra!');
            }
        });
    });
</script>