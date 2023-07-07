<?php

/**
 * Construye el layout dependiendo los parámetrs
 */


$_layoutArgs = array(
    'section_name' => 'Nuestras recomendaciones en tu correo, ¡cada semana!',
    'section_class' => '',
    'section_id' => '',
    'section_description' => null,
);

$layoutArgs = wp_parse_args($args, $_layoutArgs);
?>
<section class="section section-suscripcion <?php echo $layoutArgs['section_class']; ?> <?php echo (trim($layoutArgs['section_id']) !== "") ? "seccion-" . $layoutArgs['section_id'] : ''; ?>" data-section-id="<?php echo (trim($layoutArgs['section_id']) !== "") ? $layoutArgs['section_id'] : ''; ?>">

    <div class="container">
        <header class="section__title-container">



            <h2 class="section__title">


                <span>

                    <?php echo $layoutArgs['section_name']; ?>

                </span>



            </h2>

            <?php if (!is_null($layoutArgs['section_description']) && trim($layoutArgs['section_description']) !== "") : ?>
                <div class="section__description">
                    <?php echo wpautop($layoutArgs['section_description'], false); ?>
                </div>
            <?php endif; ?>

        </header>

        <form action="https://mailing.technology/subscribe" method="POST" accept-charset="utf-8" target="_blank" class="mx-auto mx-lg-0 suscribe-form">
            <div class="row align-items-center justify-content-center g-0">
                <div class="col-10 col-lg-8"><input type="text" class="form-control" name="name" id="name-widget" title="<?php echo __('Escribe tu nombre', 'wa-theme'); ?>" placeholder="<?php echo __('Nombre', 'wa-theme'); ?>" required></div>

            </div>

            <div class="row align-items-center justify-content-center g-0">
                <div class="col-10 col-lg-5 py-3"><input type="email" class="form-control" name="email" id="email-widget" title="<?php echo __('Ingresar correo electrónico', 'wa-theme'); ?>" placeholder="<?php echo __('Correo electrónico', 'wa-theme'); ?>" required></div>
                <div class="col-10 col-lg-3 py-3 ps-lg-3"> <button type="submit" class="btn btn-primary btn-submit w-100" aria-label="<?php echo __('Suscribirme', 'wa-theme'); ?>"><?php echo __('Suscribirme', 'wa-theme'); ?></button></div>
            </div>
            <div style="display:none;"> <label for="hp">HP</label><br> <input type="text" name="hp" id="hpfooter"></div> <input type="hidden" name="list" value="9763Q2PoM82sa9Bo2763AxKenQ" /> <input type="hidden" name="subform" value="yes">
        </form>
    </div>
</section>