<?php

    $args = array(
        'meta_query' => array(
            array(
                'key'     => '_pps_future_notification',
                'value'   => 1,
            ),
        ),
    );
    $query = new WP_Query( $args );

    if (isset( $_POST['pps_delete_hidden'] ) && ( $_POST['pps_delete_hidden'] == 'Y' )) { 

        if (isset($_POST['pps_scheduled_posts'])) {
            foreach ($_POST['pps_scheduled_posts'] as $post_ID) {
                delete_post_meta($post_ID, '_pps_future_notification');
            }
            ?>
            <div class="updated"><p><strong><?php _e( 'Push notifications removed from queue.' ); ?></strong></p></div>
            <?php
        }

    }
?>



<div class="wrap">
    
    <div id="icon-options-general" class="icon32"></div>
    <h2>Parse Push Service - Pending Notifications</h2>

    <div id="poststuff">
    
        <div id="post-body" class="metabox-holder columns-2">
        
            <!-- main content -->
            <div id="post-body-content">
                

                    <div class="scheduled-posts-queue">

                        <?php   
                        if ( !$query->have_posts()) {
                            echo '<p>Awesome! There aren\'t any pending Push Notifications!</p>';
                        }
                        else {
                        ?>
                            <p><?php echo __('Total notifications: ', 'pps_trdom' ).'<strong>'.count( $query->post_count );?></strong></p>
                            <form name="pps_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">  
                                <input type="hidden" name="pps_delete_hidden" value="Y">  
                                <!--    $scheduledPostsInfo = array('message'      => $values['message'],
                                                                'badge'        => $values['badge'],
                                                                'post_type'    => $post->post_type,
                                                                'post_id'      => $post->ID,
                                                                'last_updated' => time()); -->
                                <table class="widefat">
                                    <thead>
                                        <tr>
                                            <th class="check-column">
                                                <input id="selectall" type="checkbox" />
                                            </th>
                                            <th><strong>Title</strong></th>
                                            <th><strong>Post Type</strong></th>
                                            <th><strong>Post ID</strong></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $i = 0;
                                            while($query->have_posts()) {
                                                $post = $query->next_post();
                                                if ($i % 2 != 0)
                                                    echo '<tr>';
                                                else
                                                    echo '<tr class="alternate">';

                                                echo '  <td>';
                                                echo '  <input type="checkbox" class="pps_ckb" name="pps_scheduled_posts[]" value="'.$post->ID.'" />';
                                                echo '  </td>';
                                                echo '  <td>'.$post->post_title.'</td>';
                                                echo '  <td>'.$post->post_type.'</td>';
                                                echo '  <td><a href="'.get_edit_post_link($post->ID).'">'.$post->ID.'</a></td>';
                                                echo '</tr>';
                                                $i++;
                                            }
                                        ?>
                                    </tbody>
                                </table>
                
                                <p class="submit">
                                    <a id="pps_delete_ask" href="#" class="button button-secondary ">Remove from queue</a> 
                                    <p id='pps_delete_confirm' style="display: none;">
                                        Are you sure? &nbsp;
                                        <input id="pps_delete_confirm" type="submit" name="Submit" class="button button-primary" value="<?php _e('Yes, remove them from queue', 'pps_trdom' ) ?>" />
                                        <a id="pps_delete_deny" href="#" class="button button-secondary">No, cancel this action</a> 
                                    </p>
                                </tr>
                            </form>


                        <?php
                        }
                        ?>
                    </div> <!-- .scheduled-posts-queue -->
                    
                </div> <!-- .meta-box-sortables .ui-sortable -->
                
            </div> <!-- post-body-content -->
            

        </div> <!-- #post-body .metabox-holder .columns-2 -->
        
        <br class="clear">
    </div> <!-- #poststuff -->
    
</div> <!-- .wrap -->

<script type="text/javascript">

</script>