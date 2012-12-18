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
