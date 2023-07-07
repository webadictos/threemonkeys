<?php

/**
 * Construye el layout dependiendo los parámetrs
 */


$_layoutArgs = array(
    'section_name' => 'Síguenos',
    'section_class' => '',
    'section_id' => '',
    'section_description' => null,
    'username' => '',
    'section_link' => '',
    'section_show_link' => false,
);

$layoutArgs = wp_parse_args($args, $_layoutArgs);
?>
<section class="section section-instagram <?php echo $layoutArgs['section_class']; ?> <?php echo (trim($layoutArgs['section_id']) !== "") ? "seccion-" . $layoutArgs['section_id'] : ''; ?>" data-section-id="<?php echo (trim($layoutArgs['section_id']) !== "") ? $layoutArgs['section_id'] : ''; ?>">

    <header class="section__title-container">



        <h2 class="section__title">


            <?php
            if ($layoutArgs['section_show_link']) :
            ?>

                <?php
                if ($layoutArgs['section_link'] !== "") :
                    $link = $layoutArgs['section_link'];

                ?>
                    <a href="<?php echo $link; ?>" title="<?php echo $layoutArgs['section_name']; ?>" target="_blank" rel="noopener noreferrer nofollow">

                <?php
                endif;
            endif;
                ?>

                <span>
                    <?php if ($layoutArgs['section_name'] !== "") : ?>

                        <?php echo $layoutArgs['section_name']; ?>

                    <?php endif; ?>

                </span>


                <?php if ($layoutArgs['section_show_link']) : ?>

                    </a>
                <?php endif; ?>


        </h2>

        <?php if (!is_null($layoutArgs['section_description']) && trim($layoutArgs['section_description']) !== "") : ?>
            <div class="section__description">
                <?php echo wpautop($layoutArgs['section_description'], false); ?>
            </div>
        <?php endif; ?>

    </header>


    <div class="carrusel-items position-relative container">


        <div id="carrusel-instagram-container" class="scrolling-wrapper wa-instagram-grid py-5" data-user="<?php echo $layoutArgs['username']; ?>" data-element="Grid de Instagram">





        </div>

    </div>

</section>