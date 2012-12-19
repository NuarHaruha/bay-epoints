<?php do_action('main_ewallet_settings_request', $_REQUEST);?>
<div class="wrap ewallet settings">
    <?php show_ew_settings_tab();?>
    <?php settings_errors(); ?>
    <?php do_action('mc_notification', $_REQUEST);?>
    <form name="report-form" method="post">
        <input type="hidden" name="action" value="<?php echo ew_action_hook();?>">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>"/>
        <?php wp_nonce_field(WTYPE::NONCE_WALLET);
        wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
        wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-<?php echo 1 == get_current_screen()->get_columns() ? '1' : '2'; ?>">
                <div id="post-body-content" class="dn">
                    <?php do_action('main_ewallet_settings_content', $_REQUEST); ?>
                </div>
                <div id="postbox-container-1" class="postbox-container">
                    <?php do_meta_boxes('','side',null); ?>
                </div>
                <div id="postbox-container-2" class="postbox-container">
                    <?php do_meta_boxes('','normal',null);  ?>
                    <?php do_meta_boxes('','advanced',null); ?>
                </div>
            </div> <!-- #post-body -->
        </div> <!-- #poststuff -->
    </form>
    <script>
        jQuery(document).ready(function($){
            $('.fade').fadeOut('slow');
            $('.go-back').click(function(e){
                e.preventDefault(); window.history.go(-1); });
        });
    </script>
</div>