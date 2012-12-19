<?php
/**
 *
 * General functions
 *
 * @author  Nuarharuha <nhnoah+bay-isra@gmail.com>
 * @filesource https://github.com/NuarHaruha/bay-epoints/blob/master/libs/general.php
 * @since   1.0
 */

/**
 *  wrapper for ew_main_tabs()
 */
function show_ew_main_tab(){
    if (!isset($_REQUEST['panel'])): ew_main_tabs(); else: ew_main_tabs($_REQUEST['panel']); endif;
}

/**
 *  show navigation tab base on requested page
 *  @param string $current slug-name list, deposit & penalty
 */
function ew_main_tabs( $current = 'list' ) {
    $tabs = array(
        'list'       => 'Transaction',
        'deposit'    => 'Deposit',
        'penalty'    => 'Penalty',
    );

    echo '<div id="icon-wallet" class="icon32"><br></div>';
    echo '<h2 class="nav-tab-wrapper">';
    foreach( $tabs as $tab => $name ){
        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
        echo "<a class='nav-tab$class' href='/wp-admin/admin.php?page=mc-ew&panel=$tab'>$name</a>";
    }
    echo '</h2>';
}

/**
 *  wrapper for ew_setting_tabs()
 */
function show_ew_settings_tab(){
    if (!isset($_REQUEST['spanel'])): ew_setting_tabs(); else: ew_setting_tabs($_REQUEST['spanel']); endif;
}

/**
 *  show navigation tab base on requested page
 *  @param string $current slug-name settings, & bank
 */
function ew_setting_tabs( $current = 'settings' ) {
    $tabs = array(
        'settings' => 'Settings',
        'bank'     => 'Bank Account',
    );

    echo '<div id="icon-wallet" class="icon32"><br></div>';
    echo '<h2 class="nav-tab-wrapper">';
    foreach( $tabs as $tab => $name ){
        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
        echo "<a class='nav-tab$class' href='/wp-admin/admin.php?page=mc-ew-settings&spanel=$tab'>$name</a>";
    }
    echo '</h2>';
}

/**
 * form action hook
 */
function ew_action_hook(){

    $handle = 'mc-wallet-';

    /** primary page */
    if (isset($_REQUEST['panel'])){
        $handle .= $_REQUEST['panel'];
    } else {
        $handle .= 'list';
    }

    /** settings page */
    if ($_REQUEST['page'] == 'mc-ew-settings' ){

        $handle = 'mc-wallet-';

        if (isset($_REQUEST['spanel'])){
            $handle .= $_REQUEST['spanel'];
        } else {
            $handle .= 'settings';
        }
    }

    echo $handle;

}

/**
 * Get current user Points $user
 * return object
 */
function get_user_points($uid = false){

    $uid = (!$uid) ? _current_user_id() : $uid;

    return _obj(array(
       'pv' => get_points_pv($uid),
       'rm' => (float) get_points_rm($uid)
    ));
}