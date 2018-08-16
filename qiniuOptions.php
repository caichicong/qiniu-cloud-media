<?php

/**
 * custom option and settings
 */


function qiniu_media_settings_init() {
    register_setting( QINIU_MEDIA_OPTION_GROUP, QINIU_MEDIA_OPTIONS );

    add_settings_section(
        'qiniu_section',
        "七牛云",
        'qiniu_section_cb',
        'qinu_media_option'
    );

    add_settings_field(
        'qiniu_access_key',
        "access key",
        'qiniu_field_cb',
        'qinu_media_option',
        'qiniu_section',
        [
            'label_for' => 'access_key',
            'class' => 'qiniu_row',
        ]
    );

    add_settings_field(
        'qiniu_secret_key',
        "secret key",
        'qiniu_field_cb',
        'qinu_media_option',
        'qiniu_section',
        [
            'label_for' => 'secret_key',
            'class' => 'qiniu_row',
        ]
    );

    add_settings_field(
        'qiniu_cdndomain',
        "CDN域名",
        'qiniu_field_cb',
        'qinu_media_option',
        'qiniu_section',
        [
            'label_for' => 'cdn_domain',
            'class' => 'qiniu_row',
        ]
    );
}

function qiniu_field_cb($args) {
    $options = get_option(QINIU_MEDIA_OPTIONS);

    $val = isset($options[$args['label_for']]) ? $options[$args['label_for']] : "" ;

    ?>
    <input style="width: 400px" id="<?php echo esc_attr( $args['label_for'] ); ?>" type="text" value="<?php echo $val;?>" name="qiniu_options[<?php echo $args['label_for']; ?>]"/>
    <?php
}

add_action( 'admin_init', 'qiniu_media_settings_init' );

function qiniu_section_cb() {
    ?>
        <p>七牛云存储参数</p>

    <?php
}

function qiniu_media_options_page() {
    add_submenu_page(
        'edit.php?post_type='. QINIU_MEDIA_POST_TYPE,
        '七牛云参数设置',
        '参数设置',
        'manage_options',
        'qinu_media_option',
        'qiniu_media_options_page_html'
    );
}

add_action( 'admin_menu', 'qiniu_media_options_page' );

function qiniu_media_options_page_html() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    if ( isset( $_GET['settings-updated'] ) ) {
        // add settings saved message with the class of "updated"
        add_settings_error( 'qiniu_messages', 'qiniu_message', 'Settings Saved', 'updated' );
    }

    // show error/update messages
    settings_errors( 'qiniu_messages' );

    ?>

    <div class="wrap">
        <form action="options.php" method="post">
            <?php
            settings_fields( QINIU_MEDIA_OPTION_GROUP);
            do_settings_sections( 'qinu_media_option');
            submit_button( 'Save Settings' );
            ?>
        </form>
    </div>

    <?php
}