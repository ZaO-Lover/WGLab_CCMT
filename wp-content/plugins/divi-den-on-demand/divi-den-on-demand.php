<?php 
/*
Plugin Name: Divi Den on Demand (beta)
Plugin URI:  https://seku.re/ddd
Description: Get easy access to tons of Free and Premium Divi Page Layouts to speed up your work flow. Find great designs and build awesome pages. Search by keyword or browse by topic, page type, product or Divi module. Use the preview button to see live and working demos of the layouts before you import them. This plugin is in BETA. We look forward to your feedback, so we can make it even more awesome.

Version:     1.3.8
Author:      Divi Den
Author URI:  https://seku.re/divi-den
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

//======================================================================
// Load Persist Admin notices Dismissal
//======================================================================

if ( ! class_exists( 'PAnD' ) ) {
require_once( plugin_dir_path( __FILE__ ) . '/include/persist-admin-notices-dismissal.php' );
add_action( 'admin_init', array( 'PAnD', 'init' ) );
}

//======================================================================
//  Load the API Key library if it is not already loaded. Must be placed in the root plugin file.
//======================================================================

if ( ! class_exists( 'ddd_AM_License_Menu' ) ) {
    require_once( plugin_dir_path( __FILE__ ) . 'ddd-am-license-menu.php' );
    ddd_AM_License_Menu::instance( __FILE__, 'Divi Den on Demand', '1.3.8', 'plugin', 'https://divi-den.com/' );
}

//======================================================================
// CHECK IF DIVI THEME INSTALLED
//======================================================================

function ddd_assistant_not_installed_admin_notice__error() {
	$class = 'notice notice-error is-dismissible';
	$message = 'Action Required: The Divi Theme is not installed. You must install the Divi Theme for the Divi Den on Demand to work. If you do not already have it, <a href="https://seku.re/get-divi" target="_blank">Get it here</a>';

	printf( '<div data-dismissible="disable-ddd-status-warning-notice-forever" class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message ); 
}

$divi_theme = wp_get_theme( 'Divi' );
if ( !($divi_theme->exists()) ) add_action( 'admin_notices', 'ddd_assistant_not_installed_admin_notice__error' );


//======================================================================
// ADD ADMIN SCRIPTS
//======================================================================

add_action( 'admin_enqueue_scripts', 'ddd_enqueue_admin_js' );

function ddd_enqueue_admin_js( $hook_suffix ) {
    wp_enqueue_script( 'ddd_assistant-clipboard', plugins_url('js/clipboard.min.js', __FILE__ ));
    wp_enqueue_script( 'ddd_assistant-admin', plugins_url('js/ddd-admin.js', __FILE__ ));
    $ddd_path_to_plugin = plugin_dir_url(__FILE__);
    wp_localize_script( 'ddd_assistant-admin', 'ddd_path_to_plugin', $ddd_path_to_plugin );
}

//======================================================================
// ADD ADMIN CSS
//======================================================================

add_action( 'admin_enqueue_scripts', 'ddd_enqueue_admin_css' );

function ddd_enqueue_admin_css() {
    wp_register_style( 'ddd_assistant-admin-css', plugins_url('css/ddd-admin.css', __FILE__ ));

 wp_enqueue_style( 'ddd_assistant-admin-css' );
}

//======================================================================
// New options for plugin's setting
//======================================================================

function ddd_add_options_on_activate() {

if (!get_option( 'ddd_enable')) add_option( 'ddd_enable', 'enabled' );

}

register_activation_hook( __FILE__, 'ddd_add_options_on_activate' );

//======================================================================
// For ajax get and set options
//======================================================================

function ddd_get_option(){
      echo get_option('ddd_enable');
      die();
}

function ddd_update_option(){
      update_option($_POST['ddd_option'], esc_attr($_POST['ddd_option_val']));
      die();
}

function ddd_get_plugin_activation_state() {
    $plugin_name = esc_attr($_GET['plugin_name']).'_assistant_activated';
    echo get_option( $plugin_name );
    die();
}

function ddd_get_divi_custom_css() {
    echo get_post_meta( $_GET['post_id'], '_et_pb_custom_css', true );
    die();
}

function ddd_update_divi_custom_css() {
    update_post_meta( $_POST['post_id'], '_et_pb_custom_css', $_POST['new_css'] );
    die();
}

//======================================================================
// SAVE TO DIVI LIBRALY
//======================================================================

if (!function_exists('write_log')) {
    function write_log ( $log )  {
        if ( true === WP_DEBUG ) {
            if ( is_array( $log ) || is_object( $log ) ) {
                error_log( print_r( $log, true ) );
            } else {
                error_log( $log );
            }
        }
    }
}


function ddd_import_posts($posts)
{
    global $wpdb;

    if (!function_exists('post_exists')) {
        require_once(ABSPATH . 'wp-admin/includes/post.php');
    }

    $imported = array();

    $posts_raw = $_POST['posts'];

    if (empty($posts_raw)) {
        echo 'empry posts raw';
        return;
    }

   // write_log('THIS IS THE START OF MY CUSTOM DEBUG');

    $posts = str_replace ('\\"','"',$posts_raw);
    $posts = str_replace ('\\\\','\\',$posts);
    $posts = str_replace ("\'", "'" ,$posts);
  //write_log('$posts: '.$posts);
  $posts = html_entity_decode($posts);
    $posts = json_decode($posts, true);

    if (empty($posts)) {
        echo 'empry posts';
        return;
    }


    foreach ($posts as $old_post_id => $post) {
        if (isset($post['post_status']) && 'auto-draft' === $post['post_status']) {
            continue;
        }

        $post_exists = post_exists($post['post_title']);

        // Make sure the post is published and stop here if the post exists.
        if ($post_exists && get_post_type($post_exists) == $post['post_type']) {
            if ('publish' == get_post_status($post_exists)) {
                $imported[$post_exists] = $post['post_title'];
                continue;
            }
        }

        if (isset($post['ID'])) {
            $post['import_id'] = $post['ID'];
            unset($post['ID']);
        }

        $post['post_author'] = (int)get_current_user_id();

        // Insert or update post.
        $post_id = wp_insert_post($post, true);

        if (!$post_id || is_wp_error($post_id)) {
            continue;
        }

        if (!isset($post['terms'])) {
            $post['terms'] = array();
        }

        $post['terms'][] = array('name' => 'Divi Den', 'slug' => 'divi-den', 'taxonomy' => 'layout_category', 'parent' => 0, 'description' => ''); 

        // Insert and set terms.
        if (count($post['terms']) > 0) {
            $processed_terms = array();

            foreach ($post['terms'] as $term) {

                if (empty($term['parent'])) {
                    $parent = 0;
                } else {
                    $parent = term_exists($term['name'], $term['taxonomy'], $term['parent']);

                    if (is_array($parent)) {
                        $parent = $parent['term_id'];
                    }
                }

                if (!$insert = term_exists($term['name'], $term['taxonomy'], $term['parent'])) {
                    $insert = wp_insert_term($term['name'], $term['taxonomy'], array(
                        'slug' => $term['slug'],
                        'description' => $term['description'],
                        'parent' => intval($parent),
                    ));
                }

                if (is_array($insert) && !is_wp_error($insert)) {
                    $processed_terms[$term['taxonomy']][] = $term['slug'];
                }
            }

            // Set post terms.
            foreach ($processed_terms as $taxonomy => $ids) {
                wp_set_object_terms($post_id, $ids, $taxonomy);
            }
        }

        // Insert or update post meta.
        if (isset($post['post_meta']) && is_array($post['post_meta'])) {
            foreach ($post['post_meta'] as $meta_key => $meta) {

                $meta_key = sanitize_text_field($meta_key);

                if (count($meta) < 2) {
                    $meta = wp_kses_post($meta[0]);
                } else {
                    $meta = array_map('wp_kses_post', $meta);
                }

                update_post_meta($post_id, $meta_key, $meta);
            }
        }

        $imported[$post_id] = $post['post_title'];

    }

    return $imported;


    die();
}

if( is_admin() ) { 
  add_action('wp_ajax_ddd_update_option', 'ddd_update_option', 10, 2);
  add_action('wp_ajax_ddd_get_option', 'ddd_get_option', 9, 1); 
  add_action('wp_ajax_ddd_get_plugin_activation_state', 'ddd_get_plugin_activation_state', 8, 1); 
  add_action('wp_ajax_ddd_get_divi_custom_css', 'ddd_get_divi_custom_css', 7, 1); 
  add_action('wp_ajax_ddd_import_posts', 'ddd_import_posts', 5, 1);
}



//======================================================================
// AUTOUPDATE
//======================================================================

require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://s3-eu-west-1.amazonaws.com/ddlicensed/ddd/onetime/dddpluginsupdates/ddd-plugin-update.json',
	__FILE__,
	'divi-den-on-demand'
);


?>