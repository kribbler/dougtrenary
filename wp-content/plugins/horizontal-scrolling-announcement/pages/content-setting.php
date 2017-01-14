<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<div class="wrap">
  <div class="form-wrap">
    <div id="icon-edit" class="icon32 icon32-posts-post"><br>
    </div>
    <h2><?php _e(WP_hsa_TITLE, WP_hsa_UNIQUE_NAME); ?></h2>	
    <?php
	$hsa_title = get_option('hsa_title');
	$hsa_scrollamount = get_option('hsa_scrollamount');
	$hsa_scrolldelay = get_option('hsa_scrolldelay');
	$hsa_direction = get_option('hsa_direction');
	$hsa_style = get_option('hsa_style');
	$hsa_noannouncement = get_option('hsa_noannouncement');
	if (isset($_POST['hsa_submit'])) 
	{
		//	Just security thingy that wordpress offers us
		check_admin_referer('hsa_form_setting');
			
		$hsa_title = stripslashes($_POST['hsa_title']);
		$hsa_scrollamount = stripslashes($_POST['hsa_scrollamount']);
		$hsa_scrolldelay = stripslashes($_POST['hsa_scrolldelay']);
		$hsa_direction = stripslashes($_POST['hsa_direction']);
		$hsa_style = stripslashes($_POST['hsa_style']);
		$hsa_noannouncement = stripslashes($_POST['hsa_noannouncement']);
	
		update_option('hsa_title', $hsa_title );
		update_option('hsa_scrollamount', $hsa_scrollamount );
		update_option('hsa_scrolldelay', $hsa_scrolldelay );
		update_option('hsa_direction', $hsa_direction );
		update_option('hsa_style', $hsa_style );
		update_option('hsa_noannouncement', $hsa_noannouncement );
		
		?>
		<div class="updated fade">
			<p><strong><?php _e('Details successfully updated.', WP_hsa_UNIQUE_NAME); ?></strong></p>
		</div>
		<?php
	}
	?>
	<script language="JavaScript" src="<?php echo get_option('siteurl'); ?>/wp-content/plugins/horizontal-scrolling-announcement/pages/setting.js"></script>
    <form name="hsa_form" method="post" action="">
        <h3><?php _e('Default settings', WP_hsa_UNIQUE_NAME); ?></h3>
		<label for="tag-width"><?php _e('Widget Title', WP_hsa_UNIQUE_NAME); ?></label>
		<input name="hsa_title" type="text" value="<?php echo $hsa_title; ?>"  id="hsa_title" size="70" maxlength="150">
		<p><?php _e('Please enter your widget title.', WP_hsa_UNIQUE_NAME); ?></p>
		
		<label for="tag-width"><?php _e('Scroll amount', WP_hsa_UNIQUE_NAME); ?></label>
		<input name="hsa_scrollamount" type="text" value="<?php echo $hsa_scrollamount; ?>"  id="hsa_scrollamount" maxlength="3">  
		<p><?php _e('Please enter scroll amount, This will make the scroll faster. (Example: 2)', WP_hsa_UNIQUE_NAME); ?></p>
		
		<label for="tag-width"><?php _e('Scroll delay', WP_hsa_UNIQUE_NAME); ?></label>
		<input name="hsa_scrolldelay" type="text" value="<?php echo $hsa_scrolldelay; ?>"  id="hsa_scrolldelay" maxlength="3">
		<p><?php _e('Set the amount of delay in milliseconds. (Example: 5)', WP_hsa_UNIQUE_NAME); ?></p>
		
		<label for="tag-width"><?php _e('Direction', WP_hsa_UNIQUE_NAME); ?></label>
		<select name="hsa_direction" id="hsa_direction">
			<option value='left' <?php if($hsa_direction == 'left') { echo "selected='selected'" ; } ?>>Right to Left</option>
			<option value='right' <?php if($hsa_direction == 'right') { echo "selected='selected'" ; } ?>>Left to Right</option>
		</select>
		<p><?php _e('Please select your scroll direction.', WP_hsa_UNIQUE_NAME); ?></p>
		
		<label for="tag-width"><?php _e('CSS attribute', WP_hsa_UNIQUE_NAME); ?></label>
		<input name="hsa_style" type="text" value="<?php echo $hsa_style; ?>"  id="hsa_style" size="70" maxlength="500">
		<p><?php _e('Please enter your CSS attributes for style. (Example: color:#FF0000; font:Arial;)', WP_hsa_UNIQUE_NAME); ?></p>
		
		<label for="tag-width"><?php _e('No announcement text', WP_hsa_UNIQUE_NAME); ?></label>
		<input name="hsa_noannouncement" type="text" value="<?php echo $hsa_noannouncement; ?>"  id="hsa_noannouncement" size="70" maxlength="500">
		<p><?php _e('This text will be display, if no announcement available or all announcement expired.', WP_hsa_UNIQUE_NAME); ?></p>
		
		<p class="submit">
		<input name="hsa_submit" id="hsa_submit" class="button" value="Submit" type="submit" />
		<input name="publish" lang="publish" class="button" onclick="hsa_redirect()" value="Cancel" type="button" />
		<input name="Help" lang="publish" class="button" onclick="hsa_help()" value="Help" type="button" />
		</p>
		<?php wp_nonce_field('hsa_form_setting'); ?>
    </form>
  </div>
  <p class="description"><?php echo WP_hsa_LINK; ?></p>
</div>