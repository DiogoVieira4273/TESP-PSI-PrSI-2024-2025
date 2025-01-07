<?php

/** @var yii\web\View $this */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Produtos de Ginásio';
?>
<div class="slider_section">
    <?php
    if ($cupoes) {
        $first = true;
        ?>
        <div id="customCarousel1" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
                <?php foreach ($cupoes as $cupao): ?>
                    <div class="carousel-item <?= $first ? 'active' : '' ?>">
                        <!-- Marca o primeiro cupão como 'active' -->
                        <div class="container">
                            <div class="row">
                                <div class="col-md-8 mx-auto">
                                    <div class="detail_box">
                                        <h1>
                                            Aproveite <br/>
                                            Desconto - <?= $cupao->desconto ?>%
                                        </h1>
                                        <p>
                                            Código de desconto para aplicar numa compra:
                                            <b><?= $cupao->codigo ?></b>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php $first = false; ?>
                <?php endforeach; ?>
            </div>

            <div class="carousel_btn-box">
                <a class="carousel-control-prev" href="#customCarousel1" role="button" data-slide="prev">
                    <i class="fa fa-angle-left" aria-hidden="true"></i>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#customCarousel1" role="button" data-slide="next">
                    <i class="fa fa-angle-right" aria-hidden="true"></i>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </div>
    <?php } ?>
</div>
<!-- end slider section -->

<!-- product section -->

<div class="product_section layout_padding">
    <div class="container">
        <div class="heading_container heading_center">
            <h2>
                Novos Produtos
            </h2>
        </div>
        <div class="product_container">
            <div class="row">
                <?php foreach ($produtosRecentes as $produtoRecente): ?>
                    <div class="col-sm-6 col-md-4 ">
                        <div class="box">
                            <a href="" class="p_cart">
                                <i class="fa fa-cart-plus" aria-hidden="true"></i>
                            </a>
                            <div class="img-box">
                                <?php if (!empty($produtoRecente->imagens) && isset($produtoRecente->imagens[0])): ?>
                                    <img src="<?= htmlspecialchars("./../../backend/web/uploads/" . $produtoRecente->imagens[0]->filename) ?>">
                                <?php endif; ?>
                            </div>
                            <div class="detail-box">
                <span class="p_rating">
                  <a href="">
                    <i class="fa fa-star" aria-hidden="true"></i>
                  </a>
                  <a href="">
                    <i class="fa fa-star" aria-hidden="true"></i>
                  </a>
                  <a href="">
                    <i class="fa fa-star" aria-hidden="true"></i>
                  </a>
                  <a href="">
                    <i class="fa fa-star" aria-hidden="true"></i>
                  </a>
                  <a href="">
                    <i class="fa fa-star" aria-hidden="true"></i>
                  </a>
                </span>
                                <a href="<?= Url::to(['produto/detalhes', 'id' => $produtoRecente->id]) ?>"
                                   class="p_name">
                                    <?= Html::Encode($produtoRecente->nomeProduto) ?>
                                </a>
                                <h6 class="p_price">
                              <span class="new_price">
                                <?= number_format($produtoRecente->preco, 2, ',', '.') ?>€
                              </span>
                                </h6>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<!-- end product section -->

<!-- about section -->

<div class="about_section">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-5 ml-auto">
                <div class="detail-box">
                    <div class="heading_container ">
                        <h2>
                            Sobre os Produtos Ginásio
                        </h2>
                    </div>
                    <p>
                        Este sistema é uma plataforma que permite a venda de produtos de ginásio, como roupa, artigos de
                        desporto, entre outros...
                    <hr>
                    O sistema foi desenvolvido no âmbito do projeto final de curso de TeSP em Programação de Sistemas de
                    Informação, que abrange as unidades curriculares de Plataformas de Sistemas de Informação, Acesso
                    Móvel a Sistemas de Informação e Serviços e Interoperabilidade de Sistemas.
                    </p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="img-box" style="display: flex; justify-content: center; align-items: center; height: 100%;">
                    <img src="images/Icon_Projeto.png" alt="Ícone do Projeto" style="width: 40%; height: auto;"/>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- end about section -->

<!-- why us section -->
<div class="why_us_section layout_padding">
    <div class="container">
        <div class="heading_container heading_center">
            <h2>
                Porquê escolher-nos?
            </h2>
        </div>
        <div class="row">
            <div class="col-lg-4 col-md-6 mx-auto">
                <div class="box">
                    <div class="img-box">
                        <i class="fa fa-wallet"></i>
                    </div>
                    <div class="detail-box">
                        <h5>
                            Principais Formas de Pagamento
                        </h5>
                        <hr>
                        <p>
                            <i class="fa fa-paypal"></i> Paypal
                            <br><br>
                            <i class="fa fa-credit-card"></i> Cartão de Crédito
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mx-auto">
                <div class="box">
                    <div class="img-box">
                        <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg"
                             x="0px" y="0px" viewBox="0 0 512 512"
                             style="enable-background:new 0 0 512 512;" xml:space="preserve">
                <g>
                    <g>
                        <path
                                d="M366,396c-5.52,0-10,4.48-10,10c0,5.52,4.48,10,10,10c5.52,0,10-4.48,10-10C376,400.48,371.52,396,366,396z"/>
                    </g>
                </g>
                            <g>
                                <g>
                                    <path d="M390.622,363.663l-47.53-15.84l-17.063-34.127c15.372-15.646,26.045-36.348,29.644-57.941L357.801,243H376
			c16.542,0,30-13.458,30-30v-63C406,67.29,338.71,0,256,0c-82.922,0-150,67.097-150,150v63c0,13.036,8.361,24.152,20,28.28V253
			c0,16.542,13.458,30,30,30h8.782c4.335,9.417,9.946,18.139,16.774,25.974c1.416,1.628,2.893,3.206,4.406,4.741l-17.054,34.108
			l-47.531,15.841C66.112,382.092,26,440.271,26,502c0,5.523,4.477,10,10,10h440c5.522,0,10-4.477,10-10
			C486,440.271,445.889,382.092,390.622,363.663z M386,213c0,5.514-4.486,10-10,10h-15.262c2.542-19.69,4.236-40.643,4.917-61.28
			c0.02-0.582,0.036-1.148,0.054-1.72H386V213z M136,223c-5.514,0-10-4.486-10-10v-53h20.298c0.033,1.043,0.068,2.091,0.107,3.146
			c0.001,0.036,0.003,0.071,0.004,0.107c0,0.003,0,0.006,0,0.009c0.7,20.072,2.372,40.481,4.856,59.737H136V223z M156,263
			c-5.514,0-10-4.486-10-10v-10h8.198l2.128,12.759c0.406,2.425,0.905,4.841,1.482,7.241H156z M146.017,140H126.38
			C131.445,72.979,187.377,20,256,20c68.318,0,124.496,52.972,129.619,120h-19.635c-0.72-55.227-45.693-100-101.033-100h-17.9
			C191.712,40,146.736,84.773,146.017,140z M247.05,60h17.9c44.809,0,81.076,36.651,81.05,81.41c0,3.147-0.025,5.887-0.078,8.38
			c0,0.032-0.001,0.065-0.001,0.098l-12.508-1.787c-33.98-4.852-66.064-20.894-90.342-45.172C241.195,101.054,238.652,100,236,100
			c-26.856,0-52.564,12.236-69.558,32.908C170.63,92.189,205.053,60,247.05,60z M178.54,263c-5.006-16.653-10.734-65.653-12-97.053
			l13.459-17.946c12.361-16.476,31.592-26.713,52.049-27.888c26.917,25.616,61.739,42.532,98.537,47.786l14.722,2.104
			c-0.984,20.885-2.995,41.843-5.876,61.118c-0.001,0.006-0.002,0.013-0.003,0.02c-0.916,6.197-1.638,10.185-3.482,21.324
			c-5.296,31.765-28.998,60.49-60.287,68.313c-12.877,3.215-26.443,3.214-39.313,0c-19.537-4.884-37.451-18.402-49.012-37.778
			h20.386c4.128,11.639,15.243,20,28.28,20h20c16.575,0,30-13.424,30-30c0-16.542-13.458-30-30-30h-20
			c-13.327,0-24.278,8.608-28.297,20H178.54z M235.159,341.016c6.859,1.445,13.852,2.184,20.841,2.184
			c5.471,0,10.943-0.458,16.353-1.346l-17.67,18.687L235.159,341.016z M240.935,375.079l-31.718,33.542
			c-8.732-16.714-16.235-34.109-22.389-51.917l11.911-23.822L240.935,375.079z M311.566,329.494l13.604,27.209
			c-6.164,17.838-13.669,35.239-22.392,51.933l-33.948-33.948L311.566,329.494z M226,273c0-5.521,4.478-10,10-10h20
			c5.514,0,10,4.486,10,10c0,5.522-4.479,10-10,10h-20C230.486,283,226,278.514,226,273z M46.4,492
			c3.963-49.539,36.932-94.567,81.302-109.363l42.094-14.028c7.712,21.325,17.266,42.052,28.463,61.74
			c0.019,0.034,0.037,0.068,0.056,0.101c0,0.001,0.001,0.001,0.001,0.002c8.181,14.389,17.389,28.45,27.372,41.799L237.99,492H46.4z
			 M256,483.086l-13.562-21.773c-0.152-0.244-0.314-0.481-0.486-0.711c-8.098-10.802-15.652-22.099-22.532-33.662l35.663-37.714
			l37.578,37.578c-6.926,11.647-14.506,22.991-22.611,33.796C269.56,461.253,270.255,460.224,256,483.086z M274.01,492
			l12.301-19.748c10.027-13.4,19.301-27.574,27.564-42.132c0.05-0.088,0.097-0.178,0.147-0.266c0.006-0.011,0.012-0.021,0.018-0.032
			c11.055-19.5,20.509-40.047,28.164-61.213l42.093,14.028c44.371,14.796,77.34,59.824,81.303,109.363H274.01z"/>
                                </g>
                            </g>
                            <g>
                                <g>
                                    <path d="M435.546,451.531c-6.683-13.377-16.472-25.261-28.309-34.367c-4.378-3.369-10.656-2.55-14.023,1.828
			c-3.368,4.378-2.549,10.656,1.828,14.024c9.454,7.273,17.272,16.766,22.611,27.453c2.473,4.949,8.483,6.941,13.415,4.477
			C436.008,462.478,438.013,456.472,435.546,451.531z"/>
                                </g>
                            </g>
                            <g>
                            </g>
                            <g>
                            </g>
                            <g>
                            </g>
                            <g>
                            </g>
                            <g>
                            </g>
                            <g>
                            </g>
                            <g>
                            </g>
                            <g>
                            </g>
                            <g>
                            </g>
                            <g>
                            </g>
                            <g>
                            </g>
                            <g>
                            </g>
                            <g>
                            </g>
                            <g>
                            </g>
                            <g>
                            </g>
              </svg>
                    </div>
                    <div class="detail-box">
                        <h5>
                            Suporte 24/7
                        </h5>
                        <hr>
                        <p>
                            Equipa sempre disponível para ajudar!
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end why us section -->


<!-- client section -->
<div class="client_section layout_padding">
    <div class="container">
        <div class="heading_container heading_center">
            <h2>
                Opiniões em alguns dos nossos Produtos
            </h2>
        </div>
        <div id="carouselExample2Indicators" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <div class="row">
                        <div class="col-md-7 mx-auto">
                            <div class="client_container">
                                <div class="client_detail">
                                    <p>
                                        <i class="fa fa-quote-left" aria-hidden="true"></i> &nbsp;
                                        ||
                                        <i class="fa fa-quote-right" aria-hidden="true"></i>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="row">
                        <div class="col-md-7 mx-auto">
                            <div class="client_container">
                                <div class="client_detail">
                                    <p>
                                        <i class="fa fa-quote-left" aria-hidden="true"></i> &nbsp;
                                        ||
                                        <i class="fa fa-quote-right" aria-hidden="true"></i>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="row">
                        <div class="col-md-7 mx-auto">
                            <div class="client_container">
                                <div class="client_detail">
                                    <p>
                                        <i class="fa fa-quote-left" aria-hidden="true"></i> &nbsp;
                                        ||
                                        <i class="fa fa-quote-right" aria-hidden="true"></i>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <ol class="carousel-indicators">
                <li data-target="#carouselExample2Indicators" data-slide-to="0" class="active"></li>
                <li data-target="#carouselExample2Indicators" data-slide-to="1"></li>
                <li data-target="#carouselExample2Indicators" data-slide-to="2"></li>
            </ol>
        </div>
    </div>
</div>
<!-- end client section -->


<!-- info section -->

<div class="info_section layout_padding2">
    <div class="container">
        <div class="row">
            <div class="col-lg-9 mx-auto">
                <h4>
                    Contactos
                </h4>
                <div class="contact_items">
                    <a href="">
                        <div class="img-box">
                            <i class="fa fa-map-marker" aria-hidden="true"></i>
                        </div>
                        <h6>
                            Leiria
                        </h6>
                    </a>
                    <a href="">
                        <div class="img-box">
                            <i class="fa fa-envelope" aria-hidden="true"></i>
                        </div>
                        <h6>
                            produtosginasio@gmail.com
                        </h6>
                    </a>
                    <a href="">
                        <div class="img-box">
                            <i class="fa fa-phone" aria-hidden="true"></i>
                        </div>
                        <h6>
                            (+351) 912345678
                        </h6>
                    </a>
                </div>
            </div>
        </div>

        <div class="box">
            <div class="info_social">
                <div>
                    <a href="">
                        <i class="fa fa-facebook" aria-hidden="true"></i>
                    </a>
                </div>
                <div>
                    <a href="">
                        <i class="fa fa-twitter" aria-hidden="true"></i>
                    </a>
                </div>
                <div>
                    <a href="">
                        <i class="fa fa-linkedin" aria-hidden="true"></i>
                    </a>
                </div>
                <div>
                    <a href="">
                        <i class="fa fa-instagram" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>