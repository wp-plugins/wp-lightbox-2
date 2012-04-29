<?php
/*
 * Plugin Name: WP Lightbox 2
 * Plugin URI: http://onlinewebapplication.com/2011/11/wp-lightbox-2.html
 * Description: This plugin used to add the lightbox (overlay) effect to the current page images on your WordPress blog.
 * Version:       2.0
 * Author:        Pankaj Jha
 * Author URI:    http://onlinewebapplication.com/
 * License:       GNU General Public License, v2 (or newer)
 * License URI:  http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */
 /*  Copyright 2011 Pankaj Jha (onlinewebapplication.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation using version 2 of the License.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/* Where our theme reside: */
$lightbox_2_theme_path = (dirname(__FILE__)."/Themes");
update_option('lightbox_2_theme_path', $lightbox_2_theme_path);
/* Set the default theme to Black */
add_option('lightbox_2_theme', 'Black');
add_option('lightbox_2_automate', 1);
add_option('lightbox_2_resize_on_demand', 0);

/* use WP_PLUGIN_URL if version of WP >= 2.6.0. If earlier, use wp_url */
if($wp_version >= '2.6.0') {
	$stimuli_lightbox_plugin_prefix = WP_PLUGIN_URL."/wp-lightbox-2/"; /* plugins dir can be anywhere after WP2.6 */
} else {
	$stimuli_lightbox_plugin_prefix = get_bloginfo('wpurl')."/wp-content/plugins/wp-lightbox-2/";
}

/* options page (required for saving prefs)*/
$options_page = get_option('siteurl') . '/wp-admin/admin.php?page=wp-lightbox-2/options.php';
/* Adds our admin options under "Options" */
function lightbox_2_options_page() {
	add_options_page('Lightbox Options', 'WP Lightbox 2', 10, 'wp-lightbox-2/options.php');
}

function lightbox_styles() {
	/* What version of WP is running? */
	global $wp_version;
	global $stimuli_lightbox_plugin_prefix;
    /* The next line figures out where the javascripts and images and CSS are installed,
    relative to your wordpress server's root: */
    $lightbox_2_theme = urldecode(get_option('lightbox_2_theme'));
    $lightbox_style = ($stimuli_lightbox_plugin_prefix."Themes/".$lightbox_2_theme."/");

    /* The xhtml header code needed for lightbox to work: */
	$lightboxscript = "
	<!-- begin lightbox scripts -->
	<script type=\"text/javascript\">
    //<![CDATA[
    document.write('<link rel=\"stylesheet\" href=\"".$lightbox_style."lightbox.css\" type=\"text/css\" media=\"screen\" />');
    //]]>
    </script>
	<!-- end lightbox scripts -->\n";
	/* Output $lightboxscript as text for our web pages: */
	echo($lightboxscript);
}

/* Added a code to automatically insert rel="lightbox[nameofpost]" to every image with no manual work. 
If there are already rel="lightbox[something]" attributes, they are not clobbered. 
Michael Tyson, you are a regular expressions god! ;) 
http://atastypixel.com
*/
function autoexpand_rel_wlightbox ($content) {
	global $post;
	$pattern        = "/(<a(?![^>]*?rel=['\"]lightbox.*)[^>]*?href=['\"][^'\"]+?\.(?:bmp|gif|jpg|jpeg|png)['\"][^\>]*)>/i";
	$replacement    = '$1 rel="lightbox['.$post->ID.']">';
	$content = preg_replace($pattern, $replacement, $content);
	return $content;
}

if (get_option('lightbox_2_automate') == 1){
	add_filter('the_content', 'autoexpand_rel_wlightbox', 99);
	add_filter('the_excerpt', 'autoexpand_rel_wlightbox', 99);
}

/* To resize images, or not to resize; that is the question */
$resize_images_or_not = get_option('lightbox_2_resize_on_demand');
if ($resize_images_or_not == 1) {
	$stimuli_lightbox_js = "lightbox-resize.js"; 
} else {
	$stimuli_lightbox_js = "lightbox.js"; 
}

if (!is_admin()) { // if we are *not* viewing an admin page, like writing a post or making a page:
	wp_enqueue_script('lightbox', ($stimuli_lightbox_plugin_prefix.$stimuli_lightbox_js), array('scriptaculous-effects'), '1.8');
}

/* we want to add the above xhtml to the header of our pages: */
add_action('wp_head', 'lightbox_styles');
add_action('admin_menu', 'lightbox_2_options_page');
?>
