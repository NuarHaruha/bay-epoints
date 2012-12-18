<?php
/**
 *
 * General functions
 *
 * @author  Nuarharuha <nhnoah+bay-isra@gmail.com>
 * @filesource https://github.com/NuarHaruha/bay-mk/blob/master/libs/general.php
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

function ew_action_hook(){

    $handle = 'mc-wallet-';

    if (isset($_REQUEST['panel'])){
        $handle .= $_REQUEST['panel'];
    } else {
        $handle .= 'list';
    }

    echo $handle;

}