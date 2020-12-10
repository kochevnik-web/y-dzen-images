<?php
    require_once 'vendor/autoload.php';
    use ColorThief\ColorThief;

    if ( ! defined( 'ABSPATH' ) ) exit;

    require_once ABSPATH . 'wp-admin/includes/image.php';

    if ( !current_user_can( 'upload_files' ) ) {
        wp_die( esc_html__( 'У Вас нет доступа к данной странице!' ) );
    }

    $attachment_id = intval( $_GET['attachment_id'] );
    $attachment    = get_post( $attachment_id );
    $wp_upload_dir = wp_upload_dir();
    $path          = wp_get_original_image_path( $attachment->ID );
    $height        = 290;
    $width         = 516;

    //$dominantColor = ColorThief::getColor( $path );

    $createimg = imageCreateTrueColor( $width, $height * 2 );
    // $color = imageColorAllocate($createimg, $dominantColor[0], $dominantColor[1],$dominantColor[2]);
    $img         = imagecreatefromjpeg( $path );
    $w           = imagesx( $img );
    $h           = imagesy( $img );
    
    $imgRatio    = $w / $h;
    $ratio       = $width / $height;
    
    if ( $ratio >= $imgRatio ) {
       $newHeight = $height;
       $newWidth = $w / ( $h / $height );
    } else {
       $newWidth   = $width;
       $newHeight  = $h / ( $w / $width );
    }

    $thumb = imagecreatetruecolor( $width, $height );

    imagecopyresampled( $thumb, $img, 0 - ( $newWidth - $width ) / 2, 0 - ( $newHeight - $height ) / 2, 0, 0, $newWidth, $newHeight, $w, $h );

    $logo  = imagecreatefromjpeg( $path );
    $logo2 = imagecreatefromjpeg( $path );
    imageflip($logo2, IMG_FLIP_VERTICAL);

    imagecopyresampled($createimg,$logo,0,0,0,0,300,156, 1280, 720);
    imagecopyresampled($createimg,$logo2,0,156,0,0,300,156, 1280, 360);
    imageFilledRectangle($createimg, 0, 186, 300 - 1, 318  - 1, $color);

    $output = imagecreatetruecolor(300, 100);
    $trans_colour = imagecolorallocatealpha($output, 0, 0, 0, 127);
    imagefill($output, 0, 0, $trans_colour);
    // create the gradient
    for ( $y = 0; $y < 100; ++$y ) {
        $alpha = $y <= 0 ? 0 : round(min(($y - 0)/100, 1)*127);
        $new_color = imagecolorallocatealpha($output, $dominantColor[0], $dominantColor[1], $dominantColor[2], $alpha);
        imageline($output, 0, 100 - $y, 300, 100 - $y, $new_color);
    }

    imagecopyresampled($createimg, $output, 0, 86, 0, 0, 300, 100, 300, 100);

    imagejpeg( $thumb, $wp_upload_dir['path'] . '/' . $attachment->post_name . '_dzen.jpg', 100 );

    imagedestroy( $createimg );


    // var_dump( $attachment );

    $image_editor = wp_get_image_editor( $path );
    // var_dump( $image_editor );
    if ( ! is_wp_error( $image_editor ) ) {
        // // повернем картинку на 90 градусов
        // $image_editor->rotate( 90 );
        // // уменьшим её до размеров 80х80
        // $image_editor->resize( 80, 300, true );
        // // сохраним в корне сайта под названием new_image.png
        // $image_editor->save( $wp_upload_dir['path'] . '/' . $attachment->post_name . '_dzen.jpg' );
        
        $filename = $wp_upload_dir['path'] . '/' . $attachment->post_name . '_dzen.jpg';
        $filetype = wp_check_filetype( basename( $filename ), null );

        $attachment = array(
            'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ), 
            'post_mime_type' => $filetype['type'],
            'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
            'post_content'   => '',
            'post_status'    => 'inherit'
        );

        // $attach_id = wp_insert_attachment( $attachment, $filename, $parent_post_id );

        // $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
        // wp_update_attachment_metadata( $attach_id, $attach_data );

    }

?>