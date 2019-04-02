<?php

/**
 * WordPress eXtended RSS file parser implementations
 *
 * @package WordPress
 * @subpackage Importer
 */
/**
 * WordPress Importer class for managing parsing of WXR files.
 */
class WXR_Parser
{
    function parse($file)
    {
    }
}
/**
 * WXR Parser that makes use of the SimpleXML PHP extension.
 */
class WXR_Parser_SimpleXML
{
    function parse($file)
    {
    }
}
/**
 * WXR Parser that makes use of the XML Parser PHP extension.
 */
class WXR_Parser_XML
{
    var $wp_tags = array('wp:post_id', 'wp:post_date', 'wp:post_date_gmt', 'wp:comment_status', 'wp:ping_status', 'wp:attachment_url', 'wp:status', 'wp:post_name', 'wp:post_parent', 'wp:menu_order', 'wp:post_type', 'wp:post_password', 'wp:is_sticky', 'wp:term_id', 'wp:category_nicename', 'wp:category_parent', 'wp:cat_name', 'wp:category_description', 'wp:tag_slug', 'wp:tag_name', 'wp:tag_description', 'wp:term_taxonomy', 'wp:term_parent', 'wp:term_name', 'wp:term_description', 'wp:author_id', 'wp:author_login', 'wp:author_email', 'wp:author_display_name', 'wp:author_first_name', 'wp:author_last_name');
    var $wp_sub_tags = array('wp:comment_id', 'wp:comment_author', 'wp:comment_author_email', 'wp:comment_author_url', 'wp:comment_author_IP', 'wp:comment_date', 'wp:comment_date_gmt', 'wp:comment_content', 'wp:comment_approved', 'wp:comment_type', 'wp:comment_parent', 'wp:comment_user_id');
    function parse($file)
    {
    }
    function tag_open($parse, $tag, $attr)
    {
    }
    function cdata($parser, $cdata)
    {
    }
    function tag_close($parser, $tag)
    {
    }
}
/**
 * WXR Parser that uses regular expressions. Fallback for installs without an XML parser.
 */
class WXR_Parser_Regex
{
    var $authors = array();
    var $posts = array();
    var $categories = array();
    var $tags = array();
    var $terms = array();
    var $base_url = '';
    function __construct()
    {
    }
    function parse($file)
    {
    }
    function get_tag($string, $tag)
    {
    }
    function process_category($c)
    {
    }
    function process_tag($t)
    {
    }
    function process_term($t)
    {
    }
    function process_author($a)
    {
    }
    function process_post($post)
    {
    }
    function _normalize_tag($matches)
    {
    }
    function fopen($filename, $mode = 'r')
    {
    }
    function feof($fp)
    {
    }
    function fgets($fp, $len = 8192)
    {
    }
    function fclose($fp)
    {
    }
}
class WP_Import extends \WP_Importer
{
    var $max_wxr_version = 1.2;
    // max. supported WXR version
    var $id;
    // WXR attachment ID
    // information to import from WXR file
    var $version;
    var $authors = array();
    var $posts = array();
    var $terms = array();
    var $categories = array();
    var $tags = array();
    var $base_url = '';
    // mappings from old information to new
    var $processed_authors = array();
    var $author_mapping = array();
    var $processed_terms = array();
    var $processed_posts = array();
    var $post_orphans = array();
    var $processed_menu_items = array();
    var $menu_item_orphans = array();
    var $missing_menu_items = array();
    var $fetch_attachments = \false;
    var $url_remap = array();
    var $featured_images = array();
    /**
     * Registered callback function for the WordPress Importer
     *
     * Manages the three separate stages of the WXR import process
     */
    function dispatch()
    {
    }
    /**
     * The main controller for the actual import stage.
     *
     * @param string $file Path to the WXR file for importing
     */
    function import($file)
    {
    }
    /**
     * Parses the WXR file and prepares us for the task of processing parsed data
     *
     * @param string $file Path to the WXR file for importing
     */
    function import_start($file)
    {
    }
    /**
     * Performs post-import cleanup of files and the cache
     */
    function import_end()
    {
    }
    /**
     * Handles the WXR upload and initial parsing of the file to prepare for
     * displaying author import options
     *
     * @return bool False if error uploading or invalid file, true otherwise
     */
    function handle_upload()
    {
    }
    /**
     * Retrieve authors from parsed WXR data
     *
     * Uses the provided author information from WXR 1.1 files
     * or extracts info from each post for WXR 1.0 files
     *
     * @param array $import_data Data returned by a WXR parser
     */
    function get_authors_from_import($import_data)
    {
    }
    /**
     * Display pre-import options, author importing/mapping and option to
     * fetch attachments
     */
    function import_options()
    {
    }
    /**
     * Display import options for an individual author. That is, either create
     * a new user based on import info or map to an existing user
     *
     * @param int $n Index for each author in the form
     * @param array $author Author information, e.g. login, display name, email
     */
    function author_select($n, $author)
    {
    }
    /**
     * Map old author logins to local user IDs based on decisions made
     * in import options form. Can map to an existing user, create a new user
     * or falls back to the current user in case of error with either of the previous
     */
    function get_author_mapping()
    {
    }
    /**
     * Create new categories based on import information
     *
     * Doesn't create a new category if its slug already exists
     */
    function process_categories()
    {
    }
    /**
     * Create new post tags based on import information
     *
     * Doesn't create a tag if its slug already exists
     */
    function process_tags()
    {
    }
    /**
     * Create new terms based on import information
     *
     * Doesn't create a term its slug already exists
     */
    function process_terms()
    {
    }
    /**
     * Add metadata to imported term.
     *
     * @since 0.6.2
     *
     * @param array $term    Term data from WXR import.
     * @param int   $term_id ID of the newly created term.
     */
    protected function process_termmeta($term, $term_id)
    {
    }
    /**
     * Create new posts based on import information
     *
     * Posts marked as having a parent which doesn't exist will become top level items.
     * Doesn't create a new post if: the post type doesn't exist, the given post ID
     * is already noted as imported or a post with the same title and date already exists.
     * Note that new/updated terms, comments and meta are imported for the last of the above.
     */
    function process_posts()
    {
    }
    /**
     * Attempt to create a new menu item from import data
     *
     * Fails for draft, orphaned menu items and those without an associated nav_menu
     * or an invalid nav_menu term. If the post type or term object which the menu item
     * represents doesn't exist then the menu item will not be imported (waits until the
     * end of the import to retry again before discarding).
     *
     * @param array $item Menu item details from WXR file
     */
    function process_menu_item($item)
    {
    }
    /**
     * If fetching attachments is enabled then attempt to create a new attachment
     *
     * @param array $post Attachment post details from WXR
     * @param string $url URL to fetch attachment from
     * @return int|WP_Error Post ID on success, WP_Error otherwise
     */
    function process_attachment($post, $url)
    {
    }
    /**
     * Attempt to download a remote file attachment
     *
     * @param string $url URL of item to fetch
     * @param array $post Attachment details
     * @return array|WP_Error Local file location details on success, WP_Error otherwise
     */
    function fetch_remote_file($url, $post)
    {
    }
    /**
     * Attempt to associate posts and menu items with previously missing parents
     *
     * An imported post's parent may not have been imported when it was first created
     * so try again. Similarly for child menu items and menu items which were missing
     * the object (e.g. post) they represent in the menu
     */
    function backfill_parents()
    {
    }
    /**
     * Use stored mapping information to update old attachment URLs
     */
    function backfill_attachment_urls()
    {
    }
    /**
     * Update _thumbnail_id meta to new, imported attachment IDs
     */
    function remap_featured_images()
    {
    }
    /**
     * Parse a WXR file
     *
     * @param string $file Path to WXR file for parsing
     * @return array Information gathered from the WXR file
     */
    function parse($file)
    {
    }
    // Display import page title
    function header()
    {
    }
    // Close div.wrap
    function footer()
    {
    }
    /**
     * Display introductory text and file upload form
     */
    function greet()
    {
    }
    /**
     * Decide if the given meta key maps to information we will want to import
     *
     * @param string $key The meta key to check
     * @return string|bool The key if we do want to import, false if not
     */
    function is_valid_meta_key($key)
    {
    }
    /**
     * Decide whether or not the importer is allowed to create users.
     * Default is true, can be filtered via import_allow_create_users
     *
     * @return bool True if creating users is allowed
     */
    function allow_create_users()
    {
    }
    /**
     * Decide whether or not the importer should attempt to download attachment files.
     * Default is true, can be filtered via import_allow_fetch_attachments. The choice
     * made at the import options screen must also be true, false here hides that checkbox.
     *
     * @return bool True if downloading attachments is allowed
     */
    function allow_fetch_attachments()
    {
    }
    /**
     * Decide what the maximum file size for downloaded attachments is.
     * Default is 0 (unlimited), can be filtered via import_attachment_size_limit
     *
     * @return int Maximum attachment file size to import
     */
    function max_attachment_size()
    {
    }
    /**
     * Added to http_request_timeout filter to force timeout at 60 seconds during import
     * @return int 60
     */
    function bump_request_timeout($val)
    {
    }
    // return the difference in length between two strings
    function cmpr_strlen($a, $b)
    {
    }
}
// class_exists( 'WP_Importer' )
function wordpress_importer_init()
{
}