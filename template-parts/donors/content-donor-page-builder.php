<?php
/**
 * The "page builder" for Donor pages
 *
 * @package KJR_Dev
 */

use KJR_Dev\CSV_Handler;

$sections = array(
	'header'      => 'section',
	'name-search' => 'aside',
	'donor-list'  => 'section',
);
foreach ( $sections as $template => $prefix ) {
	$args = array();
	if ( 'donor-list' === $template ) {
		$file_handler        = new CSV_Handler( get_the_ID() );
		$donor_names         = $file_handler->get_the_list();
		$args['donor_names'] = $donor_names;
	}
	get_template_part( "template-parts/donors/{$prefix}", $template, $args );
}
