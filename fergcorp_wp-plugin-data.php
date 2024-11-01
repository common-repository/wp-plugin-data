<?php
/*
Plugin Name: WP Plugin Data
Plugin URI: http://andrewferguson.net/
Description: Provides abstracted data about plugins. Based on <a href="http://wordpress.org/extend/plugins/plugin-info/">Plugin Info</a> by John Blackbourn.
Version: 0.5
Author: Andrew Ferguson
Author URI: http://andrewferguson.net


WP Plugin Data - Provides abstracted data about plugins
Copyright (c) 2009 Andrew Ferguson

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


/**
 * Returns the data for a specific plugin
 *
 * @param $slug string The slug name of the plugin
 * @since 0.1
 * @access public
 * @author Andrew Ferguson
 * @return Object Plugin Object
*/
function fergcorp_wppd_getPluginData($slug){
	require_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
	
	$slug   = sanitize_title( $slug );
	$plugin = plugins_api( 'plugin_information', array( 'slug' => $slug ) );
	
	return $plugin;
}

/**
 * Returns the requested data of a particular plugin
 *
 * @param $slug string The slug name of the plugin
 * @param $param The requested data for the plugin
 * @param $content If returning a URL, the link text for the URL
 * @since 0.1
 * @access public
 * @author Andrew Ferguson
 * @return string Requested data of a particular plugin
*/
function fergcorp_wppd_getInfo($slug, $param, $content = NULL){

	$plugin = fergcorp_wppd_checkPlugin($slug);
	$info   = array();
	$attributes = array(
		'name'             => 'name',
		'slug'             => 'slug',
		'version'          => 'version',
		'author'           => 'author',
		'requires'         => 'requires',
		'tested'           => 'tested',
		'rating_raw'       => 'rating',
		'downloaded_raw'   => 'downloaded',
		'last_updated_raw' => 'last_updated',
		'num_ratings'      => 'num_ratings',
		'description'      => array( 'sections', 'description' ),
		'faq'              => array( 'sections', 'faq' ),
		'installation'     => array( 'sections', 'installation' ),
		'screenshots'      => array( 'sections', 'screenshots' ), # awaiting API support
		#'other_notes'      => array( 'sections', 'other_notes' ), # awaiting API support
		'download_url'     => 'download_link',
		'homepage_url'     => 'homepage',
		'tags'             => 'tags'
	);
	
	foreach ( $attributes as $name => $key ) {
	
		if(is_array($key)){
			$_key = $plugin->$key[0];
			$info[$name] = $_key[$key[1]];
		}else{
			$info[$name] = $plugin->$key;
		}
		
		if(is_array($info[$name])){
			$info[$name] = implode( ', ', $info[$name] );
		}
	}

		$info['downloaded']       = number_format( $info['downloaded_raw'] );
		$info['rating']           = ceil( 0.05 * $info['rating_raw'] );
		$info['link_url']         = "http://wordpress.org/extend/plugins/{$info['slug']}/";
		$info['last_updated']     = date( get_option('date_format'), strtotime( $info['last_updated_raw'] ) );
		$info['last_updated_ago'] = sprintf( __('%s ago'), human_time_diff( strtotime( $info['last_updated_raw'] ) ) );
		$info['download']         = '<a href="' . $info['download_url'] . '">' . $content . '</a>';
		$info['homepage']         = '<a href="' . $info['homepage_url'] . '">' . $content . '</a>';
		$info['link']             = '<a href="' . $info['link_url']   . '">' . $content . '</a>';
		$info['screenshots']      = preg_replace( "/src='([^\']+)'/i","src='{$info['link_url']}$1'", $info['screenshots'] ); # awaiting API support

		if ( preg_match( '/href="([^"]+)"/i', $info['author'], $matches ) )
			$info['author_url'] = $matches[1];

		if ( preg_match( '/>([^<]+)</i', $info['author'], $matches ) )
			$info['author_name'] = $matches[1];
		else
			$info['author_name'] = $info['author'];
	
	return $info[$param];
	

}

/**
 * Processes the wppd shortcode
 *
 * @param $atts Attributes provided in the shortcode
 * @param $content Content provided between the opening and closing elements
 * @since 0.1
 * @access public
 * @author Andrew Ferguson
 * @return string The result of fergcorp_wppd_getInfo
*/
function fergcorp_wppd_shortcode($atts, $content=NULL) {
	extract(shortcode_atts(array(
		0 => 'countdown-timer',
		1 => 'name',
	), $atts));
	
	$content = do_shortcode($content);
	
	return fergcorp_wppd_getInfo($atts[0], $atts[1], $content);
}

add_shortcode('wppd', 'fergcorp_wppd_shortcode');
add_shortcode('wppdlink', 'fergcorp_wppd_shortcode');


/**
 * Processes the wppd shortcode
 *
 * @param $slug string The slug name of the plugin
 * @since 0.1
 * @access public
 * @author Andrew Ferguson
 * @return Object Plugin Object
*/
function fergcorp_wppd_checkPlugin($slug){

	$fergcorp_wppd_cache = get_option('fergcorp_wppd_cache');
	//If the cache is not found
	if(!$fergcorp_wppd_cache){
		$fergcorp_wppd_cache = array();
		$fergcorp_wppd_cache[$slug] = fergcorp_wppd_getPluginData($slug);
		$fergcorp_wppd_cache[$slug.'-fetchTime'] = time();
		update_option('fergcorp_wppd_cache', $fergcorp_wppd_cache);
		return $fergcorp_wppd_cache[$slug];
	}
	//If the cache is found, and the slug exists in the cache
	elseif(array_key_exists($slug, $fergcorp_wppd_cache)){
		//If the slug in the cache is over 3600 seconds (1 hour) old, get a new copy.
		if($fergcorp_wppd_cache[$slug.'-fetchTime'] < (time()-3600)){
			$fergcorp_wppd_cache[$slug] = fergcorp_wppd_getPluginData($slug);
			$fergcorp_wppd_cache[$slug.'-fetchTime'] = time();
			update_option('fergcorp_wppd_cache', $fergcorp_wppd_cache);
			return $fergcorp_wppd_cache[$slug];
		}	

		return $fergcorp_wppd_cache[$slug];
	}
	//If the cache is found, but the slug was not in the cache
	else{
		$fergcorp_wppd_cache[$slug] = fergcorp_wppd_getPluginData($slug);
		$fergcorp_wppd_cache[$slug.'-fetchTime'] = time();
		update_option('fergcorp_wppd_cache', $fergcorp_wppd_cache);
		return $fergcorp_wppd_cache[$slug];
	}
}
?>