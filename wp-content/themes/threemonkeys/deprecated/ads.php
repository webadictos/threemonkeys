<?php

/**
 * Funciones de Google Ad Manager
 */


//add_action('wp_head', 'wa_googleAdManager');

function wa_googleAdManager()
{
?>
    <script async src="https://securepubads.g.doubleclick.net/tag/js/gpt.js"></script>
    <script>
        const adSlots = {
            all: [],
            refreshables: [],
        };
        window.googletag = window.googletag || {
            cmd: []
        };
        googletag.cmd.push(function() {

            googletag.pubads().setTargeting("canal", ThemeSetup.page.canal);
            googletag.pubads().setTargeting("postID", ThemeSetup.page.postID);
            googletag.pubads().setTargeting("tags", ThemeSetup.page.tags);
            googletag.pubads().setTargeting("single", ThemeSetup.page.is_single);
            googletag.pubads().setTargeting("url", window.location.pathname);
            googletag.pubads().setTargeting("referrer", document.referrer.split('/')[2]);
            googletag.pubads().setTargeting('hostname', window.location.hostname);
            googletag.pubads().setCentering(true);

            googletag.pubads().setCentering(true);

            googletag.pubads().enableLazyLoad({
                fetchMarginPercent: 50,
                renderMarginPercent: 25,
                mobileScaling: 2.0
            });

            googletag.pubads().enableSingleRequest();
            googletag.pubads().disableInitialLoad();
            googletag.enableServices();

            googletag.pubads().addEventListener('slotRenderEnded', function(event) {
                if (event.isEmpty) {
                    var id = event.slot.getSlotElementId();
                    var x = document.getElementById(id);
                    x.style.display = "none";
                    //console.log("No tiene anuncio");

                    var r1 = x.closest(".ad-container");

                    if (r1) {
                        r1.style.display = "none";
                    }

                } else {
                    var id = event.slot.getSlotElementId();
                    var x = document.getElementById(id);
                    //console.log("Cargando anuncio");

                    //console.log(event.size[1]);
                    if (event.size[1] > 100) {
                        //console.log("Es mayor");
                        var r1 = x.closest(".ad-fixed-top");
                        if (r1) {
                            r1.classList.remove("sticky-top");
                            r1.classList.add("not-sticky");
                        }
                    }



                    x.classList.add("ad-slot");

                    var r1 = x.closest(".ad-container");

                    if (r1) {
                        r1.style.display = r1.style.display === 'none' ? '' : '';
                    }
                }
            });

        });
    </script>
    <?php
}

function isSlotActive($slot)
{

    $units = isset($GLOBALS['current_post_config']['exclude_adunits']) ? $GLOBALS['current_post_config']['exclude_adunits'] : array();

    if (!is_array($units)) $units = array();
    //print_r($units);
    if (in_array($slot, $units)) {
        return false;
    }

    return true;
}

function waCreatePlacement($slot = "ros-top-a", $adtype = "superbanner", $_params = array())
{
    $infinitescroll = (isset($_REQUEST['action']) &&  $_REQUEST['action'] == "loadmore") ? true : false;

    $defaultParams = array(
        'postID' => get_the_ID(),
        'css' => '',
        'canRefresh' => true,
        'mappings' => array(),
        'infinitescroll' => $infinitescroll,
    );

    $uid = uniqid();

    $uuid = $slot . "-" . $uid;

    /**
     * Hace un merge del array de parámetros con los valores por default
     */
    $params = wp_parse_args($_params, $defaultParams);
    //echo $slot;
    if ($slot !== "") {
        if (isSlotActive($slot)) :
    ?>
            <div class="ad-container dfp-ad-unit ad-<?php echo $adtype; ?> <?php echo $params['css']; ?>" id="<?php echo $uuid; ?>" data-ad-type="<?php echo $adtype; ?>" data-slot="<?php echo $slot; ?>" data-ad-setup='<?php echo json_encode($params); ?>' data-ad-loaded="0"></div>
<?php
        endif;
    }
}
