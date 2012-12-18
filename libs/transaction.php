<?php
/**
 * @package     isralife
 * @category    points
 *
 * @author      Nuarharuha <nhnoah+bay-isra@gmail.com>
 * @copyright   Copyright (C) 2012, Nuarharuha <nhnoah+bay-isra@gmail.com>, MDAG Consultancy
 * @license     http://mdag.mit-license.org/ MIT License
 * @filesource  https://github.com/NuarHaruha/bay-epoints/blob/master/libs/transaction.php
 * @version     0.1
 * @access      public
 */
class eWalletTransaction
{
    /**
     *  Version numbers
     *
     * @access  public
     * @var     string
     */
    public $version     = '0.1';

    /**
     *  Valid user ID
     *
     * @access  public
     * @var     int
     */
    public $uid;

    /**
     *  Points to store
     *
     * @access  public
     * @var     float
     */
    public $points      = 0;

    /**
     *  Log Transaction type
     *
     * @see     DTYPE
     * @access  public
     * @var     mixed   object class of WTYPE
     */
    public $code;

    /**
     *  Points Currency Type
     *
     * @see     DTYPE
     * @access  public
     * @var     mixed  object class of WTYPE
     */
    public $type;

    /**
     *  DB Insert ID
     *
     * @see     wpdb::$insert_id
     * @access  public
     * @var     int
     */
    public $insert_id   = false ;

    /**
     * eWalletTransaction::__construct()
     *
     * Constructor, the actual setting up of the class properties
     *
     * @author  Nuarharuha <nhnoah+bay-isra@gmail.com>
     * @since   0.1
     * @access  public
     *
     * @param   string      $uid        valid WP user ID
     * @param   int|float   $points     integer, amount for the transaction
     * @param   object      $code       object of WTYPE
     * @param   object      $type       object of WTYPE
     */
    public function __construct($uid, $points, $code = WTYPE::DEPOSIT_RM, $type = WTYPE::RM)
    {
        $this->uid      = (int) $uid;
        $this->points   = (float) $points;
        $this->code     = (int) $code;
        $this->type     = (int) $type;

        if ($this->_valid_id()) $this->_init();
    }
    /** eWalletTransaction::__construct() */

    /**
     * Destructor and will run when object is destroyed.
     *
     * @see    eWalletTransaction::__construct()
     * @since  0.1
     * @return bool        true
     */
    public function __destruct()
    {
        return true;
    }
    /** eWalletTransaction::__destruct() */


    /**
     * eWalletTransaction::_init()
     *
     * register global hooks & filters
     * define base configuration settings
     *
     * @author  Nuarharuha <nhnoah+bay-isra@gmail.com>
     * @since   0.1
     * @access  private
     */
    private function _init()
    {
        $this->_log_transaction();
    }
    /** eWalletTransaction::_init() */


    /**
     * eWalletTransaction::_log_transaction()
     *
     * save transaction
     *
     * @author  Nuarharuha <nhnoah+bay-isra@gmail.com>
     * @since   0.1
     * @access  private
     *
     * @uses    $wpdb WordPress database object for queries.
     * @return  void
     */
    private function _log_transaction()
    {   global $wpdb;

        $table = WTYPE::DB(WTYPE::DB_PRIMARY);

        $transaction = array(
            'uid'               => $this->uid,
            'currency'          => $this->type,
            'transaction'       => $this->code,
            'points'            => $this->points
        );

        $transaction = apply_filters('before_points_transaction', $transaction);

        $results = $wpdb->insert($table, $transaction, array('%d','%d','%d','%f'));

        if ($results){
            $this->insert_id = $wpdb->insert_id;
            do_action('after_transaction_log', $this->insert_id, $transaction);
        }

    }
    /** eWalletTransaction::_log_transaction() */

    /**
     * eWalletTransaction::_valid_id()
     *
     * check if user ID is valid
     *
     * @author  Nuarharuha <nhnoah+bay-isra@gmail.com>
     * @since   0.1
     * @access  private
     */
    private function _valid_id()
    {
        $user = get_userdata($this->uid);

        return ($user->user_login !='' && !empty($user->user_login) );
    }
    /** eWalletTransaction::_valid_id() */


    /** eWalletTransaction::add_meta()
     *
     *  Static method
     *  Add metadata for transaction log.
     *
     * @uses    $wpdb WordPress database object for queries.
     *
     * @author  Nuarharuha <nhnoah+bay-isra@gmail.com>
     * @since   0.1
     * @access  public
     *
     * @param   int       $transaction_id     ID of the object metadata is for
     * @param   string    $meta_key           Metadata key
     * @param   string    $meta_value         Metadata value     *
     * @return  bool                         The meta ID on successful update, false on failure.
     *
     */
    public static function add_meta($transaction_id, $meta_key, $meta_value)
    {   global $wpdb;

        return $wpdb->insert( WTYPE::DB(WTYPE::DB_META),
            array(
                'transaction_id'    => (int) $transaction_id,
                'meta_key'          => $meta_key,
                'meta_value'        => maybe_serialize($meta_value)
            )
        );
    }
    /** eWalletTransaction::add_meta() */


    /** eWalletTransaction::update_meta()
     *
     *  Static method
     *  Update metadata for transaction log.
     *
     * @uses    $wpdb WordPress database object for queries.
     *
     * @author  Nuarharuha <nhnoah+bay-isra@gmail.com>
     * @since   0.1
     * @access  public
     *
     * @param   int       $id                 valid Id of the object metadata is for
     * @param   int       $transaction_id     transaction Id of the object metadata is for
     * @param   string    $meta_key           Metadata key
     * @param   string    $meta_value         Metadata value
     * @return  bool                          True on successful update, false on failure.
     *
     */
    public static function update_meta($id, $transaction_id, $meta_key, $meta_value)
    {   global $wpdb;

        $table = WTYPE::DB(WTYPE::DB_META);
        $data  = array($meta_key => maybe_serialize($meta_value));
        $where = array('id'=> $id, 'transaction_id' => $transaction_id);

        return $wpdb->update( $table, $data, $where, null, '%d');
    }
    /** eWalletTransaction::update_meta() */


    /** eWalletTransaction::delete_meta()
     *
     *  Static method
     *  Delete metadata for the specified transaction log.
     *
     * @uses    $wpdb WordPress database object for queries.
     *
     * @author  Nuarharuha <nhnoah+bay-isra@gmail.com>
     * @since   0.1
     * @access  public
     *
     * @param   int       $id                Valid Id of the object metadata is for
     * @return  bool                         True on successful delete, false on failure.
     *
     */
    public static function delete_meta($id)
    {   global $wpdb;

        $table  = WTYPE::DB(WTYPE::DB_META);
        return $wpdb->query($wpdb->prepare("DELETE FROM $table WHERE id = %d", $id));
    }
    /** eWalletTransaction::delete_meta() */
}