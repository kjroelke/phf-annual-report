<?php
/**
 * Donor Template: Aside Name Search
 *
 * @package KJR_Dev
 */

// TODO: Add a reset button if $_GET['name'] is set.

?>
<aside class="container">
	<form action="<?php echo esc_url( site_url( "/donors/{$post->post_name}" ) ); ?>" class="w-100 d-flex gap-3" method="get" id="donor-lookup-form">
		<input type="text" name="name" id="name-lookup" class="border border-2 border-primary flex-grow-1" placeholder="Search for your name"/>
		<button type="submit" class="btn btn-primary text-white text-hover--primary">Search</button>
	</form>
</aside>
