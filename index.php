<?php
    /*
    Plugin Name: Advanced DB Cleaner MK
    Plugin URI: /advanced-db-cleaner-mk
    Description: Easy and advanced WordPress database cleaning
    Version: 1.0
    Author: Adem Mert Kocakaya
    Author URI: https://mertkocakaya.com
    License: GNU
    */
    
    add_action('admin_menu', 'advanced_db_cleaner_mk');
    
    function advanced_db_cleaner_mk() {
        
        add_menu_page('Advanced DB Cleaner MK', 'DB Cleaner', 'manage_options', 'advanced-db-cleaner-mk', 'advanced_db_cleaner_mk_function', plugin_dir_url(__FILE__) .'assets/img/advanced-db-cleaner-mk-icon.png');
        
    }
    
    function advanced_db_cleaner_mk_function() {
        
       wp_enqueue_style('advanced_db_cleaner_mk_custom_styles', plugins_url( 'assets/css/style.css', __FILE__ ), '', '1.0');
           
	   wp_enqueue_style('bootstrap_styles', plugins_url( 'assets/css/bootstrap.min.css', __FILE__ ), '', '1.0'); 

    if (!defined('ABSPATH')) exit;
        
    if (current_user_can('upload_files')) {

        global $wpdb;
        
        $mk_db_post_size_check = $wpdb->get_var("SELECT data_free FROM INFORMATION_SCHEMA.TABLES WHERE table_name = '{$wpdb->prefix}posts'");
        $mk_db_broken_tables_size_check = $wpdb->get_var("SELECT data_free FROM INFORMATION_SCHEMA.TABLES WHERE table_name = '{$wpdb->prefix}term_relationships'");
        $mk_db_spam_comments_size_check = $wpdb->get_var("SELECT data_free FROM INFORMATION_SCHEMA.TABLES WHERE table_name = '{$wpdb->prefix}commentmeta'");
        $mk_db_postmeta_trash_size_check = $wpdb->get_var("SELECT data_free FROM INFORMATION_SCHEMA.TABLES WHERE table_name = '{$wpdb->prefix}postmeta'");
        $mk_db_options_size_check = $wpdb->get_var("SELECT data_free FROM INFORMATION_SCHEMA.TABLES WHERE table_name = '{$wpdb->prefix}options'");
        
        if($_POST) {
        
        $retrieved_nonce = $_REQUEST['_wpnonce'];
        
        if (!wp_verify_nonce($retrieved_nonce, 'mk_pm_nonce_action' )){ 
                
                die( 'Failed security check' );}
                
        else {
        
            if(isset($_POST['mk_revisions_clean'])) {
            
           $islem1 =  $wpdb->query("DELETE FROM {$wpdb->prefix}posts WHERE post_type='revision'");
           $islem2 = $wpdb->query("OPTIMIZE TABLE `{$wpdb->prefix}posts`");
           echo '<style>#mk_revisions_clean_small {display:none;} #mk_revisions_clean_alert {display:block!important;}</style>';
            
        }
            
            if(isset($_POST['mk_draft_posts_clean'])) {
            
            $wpdb->query("DELETE FROM {$wpdb->prefix}posts WHERE post_status = 'draft'");
            $wpdb->query("OPTIMIZE TABLE `{$wpdb->prefix}posts`");
            echo '<style>#mk_draft_posts_clean_small {display:none;} #mk_draft_posts_clean_alert {display:block!important;}</style>';
            
        }
            
            if(isset($_POST['mk_trash_posts_clean'])) {
            
            $wpdb->query("DELETE FROM {$wpdb->prefix}posts WHERE post_status = 'trash'");
            $wpdb->query("OPTIMIZE TABLE `{$wpdb->prefix}posts`");
            echo '<style>#mk_trash_posts_clean_small {display:none;} #mk_trash_posts_clean_alert {display:block!important;}</style>';
            
        }
            
            if(isset($_POST['mk_broken_tables_clean'])) {
            
            $wpdb->query("DELETE FROM {$wpdb->prefix}term_relationships WHERE NOT EXISTS ( SELECT * FROM wp_posts WHERE {$wpdb->prefix}term_relationships.object_id = {$wpdb->prefix}posts.ID)");
            $wpdb->query("OPTIMIZE TABLE `{$wpdb->prefix}term_relationships`");
             echo '<style>#mk_broken_tables_clean_small {display:none;} #mk_broken_tables_clean_alert {display:block!important;}</style>';
            
        }
            
            if(isset($_POST['mk_spam_comments_clean'])) {
            
            $wpdb->query("DELETE FROM {$wpdb->prefix}commentmeta WHERE comment_id NOT IN ( SELECT comment_id FROM wp_comments )");
            $wpdb->query("OPTIMIZE TABLE `{$wpdb->prefix}commentmeta`");
            echo '<style>#mk_spam_comments_clean_small {display:none;} #mk_spam_comments_clean_alert {display:block!important;}</style>';
            
        }
            
            if(isset($_POST['mk_postmeta_trash_clean'])) {
            
            $wpdb->query("DELETE {$wpdb->prefix}postmeta FROM wp_postmeta LEFT JOIN wp_posts ON ({$wpdb->prefix}postmeta.post_id = {$wpdb->prefix}posts.ID) WHERE ({$wpdb->prefix}posts.ID IS NULL)");
            $wpdb->query("DELETE FROM {$wpdb->prefix}postmeta WHERE meta_key IN ('_edit_lock','_edit_last')");
            $wpdb->query("OPTIMIZE TABLE `{$wpdb->prefix}postmeta`");
            echo '<style>#mk_postmeta_trash_clean_small {display:none;} #mk_postmeta_trash_clean_alert {display:block!important;}</style>';
            
        }
            
            if(isset($_POST['mk_transient_options_clean'])) {
            
            $wpdb->query("DELETE FROM {$wpdb->prefix}options WHERE option_name LIKE ('_transient_%')");
            $wpdb->query("DELETE FROM {$wpdb->prefix}options WHERE option_name LIKE ('_transient%_feed_%')");
            $wpdb->query("OPTIMIZE TABLE `{$wpdb->prefix}options`");
            echo '<style>#mk_transient_options_clean_small {display:none;} #mk_transient_options_clean_alert {display:block!important;}</style>';
            
        }
            
            if(isset($_POST['mk_postmeta_table_trash_clean'])) {
            
            $wpdb->query("DELETE {$wpdb->prefix}posts FROM wp_posts LEFT JOIN wp_posts child ON ({$wpdb->prefix}posts.post_parent = child.ID) WHERE ({$wpdb->prefix}posts.post_parent <> 0) AND (child.ID IS NULL)");
            $wpdb->query("DELETE pm FROM {$wpdb->prefix}postmeta pm LEFT JOIN wp_posts wp ON wp.ID = pm.post_id WHERE wp.ID IS NULL");
            $wpdb->query("OPTIMIZE TABLE `{$wpdb->prefix}posts`");
            echo '<style>#mk_postmeta_table_trash_clean_small {display:none;} #mk_postmeta_table_trash_clean_alert {display:block!important;}</style>';
            
        }
        
        }
        
    }

?>

<div class="container-fluid">
	<div class="container py-4">
		<div id="mk_row_area" class="row p-4">
			<div id="mk_logo_area" class="col-12">
			    <img class="w-25" src="<?php echo plugin_dir_url(__FILE__) .'assets/img/advanced-db-cleaner-logo.png'; ?>">
			    <hr>
			</div>
			<div id="mk_plugin_content" class="col-12">
			    <form action="" method="POST">
			        <div class="mk-select-area py-2 px-3 bg-white">
			            <input id="select1" type="radio" name="mk_revisions_clean" class="form-controll">
			            <label for="select1" class="form-controll"><b>Revisions</b><small id="mk_revisions_clean_small" class="ml-2">Total trash: <?php echo wp_kses_post($mk_db_post_size_check) . ' byte'; ?></small><small id="mk_revisions_clean_alert" class="d-none"><alert class="alert alert-success">Table clean and optimized.</alert></small></label>
			            <small><p>[The WordPress revisions system stores a record of each saved draft or published update. Therefore, a large load occurs in the database. It is recommended that you clean the revisions ]</p></small>
			        </div>
			        <div class="mk-select-area py-2 px-3 bg-white mt-3">
			            <input id="select2" type="radio" name="mk_draft_posts_clean" class="form-controll">
			            <label for="select2" class="form-controll"><b>Draft Posts</b><small id="mk_draft_posts_clean_small" class="ml-2">Total trash: <?php echo wp_kses_post($mk_db_post_size_check) . ' byte'; ?></small><small id="mk_draft_posts_clean_alert" class="d-none"><alert class="alert alert-success">Table clean and optimized.</alert></small></label>
			            <small><p>[You can clear your accumulated draft posts WARNİNG: Do not check this option if you want to keep your draft posts.]</p></small>
			        </div>
			        <div class="mk-select-area py-2 px-3 bg-white mt-3">
			            <input id="select3" type="radio" name="mk_trash_posts_clean" class="form-controll">
			            <label for="select3" class="form-controll"><b>Trash Posts</b><small id="mk_trash_posts_clean_small" class="ml-2">Total trash: <?php echo wp_kses_post($mk_db_post_size_check) . ' byte'; ?></small><small id="mk_trash_posts_clean_alert" class="d-none"><alert class="alert alert-success">Table clean and optimized.</alert></small></label>
			            <small><p>[You can clear your accumulated trash posts WARNİNG: Do not check this option if you want to keep your trash posts.]</p></small>
			        </div>
			        <div class="mk-select-area py-2 px-3 bg-white mt-3">
			            <input id="select4" type="radio" name="mk_broken_tables_clean" class="form-controll">
			            <label for="select4" class="form-controll"><b>Broken Tables</b><small id="mk_broken_tables_clean_small" class="ml-2">Total trash: <?php echo wp_kses_post($mk_db_broken_tables_size_check) . ' byte'; ?></small><small id="mk_broken_tables_clean_alert" class="d-none"><alert class="alert alert-success">Table clean and optimized.</alert></small></label>
			            <small><p>[Delete broken, unrelated data between tables in a WordPress database.]</p></small>
			        </div>
			        <div class="mk-select-area py-2 px-3 bg-white mt-3">
			            <input id="select5" type="radio" name="mk_spam_comments_clean" class="form-controll">
			            <label for="select5" class="form-controll"><b>Spam Comments</b><small id="mk_spam_comments_clean_small" class="ml-2">Total trash: <?php echo wp_kses_post($mk_db_spam_comments_size_check) . ' byte'; ?></small><small id="mk_spam_comments_clean_alert" class="d-none"><alert class="alert alert-success">Table clean and optimized.</alert></small></label>
			            <small><p>[Clean up all spam comments.]</p></small>
			        </div>
			        <div class="mk-select-area py-2 px-3 bg-white mt-3">
			            <input id="select6" type="radio" name="mk_postmeta_trash_clean" class="form-controll">
			            <label for="select6" class="form-controll"><b>Postmeta Trash</b><small id="mk_postmeta_trash_clean_small" class="ml-2">Total trash: <?php echo wp_kses_post($mk_db_postmeta_trash_size_check) . ' byte'; ?></small><small id="mk_postmeta_trash_clean_alert" class="d-none"><alert class="alert alert-success">Table clean and optimized.</alert></small></label>
			            <small><p>[Clean up trash in the postmeta table]</p></small>
			        </div>
			        <div class="mk-select-area py-2 px-3 bg-white mt-3">
			            <input id="select7" type="radio" name="mk_transient_options_clean" class="form-controll">
			            <label for="select7" class="form-controll"><b>Transient Options</b><small id="mk_transient_options_clean_small" class="ml-2">Total trash: <?php echo wp_kses_post($mk_db_options_size_check) . ' byte'; ?></small><small id="mk_transient_options_clean_alert" class="d-none"><alert class="alert alert-success">Table clean and optimized.</alert></small></label>
			            <small><p>[Delete unnecessary entries in the wp_options table named transient.]</p></small>
			        </div>
			        <div class="mk-select-area py-2 px-3 bg-white mt-3">
			            <input id="select8" type="radio" name="mk_postmeta_table_trash_clean" class="form-controll">
			            <label for="select8" class="form-controll"><b>Posts Table Trash</b><small id="mk_postmeta_table_trash_clean_small" class="ml-2">Total trash: <?php echo wp_kses_post($mk_db_postmeta_trash_size_check) . ' byte'; ?></small><small id="mk_postmeta_table_trash_clean_alert" class="d-none"><alert class="alert alert-success">Table clean and optimized.</alert></small></label>
			            <small><p>[Delete broken, unrelated data between tables in a WordPress Posts database.]</p></small>
			        </div>
			        <div id="mk_button_area" class="col-12 mt-3">
			            <input name="hiddensubmit" type="hidden" value="clean">
			            <input type="button" class="btn btn-danger" onclick="onClickHeader()" value="Select All">
			            <input type="submit" class="btn btn-danger" value="Clean">
			            <?php wp_nonce_field('mk_pm_nonce_action'); ?>
			        </div>
			    </form>
			</div>
		</div>
	</div>
</div>

<script>
    
    function onClickHeader() {
        
    var checkboxes = document.getElementsByTagName('input');

        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].type == 'radio') {
                checkboxes[i].checked = true;
            }
        }
    }
    
</script>

<?php } }  