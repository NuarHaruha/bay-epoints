<?php
/**
 * Metabox widgets
 *
 * @package     isralife
 * @category    points
 *
 * @author      Nuarharuha <nhnoah+bay-isra@gmail.com>
 * @copyright   Copyright (C) 2012, Nuarharuha, MDAG Consultancy
 * @license     http://mdag.mit-license.org/ MIT License
 * @filesource  https://github.com/NuarHaruha/bay-epoints/blob/master/libs/metabox.php
 * @version     1.0
 * @access      public
 */

/*---------------------------------------------------------------
 * Deposit metabox
 */

/**
 * deposit form
 * @params  mixed|object    $posts      will not be use, just a placeholder
 * @params  mixed|array     $options    array of arguments
 * @return void     show deposit form
 */
function mb_ew_deposit($posts, $options){
    $action = ($_REQUEST['panel'] == 'penalty') ? 'penalty' : 'deposit';
?>
<table class="widefat">
    <tbody>
    <tr valign="top">
        <th scope="row">
            <label for="user_name">Member Name</label>
        </th>
        <td>
            <input id="user_name" name="user_name" value="" type="text" class="regular-text width-85 code"/>
        </td>
        <th scope="row">
            <label for="deposit_amount">Amount</label>
        </th>
        <td>
            <select id="transaction_type" name="transaction_type" style="float:left">
                <option value="<?php echo WTYPE::DEPOSIT_RM; ?>">RM</option>
                <option value="<?php echo WTYPE::DEPOSIT_PV; ?>">PV</option>
            </select>
            <input id="deposit_amount" name="deposit_amount" value="" type="text" class="regular-text width-85 code"/>
            <small class="description db">
                Amount to deposit. Minimum Transfer: RM <?php has_option(MKEY::MIN_TRANSFER); ?>
            </small>
            <input type="hidden" id="dataresult" name="user_id" value="">
        </td>
    </tr>
    <tr valign="top">
        <th scope="row">
            <label for="user_code">Member ID</label>
        </th>
        <td>
            <input id="user_code" name="user_code" value="" type="text" class="regular-text width-85 code"/>
        </td>
        <th scope="row"><label for="transaction_note">Reason</label></th>
        <td>
            <textarea id="transaction_note" name="transaction_note" class="regular-text code width-85" rows="2"></textarea>
            <small class="description db">Please provide a reason for this transaction.</small>
        </td>
    </tr>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="4">
                    <input type="submit" name="section_<?php echo $action; ?>" class="button-primary" value="Send Deposit">
            </th>
        </tr>
    </tfoot>
</table>
<?php
}

/*---------------------------------------------------------------
 * Receipt metabox
 */

function mb_ew_receipt($posts, $options){

    list($req, $trans) = $options['args'];

    unset($posts, $options);
?>
<table class="widefat nobot">
    <!--<thead>
        <tr>
            <td colspan="">Transaction confirmation, please retain for your records</td>
        </tr>
    </thead> -->
    <tbody>
        <tr>
            <th scope="row" style="width: 20%">
               <label for="receipt_id">Transaction ID:</label>
            </th>
            <td style="width: 30%">
                <input type="text" id="receipt_id" value="<?php ew_format_receipt($req);?>" class="width-85 code disabled">
            </td>
            <th scope="row" style="width: 20%">
                <label for="tdate">Transaction Date:</label>
            </th>
            <td style="width: 30%">
                <input id="tdate" class="width-85 code disabled" value="<?php echo $trans->date;?>">
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="transfer_code">
                     Transaction type
                </label>
            </th>
            <td>
                <input id="transfer_code" class="width-85 code disabled" value="<?php echo $trans->transaction.' - '.strtoupper(tr_code($trans->transaction)); ?>">
            </td>
            <th scope="row">
                <label for="currency">Amount:</label>
            </th>
            <td>
                <input id="currency" class="width-85 code disabled" value="<?php echo tr_currency($trans->currency) . ' ' . $trans->points ;?>">
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="transfer_code_name">Recipient Name:</label>
            </th>
            <td style="width: 30%">
                <input id="transfer_code_name" class="width-85 code disabled" value="<?php echo strtoupper(uinfo($trans->uid));?>">
            </td>
            <th scope="row" style="width: 20%">
                <label for="member_id">Recipient ID:</label>
            </th>
            <td style="width: 30%">
                <input id="member_id" class="width-85 code disabled" value="<?php echo uinfo($trans->uid,'code')?> ">
            </td>
        </tr>
    </tbody>
 </table>
<?php
}

function mb_ew_receipt_actions($posts, $options){
    list($req, $trans) = $options['args'];
    unset($posts, $options);
?>
<table class="widefat nobot">
    <tbody>
    <tr>
        <th scope="row">
            <label for="transfer_by">
                Approved by
            </label>
        </th>
        <td>
            <input id="transfer_by" class="width-85 code disabled" value="<?php echo strtoupper(ew_approved_by($trans->transaction_id)); ?>">
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label for="approved_role">
                Role
            </label>
        </th>
        <td>
            <select id="approved_role" class="disabled">
<?php

            $user = new WP_User( ew_approved_by($trans->transaction_id, true) );

            if ( !empty( $user->roles ) && is_array( $user->roles ) ) {
                foreach ( $user->roles as $role )
                    echo _t('option', ucfirst($role) );
            }
?>
            </select>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label for="transfer_note">Transfer Note</label>
        </th>
        <td>
            <input id="transfer_note" class="width-85 code disabled" value="<?php echo ew_note($trans->transaction_id); ?>">
        </td>
    </tr>
    </tbody>
    <tfoot>
    <tr>
        <th colspan="4">
            <button class="button-secondary go-print">Print Receipt</button>
            <button class="button-secondary go-back">Back</button>
            <!--
            <button class="button-secondary">Cancel Transaction</button>
            <button class="button-secondary">View List</button>
            -->
        </th>
    </tr>
    </tfoot>
</table>
<script>
    jQuery(document).ready(function($){
        $('.go-print').click(function(e){
            e.preventDefault();
            $('#opt_ew_receipt').jqprint();
        });
    });
</script>
<?php
}

/*---------------------------------------------------------------
 * Settings metabox
 */

function mc_ew_general_settings($posts, $options){
    ?>
<table class="form-table widefat">
    <tr valign="top">
        <th scope="row">
            <label for="<?php echo MKEY::CURRENCY;?>">Global Currency</label>
        </th>
        <td>
            <input id="<?php echo MKEY::CURRENCY;?>" name="<?php echo MKEY::CURRENCY;?>" value="<?php has_option(MKEY::CURRENCY); ?>" type="text" class="regular-text"/>
            <p class="description">Global currency format.</p>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row">
            <label for="<?php echo MKEY::MIN_WITHDRAWAL;?>">Minimum withdrawal</label>
        </th>
        <td>
            <input id="<?php echo MKEY::MIN_WITHDRAWAL;?>" name="<?php echo MKEY::MIN_WITHDRAWAL;?>" value="<?php has_option(MKEY::MIN_WITHDRAWAL); ?>" type="text" class="regular-text"/>
            <p class="description">Minimum amount (i.e <?php echo get_currency(); ?> 100.00, <?php echo get_currency(); ?> 50.00).</p>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row">
            <label for="<?php echo MKEY::MIN_TRANSFER;?>">Minimum transfer</label>
        </th>
        <td>
            <input id="<?php echo MKEY::MIN_TRANSFER;?>" name="<?php echo MKEY::MIN_TRANSFER;?>" value="<?php has_option(MKEY::MIN_TRANSFER); ?>" type="text" class="regular-text"/>
        </td>
    </tr>
    <tfoot>
        <tr>
            <th colspan="2">
                <input type="submit" class="button-primary" value="Save Changes">
            </th>
        </tr>
    </tfoot>
</table>
<?php

}

/*---------------------------------------------------------------
 *  Bank metabox
 */
function mc_ew_bank($posts, $options){
?>
<table class="widefat nobot">
    <tbody>
    <tr valign="top">
        <th scope="row" style="width:20%">
            <label for="bank_account_name">Account Name</label>
        </th>
        <td style="width:30%">
            <input type="text" id="bank_account_name" name="bank_account_name" value="" class="regular-text code">
        </td>
        <th scope="row" style="width:20%">
            <label for="bank_name">Bank Name</label>
        </th>
        <td style="width:30%">
            <input type="text" id="bank_name" name="bank_name" value="" class="regular-text code">
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" style="width:20%">
            <label for="bank_account_no">Account No. #</label>
        </th>
        <td style="width:30%">
            <input type="text" id="bank_account_no" name="bank_account_no" value="" class="regular-text code">
        </td>
        <th scope="row" style="width:20%">
            <label for="bank_branch">Branch</label>
        </th>
        <td style="width:30%">
            <input type="text" id="bank_branch" name="bank_branch" value="" class="regular-text code">
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" style="width:20%">
            <label for="bank_account_type">Account Type</label>
        </th>
        <td style="width:30%">
            <input type="text" id="bank_account_type" name="bank_account_type" value="" class="regular-text code">
        </td>
        <td colspan="2"></td>
    </tr>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="4" scope="row">
                <button class="button-secondary go-back">Back</button>
                <button class="button-primary" type="submit">Add Bank</button>
            </th>
        </tr>
    </tfoot>
</table>
<?php
}

function mc_ew_bank_list($posts, $options){
    $bank_list = new mc_ew_bank_table();
    $bank_list->prepare_items();
    $bank_list->display();
}