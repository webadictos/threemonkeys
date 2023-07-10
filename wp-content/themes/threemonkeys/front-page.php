<?php

/**
 * Template Name: Homepage
 * Description: Home Page
 *
 */

get_header();

//the_post();
?>

<main class="site-main" role="main" data-bs-spy="scroll" data-bs-target="#navbar">

    <?php

    the_content();
    ?>


</main>

<?php
get_footer();
