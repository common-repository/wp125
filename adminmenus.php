<?php

if (function_exists('wp_enqueue_style')) {

	function wp125_queue_admin_page_scripts($hook) {
		if (strpos($hook, 'wp125_addedit') !== false) {
			wp_enqueue_script('jquery');
			wp_enqueue_script('thickbox');
			wp_enqueue_style('thickbox');
			wp_enqueue_script('media-upload');
		}
	}

	add_action('admin_enqueue_scripts', 'wp125_queue_admin_page_scripts');

}

//Write Manage Menu
function wp125_write_managemenu() {
	echo '<div class="wrap">
	<h2>'.__('Manage Ads', 'wp125').'</h2>';

	//Handle deactivations
	if ($_GET['wp125action'] == "deactivate") {
		$theid = intval($_GET['theid']);
		$nonce = wp_create_nonce('nonce_wp125_adstate'.$theid);
		echo '<div id="message" class="updated fade"><p>'.__('Are you sure you want to deactivate the ad?', 'wp125').' <a href="'.esc_url('admin.php?page=wp125/wp125.php&wp125action=deactivateconf&theid='.$theid.'&wp125_nonce_adstate='.$nonce).'">'.__('Yes', 'wp125').'</a> &nbsp; <a href="admin.php?page=wp125/wp125.php">'.__('No!', 'wp125').'</a></p></div>';
	}
	if ($_GET['wp125action'] == "deactivateconf") {
		$theid = intval($_GET['theid']);
		global $wpdb, $table_prefix;
		$adtable_name = $wpdb->prefix . "wp125_ads";
		if (wp_verify_nonce($_GET['wp125_nonce_adstate'], 'nonce_wp125_adstate'.$theid)) {
			$wpdb->update(
				$adtable_name,
				array('status' => '0'),
				array('id' => $theid)
				);
			echo '<div id="message" class="updated fade"><p>'.__('Ad deactivated.', 'wp125').'</p></div>';
		}
	}

	//Handle REactivations
	if ($_GET['wp125action'] == "activate") {
		$theid = intval($_GET['theid']);
		$nonce = wp_create_nonce('nonce_wp125_adstate'.$theid);
		echo '<div id="message" class="updated fade"><p>'.__('Are you sure you want to reactivate the ad?', 'wp125').' <a href="'.esc_url('admin.php?page=wp125/wp125.php&showmanage=inactive&wp125action=activateconf&theid='.$theid.'&wp125_nonce_adstate='.$nonce).'">'.__('Yes', 'wp125').'</a> &nbsp; <a href="admin.php?page=wp125/wp125.php&showmanage=inactive">'.__('No!', 'wp125').'</a></p></div>';
	}
	if ($_GET['wp125action'] == "activateconf") {
		$theid = intval($_GET['theid']);
		global $wpdb, $table_prefix;
		$adtable_name = $wpdb->prefix . "wp125_ads";
		if (wp_verify_nonce($_GET['wp125_nonce_adstate'], 'nonce_wp125_adstate'.$theid)) {
			$wpdb->update(
				$adtable_name,
				array('status' => '1', 'pre_exp_email' => '0'),
				array('id' => $theid)
				);
			echo '<div id="message" class="updated fade"><p>'.__('Ad activated.', 'wp125').'</p></div>';
		}
	}

	echo '<ul class="subsubsub">'; ?>
	<li><a href="admin.php?page=wp125/wp125.php"  <?php if ($_GET['showmanage'] != 'inactive') { echo 'class="current"'; } ?>><?php _e('Active Ads', 'wp125'); ?></a> | </li><li><a href="admin.php?page=wp125/wp125.php&showmanage=inactive" <?php if ($_GET['showmanage'] == 'inactive') { echo 'class="current"'; } ?>><?php _e('Inactive Ads', 'wp125'); ?></a></li>
	<?php echo '</ul>
	<table class="widefat">
	<thead><tr>
	<th scope="col">'.__('Slot', 'wp125').'</th>
	<th scope="col">'.__('Name', 'wp125').'</th>
	<th scope="col" class="num">'.__('Clicks', 'wp125').'</th>
	<th scope="col">'.__('Start Date', 'wp125').'</th>
	<th scope="col">'.__('End Date', 'wp125').'</th>
	<th scope="col"></th>
	<th scope="col" style="text-align:right;"><a href="admin.php?page=wp125_addedit" class="button rbutton">'.__('Add New', 'wp125').'</a></th>
	</tr></thead>
	<tbody>';

	global $wpdb;
	$adtable_name = $wpdb->prefix . "wp125_ads";
	if ($_GET['showmanage'] == 'inactive') {
		$wp125db = $wpdb->get_results("SELECT * FROM $adtable_name WHERE status = '0' ORDER BY id DESC", OBJECT);
	} else {
		$wp125db = $wpdb->get_results("SELECT * FROM $adtable_name WHERE status != '0' ORDER BY id DESC", OBJECT);
	}
	if ($wp125db) {
		foreach ($wp125db as $wp125db){

			echo '<tr>';
			echo '<td>'.esc_html($wp125db->slot).'</td>';
			echo '<td><strong>'.esc_html($wp125db->name).'</strong></td>';
			if ($wp125db->clicks!='-1') { echo '<td class="num">'.esc_html($wp125db->clicks).'</td>'; } else { echo '<td class="num">'.__('N/A', 'wp125').'</td>'; }
			echo '<td>'.esc_html($wp125db->start_date).'</td>';
			echo '<td>'.esc_html($wp125db->end_date).'</td>';
			echo '<td><a href="'.esc_url('admin.php?page=wp125_addedit&editad='.$wp125db->id).'">'.__('Edit', 'wp125').'</a></td>';
			if ( isset($_GET['showmanage']) && ($_GET['showmanage'] == "inactive")) {
				echo '<td><a href="'.esc_url('admin.php?page=wp125/wp125.php&showmanage=inactive&wp125action=activate&theid='.$wp125db->id).'">'.__('Activate', 'wp125').'</a></td>';
			} else {
				echo '<td><a href="'.esc_url('admin.php?page=wp125/wp125.php&wp125action=deactivate&theid='.$wp125db->id).'">'.__('Deactivate', 'wp125').'</a></td>';
			}
			echo '</tr>';

		}
	} else { echo '<tr> <td colspan="8">'.__('No ads found.', 'wp125').'</td> </tr>'; }

	echo '</tbody>
	</table>';
	echo '<br /><a href="'.esc_url(get_site_url().'?wp125_calendar').'" title="Subscribe with your calendaring software..."><img src="'.esc_url(wp125_get_plugin_dir('url').'/ical.gif').'" alt="iCalendar" /></a>';
	wp125_admin_page_footer();
	echo '</div>';
}

function wp125_write_addeditmenu() {
	//DB Data
	global $wpdb;
	$adtable_name = $wpdb->prefix . "wp125_ads";
	// Retrieve settings
	$setting_ad_orientation = get_option("wp125_ad_orientation");
	$setting_num_slots = get_option("wp125_num_slots");
	$setting_ad_order = get_option("wp125_ad_order");
	$setting_buyad_url = get_option("wp125_buyad_url");
	$setting_disable_default_style = get_option("wp125_disable_default_style");
	$setting_emailonexp = get_option("wp125_emailonexp");
	$setting_defaultad = get_option("wp125_defaultad");
	//If post is being edited, grab current info
	if ($_GET['editad']!='') {
		$theid = intval($_GET['editad']);
		$editingad = $wpdb->get_row($wpdb->prepare(
			"SELECT * FROM {$adtable_name} WHERE id = %d",
			$theid
			));
	}
	?><div class="wrap">

	<?php
	if ( $_POST['Submit'] && wp_verify_nonce($_POST['nonce_wp125_addedit'],'wp125_addedit') ) {
		$post_editedad = sanitize_text_field($_POST['editedad']);
		$post_adname = sanitize_text_field($_POST['adname']);
		$post_adslot = sanitize_text_field($_POST['adslot']);
		$post_adtarget = sanitize_text_field($_POST['adtarget']);
		$post_adexp = sanitize_text_field($_POST['adexp']);
		$post_adexpmo = sanitize_text_field($_POST['adexp-mo']);
		$post_adexpday = sanitize_text_field($_POST['adexp-day']);
		$post_adexpyr = sanitize_text_field($_POST['adexp-yr']);
		$post_countclicks = sanitize_text_field($_POST['countclicks']);
		$post_adimage = sanitize_text_field($_POST['adimage']);
		if ($post_countclicks=='on') { $post_countclicks = '0'; } else { $post_countclicks = '-1'; }
		$today = date('m').'/'.date('d').'/'.date('Y');
		if ($post_adexp=='manual') { $theenddate = '00/00/0000'; }
		if ($post_adexp=='other') { $theenddate = $post_adexpmo.'/'.$post_adexpday.'/'.$post_adexpyr; }
		if ($post_adexp=='30') { $expiry = time() + 30 * 24 * 60 * 60; $expiry = strftime('%m/%d/%Y', $expiry); $theenddate = $expiry; }
		if ($post_adexp=='60') { $expiry = time() + 60 * 24 * 60 * 60; $expiry = strftime('%m/%d/%Y', $expiry); $theenddate = $expiry; }
		if ($post_adexp=='90') { $expiry = time() + 90 * 24 * 60 * 60; $expiry = strftime('%m/%d/%Y', $expiry); $theenddate = $expiry; }
		if ($post_adexp=='120') { $expiry = time() + 120 * 24 * 60 * 60; $expiry = strftime('%m/%d/%Y', $expiry); $theenddate = $expiry; }
		if ($post_editedad!='') { $theenddate = $post_adexpmo.'/'.$post_adexpday.'/'.$post_adexpyr; }
		if ($post_editedad=='') {
			$updatedb = $wpdb->prepare(
				"INSERT INTO $adtable_name (slot, name, start_date, end_date, clicks, status, target, image_url, pre_exp_email) VALUES (%d, %s, %s, %s, %d, %d, %s, %s, %d)",
				$post_adslot, $post_adname, $today, $theenddate, $post_countclicks, 1, $post_adtarget, $post_adimage, 0
			);
			$results = $wpdb->query($updatedb);
			echo '<div id="message" class="updated fade"><p>Ad &quot;'.$post_adname.'&quot; created.</p></div>';
		} else {
			$updatedb = $wpdb->prepare(
				"UPDATE $adtable_name SET slot = %d, name = %s, end_date = %s, target = %s, image_url = %s, pre_exp_email = '0' WHERE id=%d",
				$post_adslot, $post_adname, $theenddate, $post_adtarget, $post_adimage, $post_editedad
			);
			$results = $wpdb->query($updatedb);
			echo '<div id="message" class="updated fade"><p>'.__('Ad', 'wp125').' &quot;'.$post_adname.'&quot; '.__('updated.', 'wp125').'</p></div>';
		}
	}
	if ($_POST['deletead']) {
		$post_editedad = sanitize_text_field($_POST['editedad']);
		$nonce = wp_create_nonce('nonce_wp125_deletead'.$post_editedad);
		echo '<div id="message" class="updated fade"><p>'.__('Do you really want to delete this ad record? This action cannot be undone.', 'wp125').' <a href="'.esc_url('admin.php?page=wp125_addedit&deletead='.$post_editedad.'&nonce_wp125_deletead='.$nonce).'">'.__('Yes', 'wp125').'</a> &nbsp; <a href="'.esc_url('admin.php?page=wp125_addedit&editad='.$post_editedad).'">'.__('No!', 'wp125').'</a></p></div>';
	}
	if ($_GET['deletead']!='') {
		$thead = intval($_GET['deletead']);
		if (wp_verify_nonce($_GET['nonce_wp125_deletead'], 'nonce_wp125_deletead'.$thead)) {
			$updatedb = $wpdb->prepare("DELETE FROM $adtable_name WHERE id=%d", $thead);
			$results = $wpdb->query($updatedb);
			echo '<div id="message" class="updated fade"><p>'.__('Ad deleted.', 'wp125').'</p></div>';
		}
	}
	?>

	<h2><?php _e('Add/Edit Ads', 'wp125'); ?></h2>

	<form method="post" action="admin.php?page=wp125_addedit">
		<?php wp_nonce_field('wp125_addedit', 'nonce_wp125_addedit'); ?>
		<table class="form-table">

			<?php if (isset($_GET['editad']) && $_GET['editad']!='') { echo '<input name="editedad" type="hidden" value="'.esc_attr(intval($_GET['editad'])).'" />'; } ?>

			<tr valign="top">
				<th scope="row"><?php _e('Name', 'wp125'); ?></th>
				<td><input name="adname" type="text" id="adname" value="<?php echo esc_attr($editingad->name); ?>" size="40" /><br/><?php _e('Whose ad is this?', 'wp125'); ?></td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e('Slot', 'wp125'); ?></th>
				<td><label for="adslot">
					<select name="adslot" id="adslot">
						<?php for ($count = 1; $count <= $setting_num_slots; $count += 1) { ?>
						<option value="<?php echo $count; ?>" <?php if ($count == $editingad->slot) { echo 'selected="selected"'; } ?>>#<?php echo $count; ?></option>
						<?php } ?>
					</select></label>
				</td></tr>

				<tr valign="top">
					<th scope="row"><?php _e('Target URL', 'wp125'); ?></th>
					<td><input name="adtarget" type="text" id="adtarget" value="<?php if (isset($editingad->target) && $editingad->target!='') { echo esc_attr($editingad->target); } else { echo 'http://'; } ?>" size="40" /><br/><?php _e('Where should the ad link to?', 'wp125'); ?></td>
				</tr>

				<?php if (isset($_GET['editad']) && $_GET['editad']!='') {
					$enddate = $editingad->end_date;
					if ($enddate != '00/00/0000') {
						$enddate = strtotime($enddate);
						$endmonth = date('m', $enddate);
						$endday = date('d', $enddate);
						$endyear = date('Y', $enddate);
					} else { $endmonth='00'; $endday='00'; $endyear='0000'; }
				} ?>
				<tr valign="top">
					<th scope="row"><?php _e('Expiration', 'wp125'); ?></th>
					<td><label for="adexp">
						<?php if ($_GET['editad']=='') { ?><select name="adexp" id="adexp" onChange="isOtherDate(this.value)">
						<option value="manual"><?php _e("I'll remove it manually", 'wp125'); ?></option>
						<option selected="selected" value="30">30 <?php _e('Days', 'wp125'); ?></option>
						<option value="60">60 <?php _e('Days', 'wp125'); ?></option>
						<option value="90">90 <?php _e('Days', 'wp125'); ?></option>
						<option value="120">120 <?php _e('Days', 'wp125'); ?></option>
						<option value="other"><?php _e('Other', 'wp125'); ?></option>
					</select><?php } ?></label>
					<span id="adexp-date">&nbsp;&nbsp; <?php _e('Month:', 'wp125'); ?> <input type="text" name="adexp-mo" id="adexp-mo" size="2" value="<?php if ($endmonth!='') { echo esc_attr($endmonth); } else { echo date('m'); } ?>" /> <?php _e('Day:', 'wp125'); ?> <input type="text" name="adexp-day" id="adexp-day" size="2" value="<?php if ($endday!='') { echo esc_attr($endday); } else {  echo date('d'); } ?>" /> <?php _e('Year:', 'wp125'); ?> <input type="text" name="adexp-yr" id="adexp-yr" size="4" value="<?php if ($endyear!='') { echo esc_attr($endyear); } else {  echo date('Y'); } ?>" /> <?php if ($_GET['editad']!='') { ?><br /> &nbsp;&nbsp; <?php _e('Use 00/00/0000 for manual removal.', 'wp125'); ?><?php } ?></span>
				</td></tr>

				<?php if ($_GET['editad']=='') { ?><script type="text/javascript">
				document.getElementById("adexp-date").style.display = "none";
				function isOtherDate(obj) {
					if (obj=="other") {
						document.getElementById("adexp-date").style.display = "inline";
					} else {
						document.getElementById("adexp-date").style.display = "none";
					}
				}
			</script><?php } ?>

			<?php if ($_GET['editad']=='') { ?>
			<tr valign="top">
				<th scope="row"><?php _e('Click Tracking', 'wp125'); ?></th>
				<td><input type="checkbox" name="countclicks" checked="checked" /> <?php _e('Count the number of times this ad is clicked', 'wp125'); ?></td>
			</tr>
			<?php } ?>

			<tr valign="top">
				<th scope="row"><?php _e('Ad Image', 'wp125'); ?></th>
				<td><input name="adimage" type="text" id="adimage" value="<?php if ($editingad->image_url!='') { echo esc_attr($editingad->image_url); } else { echo 'http://'; } ?>" size="40" /> <input id="upload_image_button" type="button" class="button" value="Upload Image" /></td>
			</tr>

			<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery('#upload_image_button').click(function() {
					formfield = jQuery('#adimage').attr('name');
					tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
					return false;
				});

				window.send_to_editor = function(html) {
					imgurl = jQuery('img',html).attr('src');
					jQuery('#adimage').val(imgurl);
					tb_remove();
				}
			});
			</script>

		</table>
		<p class="submit"><input type="submit" name="Submit" class="button" value="<?php _e('Save Ad', 'wp125'); ?>" /> &nbsp; <?php if ($_GET['editad']!='') { ?><input type="submit" name="deletead" value="<?php _e('Delete Ad', 'wp125'); ?>" /><?php } ?></p>
	</form>
	<?php wp125_admin_page_footer(); ?>
	</div><?php
}

function wp125_write_settingsmenu() {
	//DB Data
	global $wpdb;
	//Add settings, if submitted
	if ($_POST['issubmitted']=='yes' && wp_verify_nonce($_POST['nonce_wp125_settings'],'wp125_settings')) {
		$post_adorient = sanitize_text_field($_POST['adorient']);
		$post_numslots = sanitize_text_field($_POST['numads']);
		$post_adorder = sanitize_text_field($_POST['adorder']);
		$post_salespage = sanitize_text_field($_POST['salespage']);
		$post_widgettitle = sanitize_text_field($_POST['widgettitle']);
		$post_defaultstyle = sanitize_text_field($_POST['defaultstyle']);
		$post_emailonexp = sanitize_text_field($_POST['emailonexp']);
		$post_daysbeforeexp = sanitize_text_field($_POST['daysbeforeexp']);
		$post_defaultad = sanitize_text_field($_POST['defaultad']);
		if ($post_defaultstyle!='on') { $post_defaultstyle = 'yes'; } else { $post_defaultstyle = ''; }
		update_option("wp125_ad_orientation", $post_adorient);
		update_option("wp125_num_slots", $post_numslots);
		update_option("wp125_ad_order", $post_adorder);
		update_option("wp125_buyad_url", $post_salespage);
		update_option("wp125_disable_default_style", $post_defaultstyle);
		update_option("wp125_emailonexp", $post_emailonexp);
		update_option("wp125_daysbeforeexp", $post_daysbeforeexp);
		update_option("wp125_defaultad", $post_defaultad);
		echo '<div id="message" class="updated fade"><p>Settings updated.</p></div>';
	}
	//Retrieve settings
	$setting_ad_orientation = get_option("wp125_ad_orientation");
	$setting_num_slots = get_option("wp125_num_slots");
	$setting_ad_order = get_option("wp125_ad_order");
	$setting_buyad_url = get_option("wp125_buyad_url");
	$setting_disable_default_style = get_option("wp125_disable_default_style");
	$setting_emailonexp = get_option("wp125_emailonexp");
	$setting_defaultad = get_option("wp125_defaultad");
	$setting_daysbeforeexp = get_option("wp125_daysbeforeexp");
	?>
	<div class="wrap">
	<h2><?php _e('Settings', 'wp125'); ?></h2>
	<form method="post" action="admin.php?page=wp125_settings">
	<?php wp_nonce_field('wp125_settings', 'nonce_wp125_settings'); ?>
		<table class="form-table">

			<tr valign="top">
				<th scope="row"><?php _e('Ad Orientation', 'wp125'); ?></th>
				<td><label for="adorient">
					<select name="adorient" id="adorient">
						<option <?php if ($setting_ad_orientation=='1c') { echo 'selected="selected"'; } ?> value="1c"><?php _e('One Column', 'wp125'); ?></option>
						<option <?php if ($setting_ad_orientation=='2c') { echo 'selected="selected"'; } ?> value="2c"><?php _e('Two Column', 'wp125'); ?></option>
					</select></label>
					<br/><?php _e('How many columns should the ads be displayed in?', 'wp125'); ?>
				</td></tr>

				<tr valign="top">
					<th scope="row"><?php _e('Number of Ad Slots', 'wp125'); ?></th>
					<td><input name="numads" type="text" id="numads" value="<?php echo esc_attr($setting_num_slots); ?>" size="2" /><br/><?php _e('How many ads should be shown?', 'wp125'); ?></td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php _e('Ad Order', 'wp125'); ?></th>
					<td><label for="adorder">
						<select name="adorder" id="adorder">
							<option selected="selected" value="normal" <?php if ($setting_ad_order=='normal') { echo 'selected="selected"'; } ?>><?php _e('Normal', 'wp125'); ?></option>
							<option value="random" <?php if ($setting_ad_order=='random') { echo 'selected="selected"'; } ?>><?php _e('Random', 'wp125'); ?></option>
						</select></label>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php _e('Widget Title', 'wp125'); ?></th>
					<td><input name="widgettitle" type="text" id="widgettitle" value="<?php echo esc_attr($setting_widget_title); ?>" size="50" /><br/><?php _e('The title to be displayed in the widget.', 'wp125'); ?> <em><?php _e('(Leave blank to disable.)', 'wp125'); ?></em></td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php _e('Ad Sales Page', 'wp125'); ?></th>
					<td><input name="salespage" type="text" id="salespage" value="<?php echo esc_attr($setting_buyad_url); ?>" size="50" /><br/><?php _e('Do you have a page with statistics and prices?', 'wp125'); ?> <em><?php _e('(Default Ads will link here.)', 'wp125'); ?></em></td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php _e('Default Style', 'wp125'); ?></th>
					<td><input type="checkbox" name="defaultstyle" <?php if ($setting_disable_default_style=='') { echo 'checked="checked"'; } ?> /> <?php _e('Include default ad stylesheet?', 'wp125'); ?> <br/><?php _e('Leave checked unless you want to use your own CSS to style the ads. Refer to the documentation for further help.', 'wp125'); ?></td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php _e('Expiration Email', 'wp125'); ?></th>
					<td><input name="emailonexp" type="text" id="emailonexp" value="<?php echo esc_attr($setting_emailonexp); ?>" size="50" /><br/><?php _e('Enter your email address if you would like to be emailed when an ad expires.', 'wp125'); ?> <em><?php _e('(Leave blank to disable.)', 'wp125'); ?></em></td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php _e('Pre-Expiration Email', 'wp125'); ?></th>
					<td><?php _e('Remind me', 'wp125'); ?> <input name="daysbeforeexp" type="text" id="daysbeforeexp" value="<?php echo esc_attr($setting_daysbeforeexp); ?>" size="2" /> <?php _e('days before an ad expires.', 'wp125'); ?> <em><?php _e('(Emails will be sent to the address specified above.)', 'wp125'); ?></em></td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php _e('Default Ad', 'wp125'); ?></th>
					<td><input name="defaultad" type="text" id="defaultad" value="<?php echo esc_attr($setting_defaultad); ?>" size="50" /><br/><?php _e('Which image should be shown as a placeholder when an ad slot is empty?', 'wp125'); ?> (<a href="<?php echo wp125_get_plugin_dir('url').'/youradhere.jpg'; ?>"><?php _e('Default', 'wp125'); ?></a>)</td>
				</tr>

			</table>
		<input name="issubmitted" type="hidden" value="yes" />
		<p class="submit"><input type="submit" class="button" name="Submit" value="<?php _e('Save Changes', 'wp125'); ?>" /></p>
		</form>
		<br/>
		<p><?php _e("Your ads can be displayed using either the included widget, or by using the <strong>&lt;?php wp125_write_ads();  ?&gt;</strong> template tag. Also, you can display a single ad, without any formatting, using <strong>&lt;?php wp125_single_ad(<em>num</em>);  ?&gt;</strong>, where <em>num</em> is the number of the ad slot you wish to show. This is useful for cases where your theme prevents the default formatting from working properly, or where you wish to display your ads in an unforeseen manner.", 'wp125'); ?></p>
		<?php wp125_admin_page_footer(); ?>
		</div><?php
}



//Add Dashboard Widget
function wp125_dashboard_widget() {
	echo '<table class="widefat">
	<thead><tr>
	<th scope="col">'.__('Slot', 'wp125').'</th>
	<th scope="col">'.__('Name', 'wp125').'</th>
	<th scope="col" class="num">'.__('Clicks', 'wp125').'</th>
	<th scope="col">'.__('Start Date', 'wp125').'</th>
	<th scope="col">'.__('End Date', 'wp125').'</th>
	</tr></thead>
	<tbody>';
	global $wpdb;
	$adtable_name = $wpdb->prefix . "wp125_ads";
	$wp125db = $wpdb->get_results("SELECT * FROM $adtable_name WHERE status != '0' ORDER BY id DESC", OBJECT);
	if ($wp125db) {
	foreach ($wp125db as $wp125db){
	?>
	<tr><td><?php echo esc_html($wp125db->slot); ?></td><td><strong><?php echo esc_html($wp125db->name); ?></strong></td><td class="num"><?php echo esc_html($wp125db->clicks); ?></td><td><?php echo esc_html($wp125db->start_date); ?></td><td><?php echo esc_html($wp125db->end_date); ?></td></tr>
	<?php
	}
	} else { echo '<tr> <td colspan="8">'.__('No ads found.', 'wp125').'</td> </tr>'; }
	echo '</tbody>
	</table>
	<br />';
	echo '<a href="admin.php?page=wp125_addedit" class="button rbutton">'.__('Add New', 'wp125').'</a> &nbsp; <a href="admin.php?page=wp125/wp125.php" class="button rbutton">'.__('Manage', 'wp125').'</a> &nbsp; <a href="admin.php?page=wp125_settings" class="button rbutton">'.__('Settings', 'wp125').'</a>';
}
function wp125_dashboard_add_widget() {
	if (current_user_can(MANAGEMENT_PERMISSION)) {
		if (function_exists('wp_add_dashboard_widget')) {
			wp_add_dashboard_widget('wp125_widget', __('125x125 Ads', 'wp125'), 'wp125_dashboard_widget');
		}
	}
}
add_action('wp_dashboard_setup', 'wp125_dashboard_add_widget' );



function wp125_admin_page_footer() {
echo '<div style="margin-top:45px; font-size:0.87em;">';
echo '<div style="float:right;"><a href="http://www.webmaster-source.com/static/donate_plugin.php?plugin=wp125&amp;KeepThis=true&amp;TB_iframe=true&amp;height=250&amp;width=550" class="thickbox" title="Donate"><img src="https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif" alt="Donate" /></a></div>';
echo '<div><a href="'.wp125_get_plugin_dir('url').'/readme.txt?KeepThis=true&amp;TB_iframe=true&amp;height=450&amp;width=680" class="thickbox" title="Documentation">'.__('Documentation', 'wp125').'</a> | <a href="http://www.webmaster-source.com/wp125-ad-plugin-wordpress/">'.__('WP125 Homepage', 'wp125').'</a></div>';
echo '</div>';
}

?>