<?php

// The base forum url. e.g. "http://example.com" or "http://example.com/flarum"
$FORUM_URL = "http://example.com";

// Add the base URL to the sitemap
const BASE = true;

// Add the page with the list of tags to the sitemap
const TAG_PAGE = true;
// Add each tag's own page to the sitemap
const TAGS = true;

// Add the discussions to the sitemap
const DISCUSSIONS = true;
// Use the discussion URL with only an ID instead of the full slug (http://example.com/d/2 instead of http://example.com/2-test-discussion)
const ONLY_USE_ID = false;

// Include users in the sitemap
const USERS = true;
// If including users, also include their profile discussions page
const USERS_DISCUSSIONS = true;
// If including users, also include their profile mentions page
const USERS_MENTIONS = true;




// Set the content type to XML since sitemaps use XML
header("Content-Type: application/xml");

if (substr($FORUM_URL, -1) == "/") // If the URL contains a trailing slash
    $FORUM_URL = substr($FORUM_URL, 0, -1); // take it off


// Get all initial information from the API
$top_level = get_data($FORUM_URL . "/api/forum");
if ($top_level === false)
    die("Unable to do anything");

// Total records; Sitemaps cannot be > 50,000 URLs and each file cannot be > 10 MB; however, that is not implemented
$total_records = 0;

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

// Index page (also "all discussions" page)
if (BASE) {
    echo '    <url>';
    echo '        <loc>' . $top_level["data"]["attributes"]["baseUrl"] . '</loc>';
    echo '        <changefreq>always</changefreq>';
    echo '    </url>';
    $total_records++;
}

// Tags page
if (TAG_PAGE) {
    echo '    <url>';
    echo '        <loc>' . $top_level["data"]["attributes"]["baseUrl"] . '/tags</loc>';
    echo '        <changefreq>always</changefreq>';
    echo '    </url>';
    $total_records++;
}

// Tags
if (TAGS) {
    foreach ($top_level["included"] as $tag) {
        if ($tag["type"] != "tags")
            continue;

        echo '    <url>';
        echo '        <loc>' . $top_level["data"]["attributes"]["baseUrl"] . '/t/' . $tag["attributes"]["slug"] . '</loc>';
        if ($tag["attributes"]["lastTime"] != null)
            echo '        <lastmod>' . $tag["attributes"]["lastTime"] . '</lastmod>';
        echo '        <changefreq>always</changefreq>';
        echo '    </url>';
        $total_records++;
    }
}

// Discussions
if (DISCUSSIONS) {
    $discussions = get_data($top_level["data"]["attributes"]["apiUrl"] . "/discussions");

    while ($discussions !== false) {
        foreach ($discussions["data"] as $url) {
            echo '    <url>';
            if (ONLY_USE_ID)
                echo '        <loc>' . $top_level["data"]["attributes"]["baseUrl"] . '/d/' . $url["id"] . '</loc>';
            else
                echo '        <loc>' . $top_level["data"]["attributes"]["baseUrl"] . '/d/' . $url["id"] . '-' . $url["attributes"]["slug"] . '</loc>';
            echo '        <lastmod>' . $url["attributes"]["lastTime"] . '</lastmod>';
            echo '        <changefreq>always</changefreq>';
            echo '    </url>';
            $total_records++;
        }

        if (array_key_exists("next", $discussions["links"]))
            $discussions = get_data(urldecode($discussions["links"]["next"]));
        else
            break;
    }
}

// Users
if (USERS) {
    $users = get_data($top_level["data"]["attributes"]["apiUrl"] . "/users");

    while ($users !== false) {
        foreach ($users["data"] as $url) {
            echo '    <url>';
            echo '        <loc>' . $top_level["data"]["attributes"]["baseUrl"] . '/u/' . $url["attributes"]["username"] . '</loc>';
            if (array_key_exists("lastSeenTime", $url["attributes"]))
                echo '        <lastmod>' . $url["attributes"]["lastSeenTime"] . '</lastmod>';
            else
                echo '        <lastmod>' . $url["attributes"]["joinTime"] . '</lastmod>';
            echo '        <changefreq>always</changefreq>';
            echo '    </url>';
            $total_records++;

            if (USERS_DISCUSSIONS) {
                echo '    <url>';
                echo '        <loc>' . $top_level["data"]["attributes"]["baseUrl"] . '/u/' . $url["attributes"]["username"] . '/discussions</loc>';
                echo '        <changefreq>always</changefreq>';
                echo '    </url>';
                $total_records++;
            }

            if (USERS_MENTIONS) {
                echo '    <url>';
                echo '        <loc>' . $top_level["data"]["attributes"]["baseUrl"] . '/u/' . $url["attributes"]["username"] . '/mentions</loc>';
                echo '        <changefreq>always</changefreq>';
                echo '    </url>';
                $total_records++;
            }
        }

        if (array_key_exists("next", $users["links"]))
            $users = get_data(urldecode($users["links"]["next"]));
        else
            break;
    }
}

echo '</urlset>';

function get_data ($url) {
    if (extension_loaded("curl")) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curl);
        curl_close($curl);

        if ($result !== false)
            return json_decode($result, true);
    }

    $file = file_get_contents($url);
    if ($file !== false)
        return json_decode($file, true);

    // If cURL and file_get_contents don't work, return false
    return false;
}
