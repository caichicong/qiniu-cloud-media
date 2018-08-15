<?php
/*
Plugin Name: WordPress Qiniu Media
Plugin URI:  https://github.com/caichicong/qiniu-cloud-media
Description: Create custom post type for files on qiniu cloud
Version:     0.1.0
Author:      chicong cai
Author URI:  http://blog.hexccc.com

*/

include __DIR__ . '/vendor/autoload.php';
use Qiniu\Storage\UploadManager;
use Qiniu\Auth;

error_reporting(-1);
ini_set('display_errors', 1);

defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );

define("QINIU_POST_TYPE", "qiniu_media");
include __DIR__ . '/qiniuOptions.php';

$options = get_option(QINIU_OPTIONS);

define("QINIU_ACCESS_KEY", $options['access_key']);
define("QINIU_SECRET_KEY", $options['secret_key']);
define("QINIU_DOMAIN", $options['cdn_domain']);

function get_posts_with_count($args) {

    $wp_query = new WP_Query( $args );
    $return = ["count" => 0, "data" => []];
    $return["count"] = $wp_query->found_posts;
    if($return["count"] > 0 ) {
        foreach($wp_query->posts as $post) {
            $return["data"][] = $post;
        }
    }
    return $return;
}

function qiniu_batch_upload_page() {
    include __DIR__ . '/pages/batchUpload.php';
}

add_action( 'admin_menu', 'my_admin_menu' );

function my_admin_menu() {
    add_submenu_page(
        'edit.php?post_type='. QINIU_POST_TYPE,
        '七牛云文件批量上传',
        '批量上传',
        'upload_files',
        'qiniu-batch-upload',
        'qiniu_batch_upload_page'
    );
}

function qiniu_media_custom_post_type() {
    $labels = array(
        'name'                  => _x( '七牛云文件', 'Post Type General Name', 'text_domain' ),
        'singular_name'         => _x( '七牛云文件', 'Post Type Singular Name', 'text_domain' ),
        'menu_name'             => __( '七牛云文件', 'text_domain' ),
        'name_admin_bar'        => __( 'Post Type', 'text_domain' ),
        'archives'              => __( 'Item Archives', 'text_domain' ),
        'attributes'            => __( 'Item Attributes', 'text_domain' ),
        'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
        'all_items'             => __( '文件列表', 'text_domain' ),
        'add_new_item'          => __( '添加新文件', 'text_domain' ),
        'add_new'               => __( '添加新文件', 'text_domain' ),
        'new_item'              => __( '添加新文件', 'text_domain' ),
        'edit_item'             => __( 'Edit Item', 'text_domain' ),
        'update_item'           => __( 'Update Item', 'text_domain' ),
        'view_item'             => __( 'View Item', 'text_domain' ),
        'view_items'            => __( 'View Items', 'text_domain' ),
        'search_items'          => __( 'Search Item', 'text_domain' ),
        'not_found'             => __( '找不到', 'text_domain' ),
        'not_found_in_trash'    => __( '回收站里找不到', 'text_domain' ),
        'featured_image'        => __( 'Featured Image', 'text_domain' ),
        'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
        'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
        'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
        'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
        'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
        'items_list'            => __( 'Items list', 'text_domain' ),
        'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
        'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
    );
    $args = array(
        'label'                 => __( 'Qiniu Media', 'text_domain' ),
        'description'           => __( 'Qiniu Media(Video, Image)', 'text_domain' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'custom-fields', 'post-formats', 'thumbnail' ),
        'taxonomies'            => array( 'category', 'post_tag' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => false,
        'exclude_from_search'   => true,
        'publicly_queryable'    => true,
        'capability_type'       => 'page',
    );
    register_post_type( QINIU_POST_TYPE, $args );
}
add_action( 'init', 'qiniu_media_custom_post_type', 0 );

function add_qiniu_media_columns($columns) {
    return array_merge($columns,
        array('thumbnail' => "缩略图"));
}
add_filter('manage_' . QINIU_POST_TYPE . '_posts_columns' , 'add_qiniu_media_columns');

add_action( 'manage_posts_custom_column' , 'qiniu_media_custom_columns', 10, 2 );

function qiniu_media_custom_columns( $column, $post_id ) {
    if($column == "thumbnail") {
        $key = get_post_meta($post_id, "key", true);
        printf("<img src='%s/%s?%s' />", "http://" . QINIU_DOMAIN, $key, 'imageView2/1/w/70/h/70/format/jpg/q/75|imageslim');
    }
}

// 引入webuploader资源
function load_weduploader_style() {
    wp_register_style('webuploader_admin_css', plugins_url() . '/qiniu-media/webuploader/webuploader.css', false, '1.0.1' );
    wp_enqueue_style('webuploader_admin_css');

    wp_enqueue_script( 'webuploader_script', plugins_url() . '/qiniu-media/webuploader/webuploader.html5only.min.js', array (), 1.2, false);
    wp_enqueue_script( 'webuploader_upload_script', plugins_url() . '/qiniu-media/js/upload.js', array (), 1.11, true);
}

add_action( 'admin_enqueue_scripts', 'load_weduploader_style' );

function get_media_categories() {
    $mediaCategories = get_categories('taxonomy=category&post_type=qiniu_media');
    header( "Content-Type: application/json");
    echo json_encode($mediaCategories);
    wp_die();
}
add_action( 'wp_ajax_get_media_categories', 'get_media_categories');

function get_qiniu_image_list() {
    $catid = intval($_POST['catid']);
    $pagesize = 10;
    $page = intval($_POST['page']);
    $offset = ($page - 1) * $pagesize;

    $result = get_posts_with_count(array(
        'offset'=> $offset,
        'post_type' => 'qiniu_media',
        'posts_per_page' => $pagesize,
        'orderby' => 'date',
        'tax_query' => array(
            array(
                'taxonomy' => 'category',
                'field' => 'term_id',
                'terms' => $catid,
                'include_children' => false
            )
        )
    ));

    $func = function($post) {
        $key = get_post_meta($post->ID, "key", true);
        return [
            "id" => $post->ID,
            "key" => $key
        ];
    };

    $imageList = array_map($func, $result['data']);
    header( "Content-Type: application/json");
    echo json_encode(["imgs" => $imageList, "pagecount" => ceil($result['count'] / $pagesize) ]);
    wp_die();
}
add_action( 'wp_ajax_get_qiniu_image_list', 'get_qiniu_image_list');

function qiniu_upload_action() {
    $upManager = new UploadManager();
    $bucketName = "videoplugintest";
    $auth = new Auth(QINIU_ACCESS_KEY, QINIU_SECRET_KEY);
    $token = $auth->uploadToken($bucketName);

    $tmpFilePath = $_FILES['file']['tmp_name'];
    list($ret, $err) = $upManager->putFile($token, null, $tmpFilePath);

    $my_post = array();
    $my_post['post_title']    = $_FILES['file']['name'];
    $my_post['post_content']  = '';
    $my_post['post_type'] = QINIU_POST_TYPE;
    $my_post['post_status']   = 'publish';
    $my_post['post_author']   = get_current_user_id();
    $my_post['post_category'] = array(0);
    $postID = wp_insert_post( $my_post );

    update_post_meta( $postID, 'key',  $ret['key']);
    update_post_meta( $postID, 'hash',  $ret['hash']);

    header( "Content-Type: application/json" );
    if ($err !== null) {
        echo json_encode(["error" => $err]);
    } else {
        // $ret->hash , $ret->key
        echo json_encode(["error" => 0, "ret" => $ret]);
    }


    wp_die();
}
add_action( 'wp_ajax_qiniu_upload_action', 'qiniu_upload_action');

// shortcode
function qiniu_image_shortcode( $atts ) {
    $key = get_post_meta($atts['id'], "key", true);
    $src = "http://" . QINIU_DOMAIN . '/' . $key;

    if(!empty($atts['action'])) {
        $src = $src . "?" . $atts['action'];
    }

    return "<img src='{$src}' />";
}
add_shortcode( 'qimg', 'qiniu_image_shortcode' );

function uploda_qiniu_media_form()
{
    printf("[qimg id='%d']", $_GET['post']) ;
}

function add_qiniu_meta_box(){
    add_meta_box('qiniu-media-shortcode-id', 'shortcode', 'uploda_qiniu_media_form', 'qiniu_media', 'normal', 'high');
}
add_action('add_meta_boxes', 'add_qiniu_meta_box');

function qiniu_media_insert_button() {
    wp_enqueue_script( 'insert_media_script', plugins_url() . '/qiniu-media/js/insertMedia.js', array (), 1.0, true);
}

add_action( 'admin_enqueue_scripts', 'qiniu_media_insert_button' );

function qiniu_media_thickbox() {
    include __DIR__ . '/pages/thickbox.php';
}

add_action('wp_enqueue_media', 'qiniu_media_thickbox');


