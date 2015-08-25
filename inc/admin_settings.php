<?php  

    /////////////////////////////
    // Working with Parameters //
    /////////////////////////////
    if(isset( $_POST['pps_hidden'] ) && ( $_POST['pps_hidden'] == 'Y' )) {  
        //Form data sent 
        $ppsAppID = $_POST['pps_appID'];
        update_option('pps_appID', $ppsAppID, false);
          
        $ppsRestApi = $_POST['pps_restApi'];  
        update_option('pps_restApi', $ppsRestApi, false);

        $ppsEnableSound = '';
        if (isset($_POST['pps_enableSound'])) {  
            update_option('pps_enableSound', 'true', false);
            $ppsEnableSound = ' checked="checked"';
        }
        else {
            update_option('pps_enableSound', 'false', false);
        }

        $ppsMetaBoxPriority = $_POST['pps_metaBoxPriority'];
        update_option('pps_metaBoxPriority', $ppsMetaBoxPriority, false);


        if (isset($_POST['pps_metabox_pt'])) {
            update_option('pps_metabox_pt', $_POST['pps_metabox_pt'], false);
        }
        else {
            delete_option('pps_metabox_pt');
        }
        ?>  
        <div class="updated"><p><strong><?php _e('Options saved.' ); ?></strong></p></div>  
    <?php
    } else {  
        //Normal page display  
        $ppsAppName   = get_option('pps_appName');
        $ppsAppID     = get_option('pps_appID');  
        $ppsRestApi   = get_option('pps_restApi'); 
        $ppsEnableSound = '';
        if (get_option('pps_enableSound') == 'true') 
            $ppsEnableSound = ' checked="checked"';

        $ppsMetaBoxPriority = get_option('pps_metaBoxPriority');
        if ($ppsMetaBoxPriority == '') {
            $ppsMetaBoxPriority = 'high';
        }
    }  


    if (isset( $_POST['pps_push_hidden'] ) && ( $_POST['pps_push_hidden'] == 'Y' )) {
    	$msg = $_POST['pps_push_message'];

    	if (get_option('pps_appID') == null || get_option('pps_restApi') == null || $msg == null)
    	{ 
    		?>
    		<div class="error"><p><strong><?php _e('Fill all Parse.com Account settings, write a message and try again.' ); ?></strong></p></div>
    		<?php
    	}
    	else
    	{
    		include('parse-api.php');
            echo "<div id='pps-notification' class='updated fade'><p><strong>Parse.com response: </strong> ";
    		echo pps_send_push_notification(array(
                'alert' => $msg,
                'badge' => 0
            ));
            echo "</p></div>";
    	}
    }
?> 





<?php
//////////////////
// Main content //
//////////////////
print_r(get_current_screen());
?>





<div class="wrap">
    
    <div id="icon-options-general" class="icon32"></div>
    <h2>Parse Push Service</h2>
    
    <div id="poststuff">
    
        <div id="post-body" class="metabox-holder columns-2">
        
            <!-- main content -->
            <div id="post-body-content">
                
                <div class="meta-box-sortables ui-sortable">
                    
                    <div class="postbox">
                    
                        <h3><span><?php    echo __( 'Parse.com Push Service - Settings', 'pps_trdom' ) . " (Parse.com <a href=\"http://parse.com/apps\" target=\"_blank\">Dashboard</a>)"; ?>  </span></h3>
                        <div class="inside">
                            <form name="pps_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">  
                                <input type="hidden" name="pps_hidden" value="Y">  
        
                                <table class="form-table">
                                    <tr valign="top" class="alternate">
                                        <td scope="row"><label for="tablecell"><i><?php _e("Application ID: " ); ?></i></label></td>
                                        <td><input type="text" name="pps_appID" value="<?php echo $ppsAppID; ?>" class="regular-text"></td>
                                    </tr>
                                    <tr valign="top">
                                        <td scope="row"><label for="tablecell"><i><?php _e("REST API Key: " ); ?></i></label></td>
                                        <td><input type="text" name="pps_restApi" value="<?php echo $ppsRestApi; ?>" class="regular-text"></td>
                                    </tr>
                                    <tr valign="top" class="alternate">
                                        <td scope="row"><label for="tablecell">Sound</label></td>
                                        <td>
                                            <input type="checkbox" name="pps_enableSound" <?php echo $ppsEnableSound; ?> > Enable
                                            <p class="description">Enable the default sound for Push Notifications.</p>
                                        </td>
                                    </tr>
                                </table>

                                <!-- settings - meta box -->
                                <hr/>
                                <table class="form-table">
                                    <tr valign="top">
                                        <td scope="row"><label for="tablecell"><?php _e("Meta Box priority " ); ?></label></td>
                                        <td>
                                            <select name="pps_metaBoxPriority">
                                                <?php
                                                    $priorities = array('high', 'core', 'default', 'low');
                                                    for ($i = 0; $i < 4; $i++) {
                                                        if ($priorities[$i] == $ppsMetaBoxPriority) {
                                                            echo "<option selected value='$priorities[$i]'>$priorities[$i]</option>";
                                                        }
                                                        else {
                                                            echo "<option value='$priorities[$i]'>$priorities[$i]</option>";
                                                        }
                                                    }
                                                ?>
                                            </select>
                                            <p class="description">The priority for the 'Meta Box' inside the 'edit post' menu.
                                        </td>
                                    </tr>
                                </table>


                                <!-- settings - post types with metabox enabled -->
                                <hr/>
                                <table class="form-table">
                                    <tr valign="top">
                                        <td scope="row">
                                            <label for="tablecell">
                                                <h3><span><?php echo __( 'Post Types with MetaBox enabled', 'pps_trdom' ) ?></span></h3>
                                            </label>

                                            <?php
                                                $savedPostTypes = get_option('pps_metabox_pt', array());
                                           
                                                /* Posts are pre-defined
                                                =================================== */
                                                echo '<input type="checkbox" disabled checked/> Posts <br/>';
                                                echo '<input type="hidden" name="pps_metabox_pt[]" value="post" />';
                                            
                                                /* Check if pages are selected
                                                ==================================== */
                                                $ppsSavedPage = '';
                                                if (in_array('page', $savedPostTypes))
                                                    $ppsSavedPage = ' checked="checked"';
                                                // die( print_r($savedPostTypes));
                                                echo '<input type="checkbox" name="pps_metabox_pt[]" value="page"'.$ppsSavedPage.'/> Pages <br/>';
                                           

                                                /* Check for custom types
                                                ==================================== */
                                                $args = array('_builtin' => false, );
                                                $post_types = get_post_types( $args, 'objects' ); 
                                                foreach ( $post_types as $post_type ) {

                                                    $ppsSaved = '';
                                                    if (in_array($post_type->name, $savedPostTypes))
                                                        $ppsSaved = ' checked="checked"';
                                                    echo '<input type="checkbox" name="pps_metabox_pt[]" value="'.$post_type->name.'" '.$ppsSaved.' />'.$post_type->label.' <br/>';
                                                }
                                            ?>

                                        </td>
                                    </tr>
                                </table>



                                <p class="submit">
                                    <input type="submit" name="Submit" class="button button-primary" value="<?php _e('Update Options', 'pps_trdom' ) ?>" />
                                </p>
                            </form>


                        </div> <!-- .inside -->
                    
                    </div> <!-- .postbox -->


                    <div id="sendNow" style="height:40px;"></div>
                    <div class="postbox">
                        <h3><span>Send a Push Notification right now!</span></h3>
                        <div class="inside">
                            <!-- push dashboard -->
                            <form name="sendPush_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
                                <input type="hidden" name="pps_push_hidden" value="Y">  
                                
                                <table class="form-table">
                                    <tr valign="top">
                                        <td scope="row"><label for="tablecell"><i><?php _e("Message:"); ?></i></label></td>
                                        <td><input type="text" name="pps_push_message" class="regular-text"></td>
                                    </tr>
                                </table>
                                <p class="submit">
                                    <input type="submit" name="Submit" class="button button-action" value="<?php _e('Send Push Notification') ?>" />  
                                </p> 
                            </form>
                        </div>
                    </div> <!-- .sent push - box -->
                    
                </div> <!-- .meta-box-sortables .ui-sortable -->
                
            </div> <!-- post-body-content -->
            
            <!-- sidebar -->
            <div id="postbox-container-1" class="postbox-container">
                
                <div class="meta-box-sortables">
                    <div class="postbox">
                        <h3><span>Sample payload</span></h3>
                        <div class="inside">
                            <p style="text-align:justify;">
                           This payload will be received by every iOS device. Similar will be on Android and Windows (Phone) too.<br/> 
                           The thing to <strong>notice</strong> here, is the "post_id" key, which contains a post's id.</p>
<pre> 
{
  "aps":{
    "alert":"alert message",
    "sound":"default"
  },
  "post_id":324
}
</pre>
                        </div>
                    </div>

                </div> <!-- .meta-box-sortables -->
                
            </div> <!-- #postbox-container-1 .postbox-container -->
            
        </div> <!-- #post-body .metabox-holder .columns-2 -->
        
        <br class="clear">
    </div> <!-- #poststuff -->
    
</div> <!-- .wrap -->
