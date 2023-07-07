<div class="category-archive__sidebar">
    <?php
    if (is_active_sidebar('category_widget_area')) :

    ?>
        <div class="sticky-top sticky-header category-widget-area">

            <?php

            dynamic_sidebar('category_widget_area');
            ?>


        </div>
    <?php endif; ?>
</div>