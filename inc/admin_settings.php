<?php  

    /////////////////////////////
    // Working with Parameters //
    /////////////////////////////
    if(isset( $_POST['pps_hidden'] ) && ( $_POST['pps_hidden'] == 'Y' )) {  
        //Form data sent 
        $sppsAppName = $_POST['pps_appName'];  
        update_option('pps_appName', $sppsAppName); 

        $sppsAppID = $_POST['pps_appID'];  
        update_option('pps_appID', $sppsAppID);  
          
        $sppsRestApi = $_POST['pps_restApi'];  
        update_option('pps_restApi', $sppsRestApi); 

        $sppsAutoSendTitle = '';
        if (isset($_POST['pps_autoSendTitle'])) {  
        	update_option('pps_autoSendTitle', 'true');
        	$sppsAutoSendTitle = ' checked="checked"';
        }
        else
        	update_option('pps_autoSendTitle', 'false');

        $sppsIncludePostID = '';
        if (isset($_POST['pps_includePostID'])) {
            update_option('pps_includePostID', 'true');
            $sppsIncludePostID = ' checked="checked"';
        }
        else
            update_option('pps_includePostID', 'false');


        $sppsDiscardScheduledPosts = '';
        if (isset($_POST['pps_discardScheduledPosts'])) {
            update_option('pps_discardScheduledPosts', 'true');
            $sppsDiscardScheduledPosts = ' checked="checked"';
        }
        else
            update_option('pps_discardScheduledPosts', 'false');


        $sppsSaveLastMessage = '';
        if (isset($_POST['pps_saveLastMessage'])) {  
            update_option('pps_saveLastMessage', 'true');
            $sppsSaveLastMessage = ' checked="checked"';
        }
        else
            update_option('pps_saveLastMessage', 'false');

        $sppsEnableSound = '';
        if (isset($_POST['pps_enableSound'])) {  
            update_option('pps_enableSound', 'true');
            $sppsEnableSound = ' checked="checked"';
        }
        else
            update_option('pps_enableSound', 'false');


        $sppsDoNotIncludeChannel = '';
        if (isset($_POST['pps_doNotIncludeChannel'])) {  
            update_option('pps_doNotIncludeChannel', 'true');
            $sppsDoNotIncludeChannel = ' checked="checked"';
        }
        else
            update_option('pps_doNotIncludeChannel', 'false');

        $sppsPushChannels = trim($_POST['pps_pushChannels'], " ");
        update_option('pps_pushChannels', $sppsPushChannels);

        $sppsMetaBoxPriority = $_POST['pps_metaBoxPriority'];
        update_option('pps_metaBoxPriority', $sppsMetaBoxPriority);


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
        $sppsAppName   = get_option('pps_appName');
        $sppsAppID     = get_option('pps_appID');  
        $sppsRestApi   = get_option('pps_restApi'); 
       	$sppsAutoSendTitle = '';
       	if (get_option('pps_autoSendTitle') == 'true') 
       		$sppsAutoSendTitle = ' checked="checked"';
        $sppsSaveLastMessage = '';
        if (get_option('pps_saveLastMessage') == 'true') 
            $sppsSaveLastMessage = ' checked="checked"';
        $sppsEnableSound = '';
        if (get_option('pps_enableSound') == 'true') 
            $sppsEnableSound = ' checked="checked"';

        $sppsIncludePostID = '';
        if (get_option('pps_includePostID') == 'true')
            $sppsIncludePostID = ' checked="checked"';

        $sppsDiscardScheduledPosts = '';
        if (get_option('pps_discardScheduledPosts') == 'true')
            $sppsDiscardScheduledPosts = ' checked="checked"';

        $sppsPushChannels = get_option('pps_pushChannels');

        $sppsDoNotIncludeChannel = '';
        if (get_option('pps_doNotIncludeChannel') == 'true') 
            $sppsDoNotIncludeChannel = ' checked="checked"';

        $sppsMetaBoxPriority = get_option('pps_metaBoxPriority');
        if ($sppsMetaBoxPriority == '') {
            $sppsMetaBoxPriority = 'high';
        }
    }  


    if (isset( $_POST['pps_push_hidden'] ) && ( $_POST['pps_push_hidden'] == 'Y' )) {
    	$msg = $_POST['pps_push_message'];
    	$badge = $_POST['pps_push_badge'];

    	if (get_option('pps_appID') == null || get_option('pps_restApi') == null || $msg == null)
    	{ 
    		?>
    		<div class="error"><p><strong><?php _e('Fill all Parse.com Account settings, write a message and try again.' ); ?></strong></p></div>
    		<?php
    	}
    	else
    	{
    		include('parse-api.php');
    		echo sendPushNotification(get_option('pps_appID'), get_option('pps_restApi'), $msg, $badge, null, get_option('pps_pushChannels'), $_POST['pushExtraKey'], $_POST['pushExtraValue']);
    	}
    }
?> 





<?php
//////////////////
// Main content //
//////////////////
?>





<div class="wrap">
    
    <div id="icon-options-general" class="icon32"></div>
    <h2>Simple Parse Push Service</h2>
    
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
                                    <tr valign="top">
                                        <td scope="row"><label for="tablecell"><?php _e("Application name: " ); ?></label></td>
                                        <td><input type="text" name="pps_appName" value="<?php echo $sppsAppName; ?>" class="regular-text"></td>
                                    </tr>
                                    <tr valign="top" class="alternate">
                                        <td scope="row"><label for="tablecell"><i><?php _e("Application ID: " ); ?></i></label></td>
                                        <td><input type="text" name="pps_appID" value="<?php echo $sppsAppID; ?>" class="regular-text"></td>
                                    </tr>
                                    <tr valign="top">
                                        <td scope="row"><label for="tablecell"><i><?php _e("REST API Key: " ); ?></i></label></td>
                                        <td><input type="text" name="pps_restApi" value="<?php echo $sppsRestApi; ?>" class="regular-text"></td>
                                    </tr>
                                    <tr valign="top" class="alternate">
                                        <td scope="row"><label for="tablecell">Sound</label></td>
                                        <td>
                                            <input type="checkbox" name="pps_enableSound" <?php echo $sppsEnableSound; ?> > Enable
                                            <p class="description">Enable the default sound for Push Notifications.</p>
                                        </td>
                                    </tr>
                                    <tr valign="top">
                                        <td scope="row"><label for="tablecell">Notification title</label></td>
                                        <td><input type="checkbox" name="pps_autoSendTitle" <?php echo $sppsAutoSendTitle; ?> > Send post's title as the Push Notification's title
                                            <p class="description">This option is available while you edit a post or create a new one.</p></td>
                                    </tr>
                                    <tr valign="top" class="alternate">
                                        <td scope="row"><label for="tablecell">Notification message</label></td>
                                        <td>
                                            <input type="checkbox" name="pps_saveLastMessage" <?php echo $sppsSaveLastMessage; ?> > Remember last used message in posts
                                            <p class="description">You can check this option and send a default message (e.g. Check out my new post! ) every time you create a new post.</p>
                                        </td>
                                    </tr>
                                    <tr valign="top">
                                        <td scope="row"><label for="tablecell">Post id</label></td>
                                        <td><input type="checkbox" name="pps_includePostID" <?php echo $sppsIncludePostID; ?> > Auto include post_ID as extra parameter
                                            <p class="description">See the 'Sample Payload' for more technical info.</p>
                                        </td>
                                    </tr>
                                    <tr valign="top">
                                        <td scope="row"><label for="tablecell">Discard for scheduled</label></td>
                                        <td><input type="checkbox" name="pps_discardScheduledPosts" <?php echo $sppsDiscardScheduledPosts; ?> > Do not save Push Notification for scheduled posts
                                            <p class="description">If this is disabled, every time you schedule a post for future publish, the appropriate Push Notification (if any) will be saved add Pushed with post's publication. Existing (saved) push notifications won't be affected.</p>
                                        </td>
                                    </tr>
                                </table>

                                <!-- settings - about push channels -->
                                <hr/>
                                <table class="form-table">
                                    <tr valign="top">
                                        <td scope="row"><label for="tablecell">Push channels</label></td>
                                        <td><input type="text" name="pps_pushChannels" placeholder="e.g. news,sports,tennis" value="<?php echo $sppsPushChannels; ?>" class="regular-text">
                                            <p class="description"><strong>Comma</strong> separated and <strong>without</strong> spaces, names for the channels you want to be receiving the notifications. If empty, global broadcast channel (GBC) is selected (GBC is an empty string).</p>
                                        </td>
                                    </tr>
                                    <tr valign="top" class="alternate">
                                        <td scope="row"><label for="tablecell"></label></td>
                                        <td><input type="checkbox" name="pps_doNotIncludeChannel" <?php echo $sppsDoNotIncludeChannel; ?> > Do not include ANY channel. Send notifications to everyone.</td>
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
                                                        if ($priorities[$i] == $sppsMetaBoxPriority) {
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
                                                $savedPostTypes = get_option('pps_metabox_pt');
                                           
                                                /* Posts are pre-defined
                                                =================================== */
                                                echo '<input type="checkbox" disabled checked/> Posts <br/>';
                                                echo '<input type="hidden" name="pps_metabox_pt[]" value="post" />';
                                            
                                                /* Check if pages are selected
                                                ==================================== */
                                                $sppsSavedPage = '';
                                                if (in_array('page', $savedPostTypes))
                                                    $sppsSavedPage = ' checked="checked"';
                                                // die( print_r($savedPostTypes));
                                                echo '<input type="checkbox" name="pps_metabox_pt[]" value="page"'.$sppsSavedPage.'/> Pages <br/>';
                                           

                                                /* Check for custom types
                                                ==================================== */
                                                $args = array('_builtin' => false, );
                                                $post_types = get_post_types( $args, 'objects' ); 
                                                foreach ( $post_types as $post_type ) {

                                                    $sppsSaved = '';
                                                    if (in_array($post_type->name, $savedPostTypes))
                                                        $sppsSaved = ' checked="checked"';
                                                    echo '<input type="checkbox" name="pps_metabox_pt[]" value="'.$post_type->name.'" '.$sppsSaved.' />'.$post_type->label.' <br/>';
                                                }
                                            ?>

                                        </td>
                                    </tr>
                                </table>



                                <p class="submit">
                                    <input type="submit" name="Submit" class="button button-primary" value="<?php _e('Update Options', 'pps_trdom' ) ?>" />
                                </tr>
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
                                    <tr valign="top">
                                        <td scope="row"><label for="tablecell"><i><?php _e("Badge:"); ?></i></label></td>
                                        <td><input type="text" name="pps_push_badge" class="regular-text">
                                            <p class="description"><i>0 or 1 or 2...  "increment" value also works (for iOS)</i></p>
                                        </td>
                                    </tr>
                                </table>
                                <table class="form-table">
                                    <tr valign="top">
                                        <td scope="row"><?php _e("Extra key") ?> <input type="text" name="pushExtraKey" class="regular-text">
                                        <?php _e("Extra value") ?> <input type="text" name="pushExtraValue" class="regular-text"></td>
                                    </tr>
                                    <tr valign="top">
                                        <td scope="row" >
                                            <p class="description">
                                                With these extra key/value fields, you can add an extra parameter into the push notification payload as in the 'Sample Payload' (with post_id beeing the extra parameter). 
                                                You can find more information about <a href="https://www.parse.com/docs/push_guide#receiving-responding/iOS">Responding to the Payload</a> reading <a href="http://www.parse.com">Parse.com</a>'s <a href="https://www.parse.com/docs/">documentation</a>.
                                            </p>
                                        </td>
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
