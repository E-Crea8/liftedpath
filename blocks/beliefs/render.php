<?php
/**
 * Mission and vision columns.
 *
 * @package LiftedPath
 */

$rows       = liftedpath_block_rows( function_exists( 'get_field' ) ? get_field( 'items' ) : array() );
$is_preview = ! empty( $is_preview );
$items      = array();

foreach ( $rows as $row ) {
	if ( ! is_array( $row ) ) {
		continue;
	}

	$eyebrow = liftedpath_block_string( $row['eyebrow'] ?? '' );
	$heading = liftedpath_block_string( $row['heading'] ?? '' );
	$text    = liftedpath_block_string( $row['text'] ?? '' );

	if ( '' === $eyebrow && '' === $heading && '' === $text ) {
		continue;
	}

	$items[] = array(
		'eyebrow' => $eyebrow,
		'heading' => $heading,
		'text'    => $text,
	);
}

if ( array() === $items ) {
	if ( $is_preview ) {
		liftedpath_block_wrapper_open( 'section', 'liftedpath-section liftedpath-beliefs liftedpath-beliefs--empty' );
		liftedpath_block_placeholder( __( 'Add a mission or vision.', 'liftedpath' ) );
		liftedpath_block_wrapper_close( 'section' );
	}
	return;
}
?>
<?php liftedpath_block_wrapper_open( 'section', 'liftedpath-section liftedpath-beliefs' ); ?>
	<div class="liftedpath-section__inner liftedpath-beliefs__grid">
		<?php foreach ( $items as $item ) : ?>
			<div>
				<?php if ( '' !== $item['eyebrow'] ) : ?>
					<p class="liftedpath-eyebrow"><?php echo esc_html( $item['eyebrow'] ); ?></p>
				<?php endif; ?>
				<?php if ( '' !== $item['heading'] ) : ?>
					<h2><?php echo esc_html( $item['heading'] ); ?></h2>
				<?php endif; ?>
				<?php if ( '' !== $item['text'] ) : ?>
					<p><?php echo esc_html( $item['text'] ); ?></p>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
<?php liftedpath_block_wrapper_close( 'section' ); ?>
