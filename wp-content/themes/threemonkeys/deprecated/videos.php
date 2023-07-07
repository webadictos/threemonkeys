<?php

/**
 * YouTube Functions
 */

function waGetYouTubeVideos($maxResults = 4)
{
    $channelID =  $GLOBALS['youtube_channel']; //The username(example: johndoe)
    $API_key =  $GLOBALS['GoogleAPIKey'];

    if (false === ($videoList = get_transient('wa-youtube-list'))) {

        $videoList = json_decode(file_get_contents('https://www.googleapis.com/youtube/v3/search?order=date&part=snippet&channelId=' . $channelID . '&maxResults=' . $maxResults . '&key=' . $API_key . ''));
        if ($videoList) {
            set_transient('wa-youtube-list', $videoList, HOUR_IN_SECONDS * 12);
        }
    }

    return $videoList;
}

function waGetYouTubeThumbnail($id)
{
    if (waCheckUrl('https://i.ytimg.com/vi/' . $id . '/maxresdefault.jpg')) {
        $image = 'https://i.ytimg.com/vi/' . $id . '/maxresdefault.jpg';
    } elseif (waCheckUrl('https://i.ytimg.com/vi/' . $id . '/hqdefault.jpg')) {
        $image = 'https://i.ytimg.com/vi/' . $id . '/hqdefault.jpg';
    } elseif (waCheckUrl('https://i.ytimg.com/vi/' . $id . '/mqdefault.jpg')) {
        $image = 'https://i.ytimg.com/vi/' . $id . '/mqdefault.jpg';
    } else {
        $image = false;
    }
    return $image;
}

function waCheckUrl($url)
{
    $response = wp_remote_get($url);
    if (is_wp_error($response))
        //request can't performed
        return false;
    if (wp_remote_retrieve_response_code($response) == '404')
        //request succeed and link not found
        return false;
    //request succeed and link exist
    return true;
}
