<?php
/**
 * About band with photographs and tabs.
 *
 * @package LiftedPath
 */

$primary   = function_exists( 'get_field' ) ? get_field( 'primary_image' ) : null;
$secondary = function_exists( 'get_field' ) ? get_field( 'secondary_image' ) : null;
$figure    = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'figure' ) : '' );
$label     = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'figure_label' ) : '' );
$eyebrow   = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'eyebrow' ) : '' );
$heading   = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'heading' ) : '' );
$intro     = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'intro' ) : '' );
$primary_link   = function_exists( 'get_field' ) ? get_field( 'primary_link' ) : null;
$secondary_link = function_exists( 'get_field' ) ? get_field( 'secondary_link' ) : null;
$tabs           = array_slice( liftedpath_block_rows( function_exists( 'get_field' ) ? get_field( 'tabs' ) : array() ), 0, 6 );
$primary_html   = liftedpath_block_image( $primary, 'liftedpath-portrait', 'liftedpath-about__photo' );
$secondary_html = liftedpath_block_image( $secondary, 'liftedpath-card', 'liftedpath-about__photo liftedpath-about__photo--small' );
$button         = liftedpath_block_button( $primary_link );
$text_link      = liftedpath_block_text_link( $secondary_link );
$is_preview     = ! empty( $is_preview );
$instance       = isset( $block['id'] ) ? sanitize_html_class( (string) $block['id'] ) : 'about';

$panels = array();

foreach ( $tabs as $row ) {
	if ( ! is_array( $row ) ) {
		continue;
	}

	$tab_label = liftedpath_block_string( $row['label'] ?? '' );
	$tab_title = liftedpath_block_string( $row['title'] ?? '' );
	$tab_body  = liftedpath_block_string( $row['body'] ?? '' );

	if ( '' === $tab_label && '' === $tab_title && '' === $tab_body ) {
		continue;
	}

	$panels[] = array(
		'label' => '' !== $tab_label ? $tab_label : $tab_title,
		'title' => $tab_title,
		'body'  => $tab_body,
	);
}

$empty = '' === $heading && '' === $eyebrow && '' === $intro && '' === $figure && '' === $label && '' === $primary_html && '' === $secondary_html && '' === $button && '' === $text_link && array() === $panels;

if ( $empty ) {
	if ( $is_preview ) {
		liftedpath_block_wrapper_open( 'section', 'liftedpath-section liftedpath-about liftedpath-about--empty' );
		liftedpath_block_placeholder( __( 'Add a heading, photographs, and tabs.', 'liftedpath' ) );
		liftedpath_block_wrapper_close( 'section' );
	}
	return;
}
?>
<?php liftedpath_block_wrapper_open( 'section', 'liftedpath-section liftedpath-about' ); ?>
	<div class="liftedpath-section__inner liftedpath-about__layout">
		<?php if ( '' !== $primary_html || '' !== $secondary_html || '' !== $figure || '' !== $label ) : ?>
			<div class="liftedpath-about__photos<?php echo ( '' === $primary_html && '' === $secondary_html ) ? ' liftedpath-about__photos--badge' : ''; ?>">
				<?php echo $primary_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attachment image is escaped. ?>
				<?php echo $secondary_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attachment image is escaped. ?>
				<?php if ( '' !== $figure || '' !== $label ) : ?>
					<p class="liftedpath-about__figure">
						<?php if ( '' !== $figure ) : ?>
							<strong><?php echo esc_html( $figure ); ?></strong>
						<?php endif; ?>
						<?php if ( '' !== $label ) : ?>
							<small><?php echo esc_html( $label ); ?></small>
						<?php endif; ?>
					</p>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<div class="liftedpath-about__copy">
			<?php if ( '' !== $eyebrow ) : ?>
				<p class="liftedpath-eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>
			<?php if ( '' !== $heading ) : ?>
				<h2><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>
			<?php if ( '' !== $intro ) : ?>
				<p class="liftedpath-about__intro"><?php echo esc_html( $intro ); ?></p>
			<?php endif; ?>
			<?php if ( array() !== $panels ) : ?>
				<div class="liftedpath-about__switcher">
					<div class="liftedpath-about__tabs" role="radiogroup" aria-label="<?php echo esc_attr( '' !== $eyebrow ? $eyebrow : __( 'About', 'liftedpath' ) ); ?>">
						<?php foreach ( $panels as $index => $panel ) : ?>
							<input class="liftedpath-about__input is-<?php echo esc_attr( (string) $index ); ?>" type="radio" name="liftedpath-about-<?php echo esc_attr( $instance ); ?>" id="liftedpath-about-<?php echo esc_attr( $instance . '-' . $index ); ?>" <?php echo 0 === $index ? 'checked' : ''; ?>>
							<label class="liftedpath-about__tab tab-<?php echo esc_attr( (string) $index ); ?>" for="liftedpath-about-<?php echo esc_attr( $instance . '-' . $index ); ?>"><?php echo esc_html( $panel['label'] ); ?></label>
						<?php endforeach; ?>
					</div>
					<?php foreach ( $panels as $index => $panel ) : ?>
						<div class="liftedpath-about__panel panel-<?php echo esc_attr( (string) $index ); ?>">
							<span class="liftedpath-about__icon"><?php echo liftedpath_block_icon( 'target' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static icon. ?></span>
							<div>
								<?php if ( '' !== $panel['title'] ) : ?>
									<h3><?php echo esc_html( $panel['title'] ); ?></h3>
								<?php endif; ?>
								<?php if ( '' !== $panel['body'] ) : ?>
									<p><?php echo esc_html( $panel['body'] ); ?></p>
								<?php endif; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
			<?php if ( '' !== $button || '' !== $text_link ) : ?>
				<div class="liftedpath-about__actions">
					<?php echo $button; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- link markup is escaped. ?>
					<?php echo $text_link; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- link markup is escaped. ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
<?php liftedpath_block_wrapper_close( 'section' ); ?>
