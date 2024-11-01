<?php
/*
Plugin Name: WebP Images
Plugin URI: https://totalpress.org/plugins/webp-images?utm_source=wp-dashboard&utm_medium=installed-plugin&utm_campaign=webp-images
Description: Convert and compress images in WebP format easily. Speed up your website.
Author: TotalPress.org
Author URI: https://totalpress.org/?utm_source=wp-dashboard&utm_medium=installed-plugin&utm_campaign=webp-images
Text Domain: webp-images
Domain Path: /languages/
Version: 2.0.0
*/

if (!defined('ABSPATH')) {
    die('Invalid request.');
}

define('WEBP_IMAGES_BULK_ELEMENTS', 5); // Elements for single bulk action
define('WEBP_IMAGES_VERSION', get_file_data(__FILE__, ['Version' => 'Version'], false)['Version']);


require plugin_dir_path(__FILE__) . '/vendor/autoload.php';

use WebPConvert\WebPConvert;

require plugin_dir_path(__FILE__) . '/inc/functions.php';

if (!class_exists('webp_images')) {
    class webp_images
    {
        public function __construct()
        {
            add_action('init', array($this, 'init_load_textdomain'));
            add_action('admin_menu', array($this, 'init_menu_page'));
            add_action('admin_enqueue_scripts', array($this, 'init_admin_enqueue'));
            add_action('init', array($this, 'dismiss_compatibility_notice'));
            add_action('admin_notices', array($this, 'init_admin_notices'));
            add_filter('plugin_action_links', array($this, 'init_plugin_action_links'), 10, 2);
            add_action('wp_ajax_webp_images_ajax', array($this, 'ajax_actions'));
            add_action('webp_images_start_conversion', array($this, 'start_conversion'));
            add_action('delete_attachment', array($this, 'auto_delete'));


            // Utilities
            $this->applyUpdates();
            $this->pluginActions();
        }

        public function init_load_textdomain()
        {
            load_plugin_textdomain('webp-images', false, dirname(plugin_basename(__FILE__)) . '/languages');
        }

        public function is_pro_version_active()
        {
            $return = false;
            $pro_version = in_array('webp-images-pro/webp-images-pro.php', apply_filters('active_plugins', get_option('active_plugins')));
            if ($pro_version) {
                $return = true;
            }
            return $return;
        }

        public function is_apache()
        {
            $return = false;
            $software = isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : false;
            if ($software && strpos($software, 'Apache') !== false) {
                $return = true;
            }
            return $return;
        }

        public function htaccess_is_ok()
        {
            if (!function_exists('get_home_path')) {
                require_once ABSPATH . 'wp-admin/includes/file.php';
            }
            $return = false;
            $htaccess = get_home_path() . ".htaccess";
            if (file_exists($htaccess) && is_writable($htaccess)) {
                $return = true;
            }
            return $return;
        }

        public function is_compatible()
        {
            $return = false;
            if (webp_images_getCwebpStatus()[0] == true) {
                $return = true;
            }
            if (webp_images_getImageMagickStatus()[0] == true) {
                $return = true;
            }
            if (webp_images_getVipsStatus()[0] == true) {
                $return = true;
            }
            if (webp_images_getImagickStatus()[0] == true) {
                $return = true;
            }
            if (webp_images_getGraphicsMagickStatus()[0] == true) {
                $return = true;
            }
            if (webp_images_getGdStatus()[0] == true) {
                $return = true;
            }
            return $return;
        }

        public function init_plugin_action_links($links, $file)
        {
            if ($file == 'webp-images/webp-images.php') {
                $links[] = sprintf('<a href="%s" target="_blank"> %s </a>', 'https://wordpress.org/support/plugin/webp-images', __('Support', 'webp-images'));
                $links[] = sprintf('<a href="%s"> %s </a>', admin_url('upload.php?page=webp-images-settings'), __('Settings', 'webp-images'));
                if (!$this->is_pro_version_active()) {
                    $links[] = sprintf('<a href="%s" style="font-weight: bold;"> %s </a>', 'https://www.andreadegiovine.it/risorse/plugin/webp-images?utm_source=tools_plugin_page&utm_medium=plugin_page&utm_campaign=webp_images', __('Get PRO', 'webp-images'));
                }
            }
            return $links;
        }

        public function init_admin_notices()
        {

            if (!$this->is_compatible() && get_option('webp_images_compatibility_notice', 0) == 0) { ?>
                <div class="notice notice-error is-dismissible">
                    <p><?php _e('<strong>WebP Images</strong> it appears that your server does not (or partially) have the required features.<br>If errors or problems occur while converting images, contact your provider to enable at least one of the required features.', 'webp-images'); ?></p>
                    <p><a href="<?php echo admin_url('upload.php?page=webp-images-settings&action=status'); ?>" class="button button-secondary"><?php _e('Check system status', 'webp-images'); ?></a> <a href="<?php echo add_query_arg(array('action' => 'dismiss_webp', 'token' => wp_create_nonce('webp-images-dismiss')), admin_url('upload.php?page=webp-images-settings')); ?>" class="button button-primary"><?php _e('Ok! I know', 'webp-images'); ?></a></p>
                </div>
            <?php }
            if (!$this->is_apache()) { ?>
                <div class="notice notice-error is-dismissible">
                    <p><?php _e('<strong>WebP Images</strong> works only on Apache servers.<br>Usually all Linux servers / hosting use this software, Windows servers / hosting are not supported. Ask your provider.', 'webp-images'); ?></p>
                    <p><a href="https://www.apache.org" class="button button-secondary" target="_blank" rel="nofollow"><?php _e('Read more about Apache', 'webp-images'); ?></a></p>
                </div>
            <?php }
            if (!$this->htaccess_is_ok()) { ?>
                <div class="notice notice-error is-dismissible">
                    <p><?php _e('<strong>WebP Images</strong> requires that .htaccess file exists in the root of the WordPress installation, and that it is writable.<br>Check if the file exists (via FTP or other file manager) and if it has the correct write permissions (644 for files).<br>To automatically generate this file, visit the "Settings > Permalinks" page and save the settings without making any changes.', 'webp-images'); ?></p>
                    <p><a href="<?php echo admin_url('options-permalink.php'); ?>" class="button button-primary"><?php _e('Save permalinks', 'webp-images'); ?></a> <a href="https://wordpress.org/support/article/changing-file-permissions/" class="button button-secondary" target="_blank" rel="nofollow"><?php _e('WordPress File Permissions', 'webp-images'); ?></a></p>
                </div>
            <?php }
        }

        public function init_menu_page()
        {
            add_submenu_page('upload.php', __('WebP Images settings', 'webp-images'), __('WebP settings', 'webp-images'), 'manage_options', 'webp-images-settings', array($this, 'render_menu_page'));

            add_action('admin_init', array($this, 'register_webp_images_settings'));
        }

        public function register_webp_images_settings()
        {
            register_setting('webp-images-settings-group', 'webp-images-pro_license_key');
            register_setting('webp-images-settings-group', 'webp-images-pro_last_license_check');
            register_setting('webp-images-settings-group', 'webp_images_quality');
            register_setting('webp-images-settings-group', 'webp_images_auto_convert');
        }

        public function render_menu_page()
        {
            $main_page = menu_page_url('webp-images-settings', false);
            $status_page = $main_page . '&action=status';
            $request_page = isset($_GET['action']) && !empty($_GET['action']) && in_array($_GET['action'], array('status')) ? $_GET['action'] : 'main';
            ?>
            <div class="wrap webp-images-plugin">
                <h1><?php _e('WebP Images settings', 'webp-images'); ?></h1>

                <div class="webp-images-container">
                    <div class="webp-images-main">

                        <nav class="nav-tab-wrapper wp-clearfix" aria-label="Secondary menu">
                            <a href="<?php echo $main_page; ?>" class="nav-tab<?php echo ($request_page == 'main' ? ' nav-tab-active' : ''); ?>"><?php _e('General Settings', 'webp-images'); ?></a>
                            <a href="<?php echo $status_page; ?>" class="nav-tab<?php echo ($request_page == 'status' ? ' nav-tab-active' : ''); ?>"><?php _e('Check status', 'webp-images'); ?></a>
                        </nav>
                        <?php include(plugin_dir_path(__FILE__) . 'parts/options-page-' . $request_page . '.php'); ?>
                    </div>
                    <div class="webp-images-sidebar">
                        <?php include(plugin_dir_path(__FILE__) . 'parts/sidebar.php'); ?>
                    </div>
                </div>
            </div>
<?php
        }

        public function init_admin_enqueue()
        {
            wp_register_style('admin-webp-images', plugin_dir_url(__FILE__) . 'assets/admin-ui.css', false, '1.0.0');
            wp_enqueue_style('admin-webp-images');

            wp_register_script('admin-webp-images-js', plugin_dir_url(__FILE__) . 'assets/admin.js', array('jquery'));
            wp_localize_script('admin-webp-images-js', 'WebpImages', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('webp-images-ajax'),
                'error' => __('WebP Images: an error has occurred, check the logs for more details.', 'webp-images'),
                'beforeunload' => __('WebP Images: before leaving the page, wait for the end of the processes in progress.', 'webp-images'),
            ));
            wp_enqueue_script('jquery');
            wp_enqueue_script('admin-webp-images-js');
        }

        public function get_media_images()
        {
            $args = array(
                'post_type' => 'attachment',
                'post_mime_type' => array(
                    'jpg|jpeg|jpe' => 'image/jpeg',
                    //'gif' => 'image/gif',
                    'png' => 'image/png',
                ),
                'post_status' => 'inherit',
                'posts_per_page' => -1,
            );
            $all_images = get_posts($args);
            return $all_images;
        }

        public function get_not_webp_media_images($limit = -1)
        {
            $return_array = array();

            $args = array(
                'post_type' => 'attachment',
                'orderby' => 'rand',
                'order' => 'ASC',
                'post_mime_type' => array(
                    'jpg|jpeg|jpe' => 'image/jpeg',
                    //'gif' => 'image/gif',
                    'png' => 'image/png',
                ),
                'meta_query' => array(
                    array(
                        'key' => 'webp_src',
                        'compare' => 'NOT EXISTS'
                    )
                ),
                'post_status' => 'inherit',
                'posts_per_page' => $limit,
            );
            $all_images = get_posts($args);

            foreach ($all_images as $image) {
                $image_id = $image->ID;
                $image_path = get_attached_file($image_id);
                $image_file = basename($image_path);
                $return_array[$image_id][$image_file] = $image_path;
                $image_sizes = get_post_meta($image_id, '_wp_attachment_metadata', true);
                $sizes = isset($image_sizes['sizes']) ? $image_sizes['sizes'] : false;
                if ($sizes) {
                    foreach ($sizes as $size => $value) {
                        $resized_image_path = dirname($image_path) . '/' . $value['file'];
                        if (!file_exists($resized_image_path)) {
                            continue;
                        }
                        $return_array[$image_id][$value['file']] = $resized_image_path;
                    }
                }
            }
            return $return_array;
        }

        public function get_webp_media_images($limit = -1)
        {
            $return_array = array();

            $args = array(
                'post_type' => 'attachment',
                'post_mime_type' => array(
                    'jpg|jpeg|jpe' => 'image/jpeg',
                    //'gif' => 'image/gif',
                    'png' => 'image/png',
                ),
                'meta_query' => array(
                    array(
                        'key' => 'webp_src',
                        'value' => 'done',
                    ),
                ),
                'post_status' => 'inherit',
                'posts_per_page' => $limit,
            );
            $all_images = get_posts($args);
            foreach ($all_images as $image) {
                $image_id = $image->ID;
                $image_path = get_attached_file($image_id);
                $image_file = basename($image_path);
                $return_array[$image_id][$image_file] = $image_path;
                $image_sizes = get_post_meta($image_id, '_wp_attachment_metadata', true);
                $sizes = isset($image_sizes['sizes']) ? $image_sizes['sizes'] : false;
                if ($sizes) {
                    foreach ($sizes as $size => $value) {
                        $resized_image_path = dirname($image_path) . '/' . $value['file'];
                        if (!file_exists($resized_image_path)) {
                            continue;
                        }
                        $return_array[$image_id][$value['file']] = $resized_image_path;
                    }
                }
            }
            return $return_array;
        }

        public function count_media_images()
        {
            $all_images = $this->get_media_images();
            return count($all_images);
        }

        public function count_not_webp_media_images()
        {
            $all_images = $this->get_not_webp_media_images();
            return count($all_images);
        }

        public function count_webp_media_images()
        {
            $all_images = $this->get_webp_media_images();
            return count($all_images);
        }

        public function convert_img_to_webp($original_path = false, $new_path = false, $quality = 80)
        {
            if (!$original_path || !$new_path || !file_exists($original_path) || !is_writable(dirname($new_path))) {
                return false;
            }
            if (file_exists($new_path)) {
                return true;
            }
            if ($this->is_pro_version_active()) {
                $quality = (int) get_option('webp_images_quality', 80);
                if ($quality > 100 || $quality < 1) {
                    $quality = 80;
                }
            }
            $options = array(
                'converters' => array('cwebp', 'vips', 'imagick', 'gmagick', 'imagemagick',  'graphicsmagick', 'wpc', 'ewww', 'gd'),
                'png' => array('quality' => $quality),
                'jpeg' => array('quality' => $quality),
            );
            WebPConvert::convert($original_path, $new_path, $options);
            if (file_exists($new_path)) {
                return true;
            } else {
                return false;
            }
        }

        public function convert_to_webp($files = []){
            $result = true;
            foreach ($files as $name => $path) {
                $base_path = dirname($path);
                $conversion = $this->convert_img_to_webp($path, $base_path . '/' . $name . '.webp');
                if (!$conversion) {
                    $result = false;
                    break;
                }
            }
            return $result;
        }

        public function remove_webp_image($webp_path = false)
        {
            if (!$webp_path || !is_writable(dirname($webp_path))) {
                return false;
            }
            if (!file_exists($webp_path)) {
                return true;
            }
            unlink($webp_path);
            return true;
        }

        public function delete_webp($files = []){
            $result = true;
            foreach ($files as $name => $path) {
                $wepb_path = dirname($path) . '/' . $name . '.webp';
                $delete = $this->remove_webp_image($wepb_path);
                if (!$delete) {
                    $result = false;
                    break;
                }
            }
            return $result;
        }

        public function dismiss_compatibility_notice()
        {
            if (!$this->is_apache() || !$this->htaccess_is_ok() || !is_admin() || !isset($_GET['action']) || $_GET['action'] !== 'dismiss_webp' || !isset($_GET['token']) || !wp_verify_nonce($_GET['token'], "webp-images-dismiss")) {
                return;
            }
            update_option('webp_images_compatibility_notice', 1);
            wp_redirect(admin_url('upload.php?page=webp-images-settings&action=status'));
            exit;
        }

        public function write_htaccess_rules()
        {
            if (!function_exists('get_home_path')) {
                require_once ABSPATH . 'wp-admin/includes/file.php';
            }
            if (!function_exists('insert_with_markers')) {
                require_once ABSPATH . 'wp-admin/includes/misc.php';
            }

            if (!$this->is_apache() || !$this->htaccess_is_ok()) {
                return;
            }

            $htaccess = get_home_path() . ".htaccess";

            $write_rules = array();
            $write_rules[] = "# BEGIN WebP Images";
            $write_rules[] = "<IfModule mod_setenvif.c>
    # Vary: Accept for all the requests to jpeg, png, and gif.
    SetEnvIf Request_URI \"\.(jpg|jpeg|jpe|png|gif)$\" REQUEST_image
</IfModule>";
            $write_rules[] = "<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /

    # Check if browser supports WebP images.
    RewriteCond %{HTTP_ACCEPT} image/webp

    # Check if WebP replacement image exists.
    RewriteCond %{REQUEST_FILENAME}.webp -f

    # Serve WebP image instead.
    RewriteRule (.+)\.(jpg|jpeg|jpe|png|gif)$ $1.$2.webp [T=image/webp,NC]
</IfModule>";
            $write_rules[] = "<IfModule mod_headers.c>
    Header append Vary Accept env=REQUEST_image
</IfModule>";
            $write_rules[] = "<IfModule mod_mime.c>
    AddType image/webp .webp
</IfModule>";
            $write_rules[] = "# END WebP Images";
            $write_rules[] = "\n";


            $oldContents = file_get_contents($htaccess);
            $fr = @fopen($htaccess, 'w');
            fwrite($fr, implode("\n", $write_rules));
            fwrite($fr, $oldContents);
            fclose($fr);
        }

        public function remove_htaccess_rules()
        {
            if (!function_exists('get_home_path')) {
                require_once ABSPATH . 'wp-admin/includes/file.php';
            }

            if (!$this->is_apache() || !$this->htaccess_is_ok()) {
                return;
            }

            $htaccess = get_home_path() . ".htaccess";
            $markerdata = explode("\n", implode('', file($htaccess)));
            $found = false;
            $newdata = '';
            foreach ($markerdata as $line) {
                if ($line == '# BEGIN WebP Images') {
                    $found = true;
                }
                if (!$found) {
                    $newdata .= "{$line}\n";
                }
                if ($line == '# END WebP Images') {
                    $found = false;
                }
            }
            // write back
            $f = @fopen($htaccess, 'w');
            if (fwrite($f, $newdata)) {
                return true;
            } else {
                return false;
            }
        }

        public function write_log($log)
        {
            if (true === WP_DEBUG) {
                if (is_array($log) || is_object($log)) {
                    error_log(print_r($log, true));
                } else {
                    error_log($log);
                }
            }
        }

        public function start_conversion($limit){
            $images_for_conversion = $this->get_not_webp_media_images($limit);

            if (count($images_for_conversion) < 1) {
                return;
            }

            $original_time_limit = ini_get('max_execution_time');
            ini_set('max_execution_time', 0);

            foreach ($images_for_conversion as $id => $files) {
                $media_is_converted = $this->convert_to_webp($files);
                if($media_is_converted){
                    update_post_meta($id, 'webp_src', 'done');
                } else {
                    $this->delete_webp($files);
                    update_post_meta($id, 'webp_src', 'error');
                }
            }

            ini_set('max_execution_time', $original_time_limit);
        }

        public function auto_delete($attachment_id){
            $has_webp_version = get_post_meta($attachment_id, 'webp_src', true) == 'done';
            if(!$has_webp_version){
                return;
            }

            $files = [];

            $image_path = get_attached_file($attachment_id);
            $image_file = basename($image_path);
            $files[$image_file] = $image_path;
            $image_sizes = get_post_meta($attachment_id, '_wp_attachment_metadata', true);
            $sizes = isset($image_sizes['sizes']) ? $image_sizes['sizes'] : false;
            if ($sizes) {
                foreach ($sizes as $size => $value) {
                    $resized_image_path = dirname($image_path) . '/' . $value['file'];
                    if (!file_exists($resized_image_path)) {
                        continue;
                    }
                    $files[$value['file']] = $resized_image_path;
                }
            }

            $media_webp_is_deleted = $this->delete_webp($files);
            if($media_webp_is_deleted){
                delete_post_meta($attachment_id, 'webp_src');
            }
        }

        public function ajax_actions()
        {
            $action = !empty($_REQUEST['type']) && in_array($_REQUEST['type'], ['conversion', 'delete']) ? $_REQUEST['type'] : false;
            if (!$action) {
                wp_send_json_error();
            }

            $nonce = !empty($_REQUEST['token']) && wp_verify_nonce($_REQUEST['token'], "webp-images-ajax");
            if (!$nonce) {
                wp_send_json_error();
            }

            $max_elements_for_bulk = apply_filters('webp_images_bulk_max_elements', WEBP_IMAGES_BULK_ELEMENTS);

            if($action == 'conversion'){

                do_action('webp_images_start_conversion', $max_elements_for_bulk);

                wp_send_json_success($this->count_not_webp_media_images());

            } else {
                $images_for_deletion = $this->get_webp_media_images($max_elements_for_bulk);

                if (count($images_for_deletion) < 1) {
                    wp_send_json_success(0);
                    return;
                }

                $original_time_limit = ini_get('max_execution_time');
                ini_set('max_execution_time', 0);

                foreach ($images_for_deletion as $id => $files) {
                    $media_webp_is_deleted = $this->delete_webp($files);
                    if($media_webp_is_deleted){
                        delete_post_meta($id, 'webp_src');
                    }
                }

                ini_set('max_execution_time', $original_time_limit);

                wp_send_json_success($this->count_webp_media_images());
            }
        }

        private function pluginActions()
        {
            $currentVersion = WEBP_IMAGES_VERSION;
            register_activation_hook(__FILE__, function () use ($currentVersion) {
                $this->write_htaccess_rules();
                $request_url = add_query_arg(
                    ['id' => 589, 'action' => 'activate', 'domain' => md5(get_home_url()), 'v' => $currentVersion],
                    'https://totalpress.org/wp-json/totalpress/v1/plugin-growth'
                );
                wp_remote_get($request_url);
            });
            register_deactivation_hook(__FILE__, function () use ($currentVersion) {
                $this->remove_htaccess_rules();
                $request_url = add_query_arg(
                    ['id' => 589, 'action' => 'deactivate', 'domain' => md5(get_home_url()), 'v' => $currentVersion],
                    'https://totalpress.org/wp-json/totalpress/v1/plugin-growth'
                );
                wp_remote_get($request_url);
            });
        }

        private function applyUpdates()
        {
            $installedVersion = get_option('webp_images_version', null);
            $currentVersion = WEBP_IMAGES_VERSION;

            if (version_compare($installedVersion, $currentVersion, '=')) {
                return;
            }

            if (version_compare($installedVersion, $currentVersion, '<')) {
                // Apply updates
            }

            update_option('webp_images_version', $currentVersion);
            update_option('webp_images_installation_time', time());

            if(!empty($installedVersion)){
                $request_url = add_query_arg(
                    ['id' => 589, 'action' => 'updated', 'domain' => md5(get_home_url()), 'v' => $currentVersion],
                    'https://totalpress.org/wp-json/totalpress/v1/plugin-growth'
                );
                wp_remote_get($request_url);
            }
        }
    }
    new webp_images();

    do_action('webp_images_loaded');
}
