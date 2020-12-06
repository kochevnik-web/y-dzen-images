<?php
/**
 * Plugin Name: Yandex Dzen Images
 * Plugin URI:  https://wordpress.org
 * Version:     1.0.0
 * Description: The plugin creates images as in Yandex Dzen when uploading images to the site
 * Author:      Dmitriy Kovalev
 * Author URI:  https://github.com/kochevnik-web
 *
 */


add_action('admin_menu', 'cumenu');
function cumenu(){
    add_submenu_page(null, 'Create Yandex Dzen Images', 'Create Yandex Dzen Images', 'upload_files', 'y-dzen-images/make', 'croute');
}

function croute(){
    global $plugin_page;
    $plugin_path = plugin_dir_path(__FILE__);

    $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : '';
    if (! check_admin_referer( $action, '_wpnonce') )
    {
      die('Invalid Nonce');
    }

    elseif ( $action == 'media_dzen_make' ) {
        require_once($plugin_path . 'make.php');
      }
      else {
        exit('Something went wrong loading page, please try again');
      }
}

function cgetMediaReplaceURL( $attach_id ) {
  $url = admin_url( "upload.php" );
  $url = add_query_arg(array(
      'page'          => 'y-dzen-images/make',
      'action'        => 'media_dzen_make',
      'attachment_id' => $attach_id,
  ), $url );

  return $url;

}

add_filter( 'media_row_actions', 'custom_add_media_action', 10, 2 );
function custom_add_media_action( $actions, $post ) {
    $url     = cgetMediaReplaceURL( $post->ID );
    $action  = "media_dzen_make";
    $editurl = wp_nonce_url( $url, $action );

    $link = "href=\"$editurl\"";

    $newaction['dzenimage'] = '<a ' . $link  . '>Сделать Дзен</a>';
    return array_merge( $actions, $newaction );
}