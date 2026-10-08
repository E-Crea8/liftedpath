<?php
/**
 * Stewardship points beside a photograph.
 *
 * @package LiftedPath
 */

$eyebrow    = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'eyebrow' ) : '' );
$heading    = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'heading' ) : '' );
$intro      = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'intro' ) : '' );
$note_label = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'note_label' ) : '' );
$button     = liftedpath_block_button( function_exists( 'get_field' ) ? get_field( 'button' ) : null );
$image      = liftedpath_block_image( function_exists( 'get_field' ) ? get_field( 'image' ) : null, 'liftedpath-portrait', 'liftedpath-stewardship__photo' );
$inset      = liftedpath_block_image( function_exists( 'get_field' ) ? get_field( 'inset_image' ) : null, 'liftedpath-card', 'liftedpath-stewardship__inset' );
$phone      = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'phone', 'option' ) : '' );
$rows       = liftedpath_block_rows( function_exists( 'get_field' ) ? get_field( 'points' ) : array() );
$is_preview = ! empty( $is_preview );
$points     = array();

foreach ( $rows as $row ) {
	if ( ! is_array( $row ) ) {
		continue;
	}

	$title = liftedpath_block_string( $row['title'] ?? '' );
	$text  = liftedpath_block_string( $row['text'] ?? '' );

	if ( '' === $title && '' === $text ) {
		continue;
	}

	$points[] = array(
		'title' => $title,
		'text'  => $text,
	);
}

if ( '' === $note_label ) {
	$note_label = __( 'Call Us', 'liftedpath' );
}

$note = '';

if ( '' !== $phone ) {
	$tel  = preg_replace( '/[^\d+]/', '', $phone );
	$note = '<p class="liftedpath-stewardship__note"><span class="liftedpath-stewardship__phone">' . liftedpath_block_icon( 'phone' ) . '</span><span><strong>' . esc_html( $note_label ) . '</strong><small><a href="' . esc_url( 'tel:' . $tel ) . '">' . esc_html( $phone ) . '</a></small></span></p>';
}

$empty = '' === $heading && '' === $eyebrow && '' === $intro && '' === $image && '' === $button && array() === $points;

if ( $empty ) {
	if ( $is_preview ) {
		liftedpath_block_wrapper_open( 'section', 'liftedpath-section liftedpath-stewardship liftedpath-stewardship--empty' );
		liftedpath_block_placeholder( __( 'Add a heading and stewardship points.', 'liftedpath' ) );
		liftedpath_block_wrapper_close( 'section' );
	}
	return;
}
?>
<?php liftedpath_block_wrapper_open( 'section', 'liftedpath-section liftedpath-stewardship' ); ?>
	<div class="liftedpath-section__inner liftedpath-stewardship__layout">
		<div class="liftedpath-stewardship__copy">
			<?php if ( '' !== $eyebrow ) : ?>
				<p class="liftedpath-eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>
			<?php if ( '' !== $heading ) : ?>
				<h2><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>
			<?php if ( '' !== $intro ) : ?>
				<p class="liftedpath-stewardship__intro"><?php echo esc_html( $intro ); ?></p>
			<?php endif; ?>
			<?php if ( array() !== $points ) : ?>
				<ol class="liftedpath-stewardship__points">
					<?php foreach ( $points as $index => $point ) : ?>
						<li>
							<b><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></b>
							<span>
								<?php if ( '' !== $point['title'] ) : ?>
									<strong><?php echo esc_html( $point['title'] ); ?></strong>
								<?php endif; ?>
								<?php if ( '' !== $point['text'] ) : ?>
									<small><?php echo esc_html( $point['text'] ); ?></small>
								<?php endif; ?>
							</span>
						</li>
					<?php endforeach; ?>
				</ol>
			<?php endif; ?>
			<?php echo $button; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- link markup is escaped. ?>
			<?php if ( '' === $image && '' === $inset ) : ?>
				<?php echo $note; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- note markup is escaped. ?>
			<?php endif; ?>
		</div>
		<?php if ( '' !== $image || '' !== $inset ) : ?>
			<div class="liftedpath-stewardship__media">
				<?php echo $image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attachment image is escaped. ?>
				<?php echo $note; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- note markup is escaped. ?>
				<?php echo $inset; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attachment image is escaped. ?>
			</div>
		<?php endif; ?>
	</div>
<?php liftedpath_block_wrapper_close( 'section' ); ?>
