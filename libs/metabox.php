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
                    <input type="submit" name="section_deposit" class="button-primary" value="Send Deposit">
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