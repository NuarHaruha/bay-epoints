<?php
/*
Plugin Name: MDAG e-Wallet
Plugin URI: http://mdag.my
Description: Wallet & Points System
Version: 1.0.0
Author: Nuar, MDAG Consultancy
Author URI: http://mdag.my
License: MIT License
License URI: http://mdag.mit-license.org/
*/

setlocale(LC_ALL,'ms_MY');

class mc_ewallet
{
    protected $version          = 1.0;

    public $plugin_path;

    public $libs_path;

    /**
     * base plugin url
     * @var mixed|array
     */
    public $uri                 = array();

    /**
     * plugin admin menu screen name slug key
     * @var mixed|array
     */
    public $page                = array();

    /**
     * Primary page slug
     * @var string
     */
    public $slug                = 'mc-ew';

    public function __construct()
    {
        $this->_init();

        if (is_admin()){
            $this->_initAdmin();
        }
    }

    private function _init()
    {
        $this->plugin_path  = plugin_dir_path(__FILE__);
        $this->libs_path    = $this->plugin_path.'libs/';

        $this->uri['base']  = plugin_dir_url(__FILE__);
        $this->uri['pub']   = $this->uri['base'] . 'public/';
        $this->uri['img']   = $this->uri['pub'] . 'img/';
        $this->uri['css']   = $this->uri['pub'] . 'css/';
        $this->uri['js']    = $this->uri['pub'] . 'js/';

        $this->_loadDefaultFileSystem();
    }

    /**
     *  include all require files
     */
    private function _loadDefaultFileSystem()
    {
        $libs = array(
            'install',      // setup db table scripts
            'points',       // points
            'general',      // general functions, util
            'metabox',      // metabox widgets
            'actions',      // actions hook
            'json',         // json data & ajax function
            'transaction',  // deposit & logger
            'wallet'        // wallet transaction functions
        );

        foreach($libs as $slug){
            require $this->libs_path . sprintf('%s.php',$slug);
        }
    }

    /**
     * register WP Admin init action
     */
    private function _initAdmin()
    {
        add_action('admin_init', array($this, 'registerAdminStylesheets'));
        add_action('admin_init', array($this, 'registerAdminScripts'));
        add_action('admin_menu', array($this, 'registerAdminMenus'));
        add_action('add_meta_boxes', array(&$this,'registerAdminMetabox'));
    }

    /**
     * register admin stylesheets
     */
    public function registerAdminStyleSheets()
    {
        wp_register_style('ewallet', $this->uri['css']. 'style.css', array('font-awesome') );
    }

    /**
     * register admin scripts
     */
    public function registerAdminScripts()
    {
        wp_register_script('ewallet-suggest', $this->uri['js']. 'suggest.js', array('jquery','suggest'), false, true );

        // ajax scripts
        add_action('wp_ajax_suggest-name', 'ew_suggest');
        add_action('wp_ajax_suggest-code', 'ew_suggest');
        add_action('wp_ajax_suggest-get', 'json_get_suggest');
    }

    /**
     * register plugin admin menus
     */
    public function registerAdminMenus()
    {
        $title      = 'e-Wallet';
        $callback   = array($this, 'loadPanel');
        $icon       = $this->uri['img'] . 'ewallet-16.png';
        $pos        = 10.2;
        $this->page['primary'] = add_menu_page($title, $title, WTYPE::MANAGER_CAP, $this->slug, $callback, $icon, $pos);

        $this->_triggerDefaultPageAction($this->page['primary']);

        /**
         * Settings page
         */

        $title      = 'Settings';
        $slug       = $this->slug.'-settings';

        $this->page['settings'] = add_submenu_page($this->slug, $title, $title, WTYPE::MANAGER_CAP, $slug, $callback);

        $this->_triggerDefaultPageAction($this->page['settings']);
    }

    /**
     *  register admin metabox
     */
    public function registerAdminMetabox()
    {
        $req = _obj($_REQUEST);

        if (isset($req->receipt)){
                $this->_registerReceiptMetabox();
        }

        if (isset($req->panel)){
            switch($req->panel){
                case 'deposit':
                        $this->_registerDepositMetabox();
                    break;
                case 'penalty':
                        $this->_registerPenaltyMetabox();
                    break;
                case 'list':
                    default:
                        $this->_registerListMetabox();
                    break;
            }
        } else {

            switch ($req->page){
                case $this->slug:
                    $this->_registerListMetabox();
                    break;
                case $this->slug.'-settings':
                    $this->_registerSettingsMetabox();
                    break;
            }
        }
    }

    private function _registerSettingsMetabox()
    {
        $args = array();

        add_meta_box('opt_ew_settings','General Settings', 'mc_ewallet_general_settings',
            $this->page['settings'],'normal','high', $args);
    }

    private function _registerReceiptMetabox()
    {
        $req            = _obj($_REQUEST);
        $transaction    = get_ew_transaction($req->tid);

        $args = array(
            $req,               // 0. global request array as object
            $transaction[0]     // 1. transaction records object
        );

        add_meta_box('opt_ew_receipt','Transaction Details', 'mb_ew_receipt',
            $this->page['primary'],'normal','high', $args);

        add_meta_box('opt_ew_receipt_actions','Transaction Log', 'mb_ew_receipt_actions',
            $this->page['primary'],'side','high', $args);
    }

    private function _registerListMetabox()
    {}

    private function _registerDepositMetabox()
    {
        $args = array();

        add_meta_box('opt_ew_deposit','Deposit to e-Wallet', 'mb_ew_deposit',
            $this->page['primary'],'normal','high', $args);
    }

    private function _registerPenaltyMetabox()
    {}
    /**
     * load admin page
     */
    public function loadPanel()
    {
        $path = $this->plugin_path . 'panels/%s.php';
        $file = false;

        switch($_REQUEST['page']){
            case $this->slug.'-settings': $file = 'settings'; break;
            case $this->slug:
            default:
                $file = (!isset($_REQUEST['receipt'])) ? 'main' : 'receipt';
                break;
        }

        if ($file){
            require_once sprintf($path, $file);
        }
    }

    /**
     * set admin page stylesheet, scripts & metabox
     *
     * @author  Nuarharuha <nhnoah+bay-isra@gmail.com>
     * @since   1.0.0
     * @access  private
     */
    private function _triggerDefaultPageAction($hook)
    {
        add_action('admin_print_styles-'.$hook, array($this,'printStyleSheets') );
        add_action('admin_footer-'.$hook, array($this,'printAdminFooterScripts'));

        add_action('load-'.$hook, array($this,'pageActions'),9);
        add_action('load-'.$hook, array($this,'saveSettings'),10);
    }

    /**
     * enqueue plugin stylesheets on admin_head
     *
     * @author  Nuarharuha <nhnoah+bay-isra@gmail.com>
     * @since   1.0.0
     * @access  public
     */
    public function printStyleSheets()
    {
        wp_enqueue_style('ewallet');
    }

    public function printScripts()
    {}

    /**
     * print footer script on  plugin page
     *
     * @author  Nuarharuha <nhnoah+bay-isra@gmail.com>
     * @since   1.0.0
     * @access  public
     */
    public function printAdminFooterScripts()
    {
        t('script','postboxes.add_postbox_toggles(pagenow); var plugin_uri = "'.$this->uri['base'].'";');
    }

    /**
     * Actions to be taken prior to page loading. This is after headers have been set.
     * call on load-$hook
     * This calls the add_meta_boxes hooks, adds screen options and enqueues the postbox.js script.
     *
     * @author  Nuarharuha <nhnoah+bay-isra@gmail.com>
     * @since   1.0.0
     * @access  public
     */
    public function pageActions()
    {
        $req = _obj($_REQUEST);

        $page   = 'mc-ew_page_'.$req->page;

        do_action('add_meta_boxes_'.$page, null);
        do_action('add_meta_boxes', $page, null);

        add_screen_option('layout_columns', array('max' => 2, 'default' => 2) );

        wp_enqueue_script('jquery');
        wp_enqueue_script('postbox');

        /**
         *  auto-complete scripts
         */
        if (isset($req->panel) && $req->panel == 'deposit'){
            wp_enqueue_script('ewallet-suggest');
        }

        /**
         *  jqPrint, assumed script already registered
         * @see mc_epin
         */
        if (isset($req->receipt)){
            wp_enqueue_script('jqprint');
        }
    }

    /**
     * save all settings method
     *
     * @author  Nuarharuha <nhnoah+bay-isra@gmail.com>
     * @since   1.0.0
     * @access  public
     */
    public function saveSettings()
    {
        $req = _obj($_REQUEST);

        if (isset($req->action)){

            switch($req->page)
            {
                case $this->slug . '-settings':
                    if ($req->action == 'mc-wallet-settings'){
                        $this->_saveGeneralOptions();
                    }
                break;
                case $this->slug:
                    default:
                    /** tab section, within main page */
                        switch ($req->action):
                            case 'mc-wallet-penalty':
                                break;
                            case 'mc-wallet-deposit':
                                $this->_saveDeposit();
                                break;
                            case 'mc-wallet-list':
                            default:
                                break;
                        endswitch;
                    break;
            }
        }
    }

    /**
     *  save general options
     */
    private function _saveGeneralOptions()
    {
        $metakeys = array(MKEY::CURRENCY, MKEY::MIN_WITHDRAWAL, MKEY::MIN_TRANSFER);

        foreach($metakeys as $key){

            if (isset($_REQUEST[$key]))
            {
                $value = apply_filters('before_save_'.$key, $_REQUEST[$key]);
                update_option($key, $value);
            }
        }
    }

    /**
     * save deposit
     */
    private function _saveDeposit()
    {
        $req = _obj($_REQUEST);

        if (wp_verify_nonce($req->_wpnonce, WTYPE::NONCE_WALLET) )
        {
            if (isset($req->section_deposit))
            {
                $user       = new stdClass();
                $meta_keys  = array(
                    'user_name',
                    'user_code',
                    'user_id',
                    'deposit_amount',
                    'timestamp',
                    'transaction_type',
                    'transaction_note'
                );

                foreach($meta_keys as $k)
                {
                    if (isset($_REQUEST[$k]) && !empty($_REQUEST[$k]))
                    {
                        $value = $_REQUEST[$k];
                        switch($k){
                            case 'user_id': $value = (int) $value; break;
                            case 'deposit_amount':  $value = floatval($value); break;
                        }
                        $user->$k = $value;
                    }
                }

                if (isset($user->transaction_type)){
                    switch ($user->transaction_type){
                        case WTYPE::DEPOSIT_RM:
                            deposit_points_rm($user->user_id, $user->deposit_amount);
                            $this->_log($user->user_id, $user->deposit_amount);
                            break;
                        case WTYPE::DEPOSIT_PV:
                            deposit_points_pv($user->user_id, $user->deposit_amount);
                            $this->_log($user->user_id, $user->deposit_amount, WTYPE::DEPOSIT_PV, WTYPE::PV);
                            break;
                    }
                }

            }
        }
    }

    /**
     * log transaction
     * @see eWalletTransaction
     */
    private function _log($uid, $points, $transaction = WTYPE::DEPOSIT_RM, $currency = WTYPE::RM)
    {
        new eWalletTransaction($uid, $points, $transaction, $currency);
    }
}

new mc_ewallet();

/**
 * Register WP Activation hook
 */
register_activation_hook( __FILE__ , 'mc_ewallet_setup');

/**
 * 1. Register custom role & capability
 * 2. Setup database table
 *
 * run once on plugin activated
 * @return void
 */
function mc_ewallet_setup(){
    mc_wallet_register_role_cap();
    mc_wallet_install();
}