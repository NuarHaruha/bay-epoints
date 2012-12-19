<?php
/**
 *
 * Wallet functions
 *
 * @author  Nuarharuha <nhnoah+bay-isra@gmail.com>
 * @filesource https://github.com/NuarHaruha/bay-epoints/blob/master/libs/wallet.php
 * @since   1.0
 */

/**
 * Get e-wallet transaction records
 * @since 1.0
 * @uses Wpdb
 *
 * @param   int $tid transaction id
 * @return  mixed|object  object of $wpdb results
 */
function get_ew_transaction($tid){
    global $wpdb;

    $tid    = (int) $tid;
    $db     = WTYPE::DB(WTYPE::DB_PRIMARY);
    $sql    = "SELECT * FROM $db WHERE transaction_id=%d";
    return $wpdb->get_results($wpdb->prepare($sql, $tid));
}

/**
 * Translate currency
 * @see WTYPE
 *
 * @param  int  $currency_code
 * @return string if exists or empty on false
 */
function tr_currency($currency_code){

    $currency = '';

    switch ($currency_code){
        case WTYPE::RM:
            $currency = 'RM';
            break;
        case WTYPE::PV:
            $currency = 'PV';
            break;
        case WTYPE::BV:
            $currency = 'BV';
            break;
    }

    return $currency;
}

function tr_code($code){
    return translate_transaction_code($code);
}

function ew_format_receipt($req){
    if (is_array($req))
        $req = _obj($req);
    printf('#%s%s-%s', $req->uid, $req->code, $req->tid);
}

function ew_approved_by($tid, $return_id = false){
    $uid = get_transaction_meta($tid, MKEY::TRANS_BY);

    if ($uid){
        return (! $return_id) ? uinfo($uid,'name') : (int) $uid;
    } else {
        return false;
    }
}

function ew_note($tid){
    $note = get_transaction_meta($tid, MKEY::TRANS_NOTES);

    if ($note){
        return (string) $note;
    } else {
        return false;
    }
}

function get_currency(){
    $currency = get_option(MKEY::CURRENCY);
    return apply_filters('get_currency', $currency);
}