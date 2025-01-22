<?php
/**
 * The Donor List Markup
 *
 * @package KJR_Dev
 */

$donor_names     = $args['donor_names'];
$list_id         = $args['list_id'] ?? 'donor-list';
$is_multi_column = $args['multi_column'] ?? false;
?>
<ul class="d-grid gap-3 ps-0 my-0" id="<?php echo esc_attr( $list_id ); ?>">
	<?php
	$i         = $is_multi_column ? 1 : 0;
	$list_size = count( $donor_names );
	?>
	<?php for ( $i; $i < $list_size; $i++ ) : ?>
		<?php
		$details = array();
		if ( $is_multi_column ) {
			$name                = $donor_names[ $i ][0];
			$details['employee'] = 'x' === sanitize_text_field( strtolower( $donor_names[ $i ][1] ) );
			$details['deceased'] = 'x' === sanitize_text_field( strtolower( $donor_names[ $i ][2] ) );
		} else {
			$name = $donor_names[ $i ];
		}
		?>
		<li id="<?php echo esc_html( sanitize_title( $name ) ); ?>">
			<?php
			echo '<span>' . esc_html( $name ) . '</span>';
			if ( $is_multi_column ) {
				if ( $details['employee'] ) {
					echo '<span class="name-details mx-1" title="Employee">+<span class="visually-hidden">Employee</span></span>';
				}
				if ( $details['deceased'] ) {
					echo '<span class="name-details mx-1" title="Deceased">◊<span class="visually-hidden">Deceased</span></span>';
				}
			}
			?>
			 
		</li>
	<?php endfor; ?>
</ul>
