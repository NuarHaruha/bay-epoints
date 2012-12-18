<?php
/**
 * mc_wallet_install()
 *
 * Installation scripts
 *
 * Setup our database, this function should be
 * run once on plugin active
 *
 * @global mixed|object $wpdb object of WP database
 * @see Wpdb::get_var() {@link http://codex.wordpress.org/Class_Reference/wpdb#SELECT_a_Variable SELECT a Variable}
 * @see Wpdb::query() {@link http://codex.wordpress.org/Class_Reference/wpdb#Run_Any_Query_on_the_Database Run Any Query on the Database}
 * @see dbDelta()
 * @see add_option()
 * @see WTYPE
 * @see MKEY
 *
 * @author  Nuarharuha <nhnoah+bay-isra@gmail.com>
 * @filesource https://github.com/NuarHaruha/bay-epoints/blob/master/libs/install.php
 * @since   1.0
 * @version 1.1
 * @return  mixed|array
 */
function mc_wallet_install(){
    global $wpdb;

    $update = array();

    $db = $primary_db = WTYPE::DB(WTYPE::DB_PRIMARY);

    if($wpdb->get_var("SHOW TABLES LIKE '".$db."'") != $db || WTYPE::VERSION() < WTYPE::DB_VERSION )
    {
        require_once PATHTYPE::F_UPGRADE();

		$sql = "CREATE TABLE " . $db . " (
			  transaction_id BIGINT(20) unsigned NOT NULL AUTO_INCREMENT,
			  uid BIGINT(20) unsigned NOT NULL,
			  currency TINYINT(2) NOT NULL,
			  transaction TINYINT(4) NOT NULL,
			  points BIGINT(20) NOT NULL,
			  date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  PRIMARY KEY transaction_id (transaction_id),
              KEY uid (uid),
              KEY currency (currency),
              KEY transaction (transaction),
              KEY date (date)
			) ENGINE=INNODB;";
        $update[] = dbDelta($sql);

        $user_table = $wpdb->users;

        $sql = "ALTER TABLE $db 
                ADD FOREIGN KEY (uid) REFERENCES $user_table(ID)
                      ON DELETE CASCADE;";

        $wpdb->query($sql);

        /**
         * e-Wallet meta table
         */
   
        $db = WTYPE::DB(WTYPE::DB_META);
       
        if($wpdb->get_var("SHOW TABLES LIKE '".$db."'") != $db){
    		$sql = "CREATE TABLE " . $db . " (
    			  id BIGINT(20) unsigned NOT NULL AUTO_INCREMENT,
    			  transaction_id BIGINT(20) unsigned NOT NULL,
                  meta_key VARCHAR(255) DEFAULT NULL,
                  meta_value LONGTEXT,
                  PRIMARY KEY (id),
                  KEY transaction_id (transaction_id),
                  KEY meta_key (meta_key)
    			) ENGINE=INNODB;";
            dbDelta($sql);            
        }
        
        $sql = "ALTER TABLE $db 
                ADD FOREIGN KEY (transaction_id) REFERENCES $primary_db(transaction_id)
                      ON DELETE CASCADE;";
        $wpdb->query($sql);

        /**
         * store default value for
         * e-wallet default value for currency,
         * withdrawal & transfer amount, & db version numbers.
         */
        foreach(array(
                    MKEY::CURRENCY              => 'RM',
                    MKEY::MIN_WITHDRAWAL        => 100,
                    MKEY::MIN_TRANSFER          => 100,
                    MKEY::OP_EWALLET_DB_VERSION => WTYPE::DB_VERSION
                ) as $meta_key => $meta_value )
        {
            add_option($meta_key, $meta_value);
        }
	}    
}
/** mc_wallet_install() */