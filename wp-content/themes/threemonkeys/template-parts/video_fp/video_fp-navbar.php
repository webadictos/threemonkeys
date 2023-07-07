<?php

$categories = apply_filters('get_categories_from_videos', array());

$queried_object = get_queried_object();

$type = get_class($queried_object);


if (is_array($categories) && count($categories) > 0) :
?>

    <nav class="navbar video-archive__navbar">
        <ul class="navbar-nav">
            <?php
            foreach ($categories as $category) :
                $active = "";


                if ($type === "WP_Term") {
                    if ($category->slug == $queried_object->slug) $active = "active";
                }
            ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $active; ?>" href="/to-watch/category/<?php echo $category->slug; ?>/"><?php echo $category->name; ?></a>
                </li>
            <?php endforeach; ?>
            <!-- <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="#">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Features</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Pricing</a>
            </li>
            <li class="nav-item">
                <a class="nav-link disabled">Disabled</a>
            </li> -->
        </ul>
    </nav>
<?php endif; ?>