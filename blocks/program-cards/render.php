<?php
/**
 * Initiative cards and an optional sponsor cell.
 *
 * @package LiftedPath
 */

$eyebrow  = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'eyebrow' ) : '' );
$heading  = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'heading' ) : '' );
$intro    = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'intro' ) : '' );
$callout_heading = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'callout_heading' ) : '' );
$callout_text    = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'callout_text' ) : '' );
$callout_button  = function_exists( 'get_field' ) ? get_field( 'callout_button' ) : null;
$cards           = liftedpath_block_rows( function_exists( 'get_field' ) ? get_field( 'cards' ) : array() );
$button          = liftedpath_block_button( $callout_button );
$is_preview      = ! empty( $is_preview );

$items = array();

foreach ( $cards as $row ) {
	if ( ! is_array( $row ) ) {
		continue;
	}

	$image = liftedpath_block_image( $row['image'] ?? null, 'liftedpath-card', 'liftedpath-programs__image' );
	$title = liftedpath_block_string( $row['title'] ?? '' );
	$line  = liftedpath_block_string( $row['line'] ?? '' );
	$kicker = liftedpath_block_string( $row['kicker'] ?? '' );

	if ( '' === $image && '' === $title && '' === $line && '' === $kicker ) {
		continue;
	}

	$items[] = array(
		'image'  => $image,
		'kicker' => $kicker,
		'title'  => $title,
		'line'   => $line,
		'link'   => is_array( $row['link'] ?? null ) ? $row['link'] : array(),
	);
}

$empty = '' === $heading && '' === $eyebrow && '' === $intro && '' === $callout_heading && array() === $items;

if ( $empty ) {
	if ( $is_preview ) {
		liftedpath_block_wrapper_open( 'section', 'liftedpath-section liftedpath-programs liftedpath-programs--empty' );
		liftedpath_block_placeholder( __( 'Add a heading and program cards.', 'liftedpath' ) );
		liftedpath_block_wrapper_close( 'section' );
	}
	return;
}
?>
<?php liftedpath_block_wrapper_open( 'section', 'liftedpath-section liftedpath-programs' ); ?>
	<div class="liftedpath-section__inner">
		<?php if ( '' !== $eyebrow || '' !== $heading || '' !== $intro ) : ?>
			<div class="liftedpath-programs__heading">
				<?php if ( '' !== $eyebrow ) : ?>
					<p class="liftedpath-eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
				<?php endif; ?>
				<?php if ( '' !== $heading ) : ?>
					<h2><?php echo esc_html( $heading ); ?></h2>
				<?php endif; ?>
				<?php if ( '' !== $intro ) : ?>
					<p><?php echo esc_html( $intro ); ?></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<div class="liftedpath-programs__grid">
			<?php foreach ( $items as $item ) : ?>
				<?php
				$link  = $item['link'];
				$url   = liftedpath_block_string( $link['url'] ?? '' );
				$label = liftedpath_block_string( $link['title'] ?? '' );
				$extra = '';

				if ( '' === $label ) {
					$label = __( 'Read more', 'liftedpath' );
				}

				if ( isset( $link['target'] ) && '_blank' === $link['target'] ) {
					$extra = ' target="_blank" rel="noopener noreferrer"';
				}
				?>
				<?php if ( '' !== $url ) : ?>
					<a class="liftedpath-programs__card" href="<?php echo esc_url( $url ); ?>"<?php echo $extra; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed attributes. ?>>
				<?php else : ?>
					<article class="liftedpath-programs__card">
				<?php endif; ?>
					<div class="liftedpath-programs__media">
						<?php echo $item['image']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attachment image is escaped. ?>
						<?php if ( '' !== $item['kicker'] ) : ?>
							<span><?php echo esc_html( $item['kicker'] ); ?></span>
						<?php endif; ?>
					</div>
					<div class="liftedpath-programs__info">
						<?php if ( '' !== $item['title'] ) : ?>
							<h3><?php echo esc_html( $item['title'] ); ?></h3>
						<?php endif; ?>
						<?php if ( '' !== $item['line'] ) : ?>
							<p><?php echo esc_html( $item['line'] ); ?></p>
						<?php endif; ?>
						<?php if ( '' !== $url ) : ?>
							<span class="liftedpath-text-link"><?php echo esc_html( $label ); ?><?php echo liftedpath_block_arrow(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static icon. ?></span>
						<?php endif; ?>
					</div>
				<?php if ( '' !== $url ) : ?>
					</a>
				<?php else : ?>
					</article>
				<?php endif; ?>
			<?php endforeach; ?>
			<?php if ( '' !== $callout_heading ) : ?>
				<div class="liftedpath-programs__callout">
					<span class="liftedpath-programs__mark"><?php echo liftedpath_block_icon( 'heart' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static icon. ?></span>
					<div>
						<h3><?php echo esc_html( $callout_heading ); ?></h3>
						<?php if ( '' !== $callout_text ) : ?>
							<p><?php echo esc_html( $callout_text ); ?></p>
						<?php endif; ?>
					</div>
					<?php echo $button; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- link markup is escaped. ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
<?php liftedpath_block_wrapper_close( 'section' ); ?>
