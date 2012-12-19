<?php
/**
 * Points function
 * @package isralife
 * 
 * @author  Nuarharuha <nhnoah+bay-isra@gmail.com>
 * @filesource https://github.com/NuarHaruha/bay-epoints/blob/master/libs/points.php
 * @version 0.1
 */
 
/** filter_points()
 * 
 * filter empty & negative points
 * 
 * @author  Nuarharuha <nhnoah+bay-isra@gmail.com>
 * @since   1.0.0
 * @version 0.1
 * 
 * @param   int     $points     valid integer
 * @return  int                 points
 */ 
function filter_points($points){
    $points = ( ('' == $points || $points < 0 ) ? 0 : $points);
    return apply_filters('filter_points', $points);
}

/** get_points()
 * 
 * get number of wallet points
 * 
 * @author  Nuarharuha <nhnoah+bay-isra@gmail.com>
 * @since   1.0.0
 * @version 0.1
 * 
 * @param   int     $uid        valid wp user_id
 * @param   string  $type       type of points to update, default is RM
 *                              MKEY, MKEY::RM, MKEY::PV, MKEY::POINTS
 * @return  float               return 0 on false
 */ 	
function get_points($uid, $type = MKEY::RM) {    
	$points = get_user_meta($uid, $type, 1);	
    return filter_points($points);
}

/** get_points_rm()
 * 
 * A shorthand function for get_points()
 * get Points in RM
 * 
 * @author  Nuarharuha <nhnoah+bay-isra@gmail.com>
 * @since   1.0.0
 * 
 * @param   int     $uid        valid wp user_id
 * @return  float               return 0 on false
 */ 	
function get_points_rm($uid) { return get_points($uid); }

/** get_points_pv()
 * 
 * A shorthand function for get_points()
 * get Points in PV
 * 
 * @author  Nuarharuha <nhnoah+bay-isra@gmail.com>
 * @since   1.0.0
 * 
 * @param   int     $uid        valid wp user_id
 * @return  float               return 0 on false
 */ 	
function get_points_pv($uid) { return get_points($uid, MKEY::PV); }

/** update_points()
 * 
 * update ewallet points
 * 
 * @author  Nuarharuha <nhnoah+bay-isra@gmail.com>
 * @since   1.0.0
 * @version 0.1
 * 
 * @param   int     $uid        valid wp user_id
 * @param   float   $points     points to add must be int or float
 * @param   string  $type       type of points to update, default is RM
 *                              MKEY, MKEY::RM, MKEY::PV, MKEY::POINTS
 */ 
function update_points($uid, $points, $type = MKEY::RM) {    
    $points = filter_points($points);    
	return update_user_meta($uid, $type, $points);
}

/** update_points_rm()
 * 
 * A shorthand function for update_points()
 * update user RM points
 * 
 * @author  Nuarharuha <nhnoah+bay-isra@gmail.com>
 * @since   1.0.0
 * 
 * @param   int     $uid        valid wp user_id
 */ 	
function update_points_rm($uid, $points) {
    return update_points($uid, $points);
}

/** update_points_pv()
 * 
 * A shorthand function for update_points()
 * update user PV points
 * 
 * @author  Nuarharuha <nhnoah+bay-isra@gmail.com>
 * @since   1.0.0
 * 
 * @param   int     $uid        valid wp user_id
 */ 	
function update_points_pv($uid, $points) { return update_points($uid, $points, MKEY::PV); }

/** deposit_points()
 * 
 * alter points function, incremental update
 * 
 * @author  Nuarharuha <nhnoah+bay-isra@gmail.com>
 * @since   1.0.0
 * @version 0.1
 * 
 * @param   int     $uid        valid wp user_id
 * @param   float   $points     points to add must be int or float
 * @param   string  $type       type of points to update, default is RM
 *                              MKEY, MKEY::RM, MKEY::PV, MKEY::POINTS
 */ 
function deposit_points($uid, $points, $type = MKEY::RM) {
    
    $points = filter_points($points);    
    $amount = get_points($uid, $type) + $points;
    
	return update_points($uid, $amount, $type);
}

/** deposit_points_rm()
 * 
 * A shorthand function for deposit_points()
 * Deposit user RM points
 * 
 * @author  Nuarharuha <nhnoah+bay-isra@gmail.com>
 * @since   1.0.0
 * 
 * @param   int     $uid        valid wp user_id
 */ 	
function deposit_points_rm($uid, $points) { return deposit_points($uid, $points); }

/** deposit_points_pv()
 * 
 * A shorthand function for deposit_points()
 * Deposit user PV points
 * 
 * @author  Nuarharuha <nhnoah+bay-isra@gmail.com>
 * @since   1.0.0
 * 
 * @param   int     $uid        valid wp user_id
 */ 	
function deposit_points_pv($uid, $points) { return deposit_points($uid, $points, MKEY::PV); }

function translate_transaction_code($transaction_code)
{
    switch ($transaction_code){
        case WTYPE::RM:
            return 'RM';
            break;
        case WTYPE::PV:
            return 'PV';
            break;    
        case WTYPE::DEPOSIT_RM:
        case WTYPE::DEPOSIT_PV:
            return 'Deposit';
            break;
        case WTYPE::PENALTY_RM:
        case WTYPE::PENALTY_PV:
            return 'Penalty - Deduction';
            break;
    }
    
    return $transaction_code;
}

function get_transaction_meta($transaction_id, $metakey, $return_single_data = true){
    global $wpdb;
    
    $transaction_id = (int) $transaction_id;
    
    $table = WTYPE::DB(WTYPE::DB_META);
    
    $sql = "SELECT * FROM $table WHERE transaction_id=%d AND meta_key=%s";
    
    $results = $wpdb->get_results($wpdb->prepare($sql, $transaction_id, $metakey));
    
    if ($results){
        if (count($results) >= 0){
            foreach($results as $index => $v){
                if (isset($results[$index]->meta_value)){
                $results[$index]->meta_value = maybe_unserialize($results[0]->meta_value);
                }
            }
        }
    }   
    
    if ($return_single_data){
        return $results[0]->meta_value;
    } else {
        
        $metadata = array();
        
        foreach($results as $index=>$v) {
            $metadata[$index] = $results[0]->meta_value;
        }        
        
        return $metadata;
    }
}

function get_transaction_by($transaction_id){
    return get_transaction_meta($transaction_id, MKEY::TRANS_BY);
}

function get_transaction_notes($transaction_id){
    return get_transaction_meta($transaction_id, MKEY::TRANS_NOTES);
}

/** deduct_points()
 *
 * alter points function, decremental update
 *
 * @author  Nuarharuha <nhnoah+bay-isra@gmail.com>
 * @since   1.0.0
 * @version 0.1
 *
 * @param   int     $uid        valid wp user_id
 * @param   float   $points     points to add must be int or float
 * @param   string  $type       type of points to update, default is RM
 *                              MKEY, MKEY::RM, MKEY::PV, MKEY::POINTS
 */
function deduct_points($uid, $points, $type = MKEY::RM) {

    $points = filter_points($points);
    $amount = get_points($uid, $type) - $points;

    return update_points($uid, $amount, $type);
}

/** deduct_points_pv()
 *
 * A shorthand function for deduct_points()
 * Deduct user PV points
 *
 * @author  Nuarharuha <nhnoah+bay-isra@gmail.com>
 * @since   1.0.0
 *
 * @param   int     $uid        valid wp user_id
 */
function deduct_points_pv($uid, $points) { return deduct_points($uid, $points, MKEY::PV); }

/** deduct_points_rm()
 *
 * A shorthand function for deduct_points()
 * Deduct user RM points
 *
 * @author  Nuarharuha <nhnoah+bay-isra@gmail.com>
 * @since   1.0.0
 *
 * @param   int     $uid        valid wp user_id
 */
function deduct_points_rm($uid, $points) { return deduct_points($uid, $points); }