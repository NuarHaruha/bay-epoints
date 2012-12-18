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

    public function __construct()
    {
        $this->_init();
    }

    private function _init()
    {
        $this->plugin_path  = plugin_dir_path(__FILE__);
        $this->libs_path    = $this->plugin_path.'libs/';

        $this->_loadDefaultFileSystem();
    }

    private function _loadDefaultFileSystem()
    {
        $libs = array(
            'install',      // setup db table scripts
        );

        foreach($libs as $slug){
            require $this->libs_path . sprintf('%s.php',$slug);
        }
    }

}

new mc_ewallet();

/**
 * Register WP Activation hook
 */
register_activation_hook( __FILE__ , 'mc_ewallet_setup');

/**
 * setup database table
 * run once on plugin activated
 * @return void
 */
function mc_ewallet_setup(){
    mc_wallet_install();
}