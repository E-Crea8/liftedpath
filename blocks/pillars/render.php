<?php
/**
 * Numbered pillar cards.
 *
 * @package LiftedPath
 * @since 0.19.8
 */

$eyebrow    = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'eyebrow' ) : '' );
$heading    = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'heading' ) : '' );
$rows       = liftedpath_block_rows( function_exists( 'get_field' ) ? get_field( 'items' ) : array() );
$is_preview = ! empty( $is_preview );
$items      = array();

foreach ( $rows as $row ) {
	if ( ! is_array( $row ) ) {
		continue;
	}

	$title = liftedpath_block_string( $row['title'] ?? '' );
	$text  = liftedpath_block_string( $row['text'] ?? '' );

	if ( '' === $title && '' === $text ) {
		continue;
	}

	$items[] = array(
		'title' => $title,
		'text'  => $text,
	);
}

$empty = '' === $heading && '' === $eyebrow && array() === $items;

if ( $empty ) {
	if ( $is_preview ) {
		liftedpath_block_wrapper_open( 'section', 'liftedpath-section liftedpath-pillars liftedpath-pillars--empty' );
		liftedpath_block_placeholder( __( 'Add a heading and pillars.', 'liftedpath' ) );
		liftedpath_block_wrapper_close( 'section' );
	}
	return;
}
?>
<?php liftedpath_block_wrapper_open( 'section', 'liftedpath-section liftedpath-pillars' ); ?>
	<div class="liftedpath-section__inner">
		<?php if ( '' !== $eyebrow || '' !== $heading ) : ?>
			<div class="liftedpath-pillars__heading">
				<?php if ( '' !== $eyebrow ) : ?>
					<p class="liftedpath-eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
				<?php endif; ?>
				<?php if ( '' !== $heading ) : ?>
					<h2><?php echo esc_html( $heading ); ?></h2>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<?php if ( array() !== $items ) : ?>
			<ol class="liftedpath-pillars__grid">
				<?php foreach ( $items as $index => $item ) : ?>
					<li>
						<span class="liftedpath-pillars__index"><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></span>
						<?php if ( '' !== $item['title'] ) : ?>
							<h3><?php echo esc_html( $item['title'] ); ?></h3>
						<?php endif; ?>
						<?php if ( '' !== $item['text'] ) : ?>
							<p><?php echo esc_html( $item['text'] ); ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php endif; ?>
	</div>
<?php liftedpath_block_wrapper_close( 'section' ); ?>
