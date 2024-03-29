<?php
/**
 *
 * Register admin hook functions
 *
 * @author  Nuarharuha <nhnoah+bay-isra@gmail.com>
 * @filesource https://github.com/NuarHaruha/bay-epoints/blob/master/libs/actions.php
 * @since   1.0
 */

/**
 *  run after transaction insert
 *  @see eWalletTransaction
 */
add_action('init', 'ew_log_transaction');
function ew_log_transaction(){
    foreach_callbacks_hook(array(
        'act_transaction_log_modified_by',
        'act_transaction_note'), 'after_transaction_log',1,2);

    add_action('after_transaction_log','act_transaction_end',100,2);
}

/**
 * add modified by user ID data for every successful transaction
 *
 * @uses        hook for {@after_transaction_log}
 * @uses        ewallet_transaction::add_meta() for saving metadata value
 * @see         ewallet_transaction::_log_transaction()
 *
 * @author      Nuarharuha <nhnoah+bay-isra@gmail.com>
 * @since       0.1
 *
 * @global      object      $current_user       used to check for user id
 * @param       int         $transaction_id     valid transaction id
 * @param       mixed       $transaction        array of data from previous transaction
 *                                              {@see eWalletTransaction::_log_transaction()}
 * @return      void
 */
function act_transaction_log_modified_by($transaction_id, $transaction){
    global $current_user;

    $current_uid = (!empty($current_user->ID) ) ? $current_user->ID : _current_user_id();

    eWalletTransaction::add_meta($transaction_id, 'transaction_by', $current_uid);
}

function act_transaction_note($transaction_id, $transaction){

    if (isset($_REQUEST['transaction_note']) && !empty($_REQUEST['transaction_note'])){
        $note = (string) $_REQUEST['transaction_note'];
        eWalletTransaction::add_meta($transaction_id, 'transaction_note', $note);
    }
}

/**
 * redirect transaction
 * to receipt details page
 */
function act_transaction_end($transaction_id, $transaction){
    $url = sprintf(PATHTYPE::URI_EW_RECEIPT, $transaction_id, $transaction['uid'], (int) $transaction['transaction']);
    PATHTYPE::REDIRECT($url);
}

add_action('init','mc_filter_init');

function mc_filter_init(){
    foreach_filters(array(
        'before_save_'.MKEY::MIN_TRANSFER,
        'before_save_'.MKEY::MIN_WITHDRAWAL), 'filter_before_save_minimum');
}

function filter_before_save_minimum($amount){
    $amount   = strtolower($amount);
    $currency = strtolower(get_currency());
    $amount = strem($currency,$amount);
    $amount = strem(' ',$amount);

    return (int) $amount;
}

function foreach_filters($hooks, $callback){

    foreach($hooks as $hook) {
        add_filter($hook, $callback);
    }
}