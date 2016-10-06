# Flarum Sitemap

## About

This is a PHP file that acts as a sitemap to use on Google Webmasters, Bing Webmaster Tools, or any other location where a sitemap is accepted.

## Installation

1. Download a ZIP (or other archival format) of the repository
2. Extract the contents of the ZIP to a new location
3. Open `sitemap.php` with a text editor
4. Configure each of the settings to your desire (***bold*** items are the most important)
    1. ***`$FORUM_URL`*** - the base URL of your forum (e.g. `http://example.com/flarum`)
    2. `BASE` - set to `true` or `false` to include the base URL (`$FORUM_URL`) in the sitemap
    3. `TAG_PAGE` - set to `true` or `false` to include the `tag` page in the sitemap
    4. `TAGS` - set to `true` or `false` to include each individual tag's page in the sitemap
    5. `DISCUSSIONS` - set to `true` or `false` to include each discussion in the sitemap
    6. ***`ONLY_USE_ID`*** - set to `true` or `false` to only use a discussion's ID for the URL (e.g. `http://example.com/flarum/d/2` instead of `http://example.com/flarum/d/2-test-discussion`)
    7. `USERS` - set to `true` or `false` to include each user in the sitemap
    8. `USERS_DISCUSSIONS` - set to `true` or `false` to include the user's discussion page (only if `USERS` is also true)
    9. `USERS_MENTIONS` - set to `true` or `false` to include the user's mentions page (only if `USERS` is also true)
5. Upload the `sitemap.php` file to the same location as your Flarum installation
6. When a service asks for a sitemap, provide the link to the PHP file (e.g. `http://example.com/flarum/sitemap.php`)

## Note:
If the sitemap ends up containing more than 50,000 URL entries, or the XML generated is over 10 MB in size, the sitemap will likely not work; these are limits to the sitemap protocol, and they can be fixed by using a sitemap index and multiple sitemaps. However, **that is not done here**.
