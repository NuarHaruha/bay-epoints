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