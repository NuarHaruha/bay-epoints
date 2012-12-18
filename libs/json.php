<?php
/**
 *
 *  json data functions
 *
 * @author  Nuarharuha <nhnoah+bay-isra@gmail.com>
 * @filesource https://github.com/NuarHaruha/bay-epoints/blob/master/libs/actions.php
 * @since   1.0
 */

/**
 *  auto-complete user display name & members code
 */
function ew_suggest(){

    if (!isset($_REQUEST['action'])){
        die();
    }

    global $wpdb;

    $q      = '^'.$_REQUEST['q'];

    switch ($_REQUEST['action']){
        case 'suggest-name':
            $db     = $wpdb->users;
            $sql    = "SELECT UPPER(display_name) data FROM $db WHERE display_name REGEXP %s";
            break;
        case 'suggest-code':
            $db     = $wpdb->usermeta;
            $sql    = "SELECT UPPER(meta_value) data FROM $db WHERE meta_key='account_id' AND meta_value REGEXP %s";
            break;
    }

    $users  = $wpdb->get_results($wpdb->prepare($sql, $q));

    if ($users){
        foreach($users as $index => $user){
            echo $user->data."\n";
        }
    }
    die();
}

function json_get_suggest(){

    $output = false;
    $json   = array();

    if (isset($_REQUEST['code']) && $_REQUEST['json'] == 2){

        $code  = $_REQUEST['code'];
        $users = get_users(array('meta_value'=> $code,'meta_key'=>'account_id'));

        $output = $users[0]->ID;

        $json = array('id'=> (int) $output, 'name' => uinfo($output,'name'));
    }

    if (isset($_REQUEST['name']) && $_REQUEST['json'] == 1){
        global $wpdb;

        $name = $_REQUEST['name'];

        $output = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->users WHERE display_name=%s",$name));

        $json = array('id'=> (int) $output, 'code' => uinfo($output,'code'));
    }

    if(isset($_REQUEST['json'])){
        $json['rm'] = (float) get_points_rm($json['id']);
        echo  json_encode($json);
    } else {
        echo $output;
    }

    die();
}