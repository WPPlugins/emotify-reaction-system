<?php
/*
Plugin Name: Emotify Reaction Plugin
Plugin URI: https://goemotify.com
Description: Emotify is a smart reaction and re-engagement tool for web publishers to capture diverse audience emotions, engage them and add emotional intelligence to the website.
Version: 2.0.2
Author: emotify
*/


/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
*/


add_action("init" , "emotify_save_options");

function emotify_save_options() {


	if(isset($_POST["emotify_save"])){
		$emo_nonce_key =  $_POST['emotify_nonce'];

		if ( ! current_user_can( 'administrator' ) ) return;

		if (wp_verify_nonce( $emo_nonce_key, 'emotify' ) ) {

	    	if(isset($_POST["emotify_key"])){

				$value = sanitize_key($_POST["emotify_key"]);

				$result	= update_option('emotify_key',$value);
			}


			$value_check = "";
			if(isset($_POST["emotify_check_box"])) {
				$value_check = $_POST["emotify_check_box"];
			}
			update_option('emotify_check_box',$value_check);
		}
	}

}

add_action('admin_menu','emotify_create_menu');

function emotify_create_menu(){
	//create new top-level menu
	add_menu_page('Emotify Settings', 'Emotify Settings', 'administrator', "emotify_page" , "emotify_settings_page",  plugins_url( 'emotify_icon.png' , __FILE__));
}


function emotify_settings_page(){

	$checked = get_option('emotify_check_box');

	global $result;

	?>
		<div class="wrap">
			<div class="clearfix emo_heading">
				<div class="emotify_img_icon">
					<img src="<?php echo plugins_url('emotify40.png', __FILE__ ); ?>" alt="">
				</div>
				<div class="emotify_head">
					<h1>Emotify Settings</h1>
				</div>
				<!-- <div style="clear:both"></div> -->
			</div>
			<div class="saved_nav">
			<?php
			if(isset($_POST["emotify_save"])){

				$emo_nonce_key =  $_POST['emotify_nonce'];
				if (wp_verify_nonce( $emo_nonce_key, 'emotify' ) && current_user_can( 'administrator' )) {
				 	echo "<div class='emotify_updated' id='setting-error-settings_updated'><strong>Settings saved successfully.</strong></div>
				 	<button class='notice-dismiss cancel-btn emotify_cancel_btn' type='button'>
						<span class='screen-reader-text'>Dismiss this notice.</span>
					</button>";
				}
			}
			?>
			</div>
			<form method="POST" action ="" style="margin-top:49px;" id="emotify-form">
				<input type="hidden" name="emotify_nonce" value="<?php echo wp_create_nonce( 'emotify' ); ?>"/>
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row">
								<label>Enable widget:</label>
							</th>
							<td>
								<input type ="checkbox" name="emotify_check_box" value ="checked"<?php if ('checked' == $checked) echo 'checked = "checked"';?> style="" id="ckecked"/>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label>Emotify key:</label>
							</th>
							<td>
								<input type="text" name="emotify_key" class="regular-text" value="<?php echo esc_attr( get_option('emotify_key') );?>" style="" id="inpt"<?php if (!'checked' == $checked) echo 'disabled = "disabled"';?>><br>
								<p id="tagline-description" class="description">Don’t have the API key yet! <a target="_blank" href="http://www.goemotify.com/dashboard">Get it from Emotify.</a></p>
							</td>
						</tr>
					<tbody/>
				</table>
					<input type="submit" name="emotify_save" class="button emotify_btn" value="Save" style="box-sizing: initial; margin-top:30px; padding:3px 16px;">
			</form>
		</div>
		<div class="hr">
			<hr/>
					<a class="button button-primary" style="box-sizing: initial; margin-top:30px; padding:3px 16px;" target="_blank" href="http://www.goemotify.com/dashboard">Check out website emotion insights</a>
		</div>
		<div class="wrap">
			<div class="links_head">
				<h1>Useful Links</h1>
			</div>
			<ul class="useful_links">
				<li class="links_url link1"><a target="_blank" href"https://wordpress.org/plugins/emotify-reaction-system/installation/">View install instruction.</a></li>
				<li class="links_url link2"><a target="_blank" href="http://www.goemotify.com/register/new">Create Emotify account.</a></li>
				<li class="links_url link3"><a target="_blank" href="http://www.goemotify.com/dashboard">View Emotify analytics.</a></li>
				<li class="links_url link4"><a target="_blank" href="http://support.goemotify.com/">Got to support page.</a></li>
			</ul>
		</div>

		<script type="text/javascript">
			jQuery(document).ready(function(){
				 jQuery("input#ckecked").click(function(){
				 var value = jQuery(this).prop('checked');
				 if(value == false){
					jQuery("input#inpt").attr('disabled',true);
				}
				if(value == true){
					jQuery("input#inpt").prop('disabled',false);
				}
				});

				jQuery('.cancel-btn').click(function(){
				 	jQuery('.emotify_updated').css("display", "none");
				 	jQuery('.cancel-btn').css("display", "none");
				})
			});

			jQuery("#emotify-form").submit(function() {
			  if(jQuery("#ckecked").prop("checked")) {
			    var val = jQuery("#inpt").val().trim();
			    if(val == "") {
			      alert("Please enter Emotify Key");
			      return false;
			    }
			  }
			});
		</script>
<?php

}



add_filter('the_content', 'emotify_add_text_the_post');

function emotify_add_text_the_post($content){

	$code = "";

	$key_value = get_option('emotify_key');
	$emotify_check_box = get_option('emotify_check_box');

	if( trim($key_value) != '' && !empty($emotify_check_box) ) {

		if(is_single()){

			$code = '<div id="emotify-container"></div>
						<script type="text/javascript">
		  				var el = document.createElement("script");
		 				var url = window.location.href;
		  				var head = document.getElementsByTagName("head")[0];
		  				el.src = "//www.goemotify.com/api/2.0/reactions?url="+encodeURIComponent(url)+"&apikey=';
			$code .= get_option("emotify_key");
			$code .= 	'";el.type = "text/javascript";
		  				head.appendChild(el);
						</script>';

		}

	}

	return $content . $code;

}
include('emotify_functions.php');
