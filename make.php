<?php

    if ( ! defined( 'ABSPATH' ) ) exit;

    if ( !current_user_can( 'upload_files' ) ) {
        wp_die( esc_html__( 'У Вас нет доступа к данной странице!' ) );
    }

    $attachment_id = intval( $_GET['attachment_id'] );
    $attachment = get_post( $attachment_id );

    var_dump( $attachment );

?>