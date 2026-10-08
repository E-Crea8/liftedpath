<?php
/**
 * How-we-work grid.
 *
 * @package LiftedPath
 */

$eyebrow = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'eyebrow' ) : '' );
$heading = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'heading' ) : '' );
$intro   = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'intro' ) : '' );
$rows    = liftedpath_block_rows( function_exists( 'get_field' ) ? get_field( 'items' ) : array() );
$weight  = liftedpath_block_title_weight( function_exists( 'get_field' ) ? get_field( 'title_weight' ) : '' );
$grid_style = '' !== $weight ? ' style="' . esc_attr( '--action-title-weight:' . $weight ) . '"' : '';
$icons   = array( 'heart', 'link', 'sparkles', 'megaphone' );
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
		'link'  => is_array( $row['link'] ?? null ) ? $row['link'] : array(),
		'hover' => liftedpath_block_color( $row['hover_color'] ?? '' ),
	);
}

$empty = '' === $heading && '' === $eyebrow && '' === $intro && array() === $items;

if ( $empty ) {
	if ( $is_preview ) {
		liftedpath_block_wrapper_open( 'section', 'liftedpath-section liftedpath-actions liftedpath-actions--empty' );
		liftedpath_block_placeholder( __( 'Add a heading and actions.', 'liftedpath' ) );
		liftedpath_block_wrapper_close( 'section' );
	}
	return;
}
?>
<?php liftedpath_block_wrapper_open( 'section', 'liftedpath-section liftedpath-actions' ); ?>
	<div class="liftedpath-section__inner">
		<?php if ( '' !== $eyebrow || '' !== $heading || '' !== $intro ) : ?>
			<div class="liftedpath-actions__heading">
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
		<div class="liftedpath-actions__grid"<?php echo $grid_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped weight. ?>>
			<?php foreach ( $items as $index => $item ) : ?>
				<?php
				$link  = $item['link'];
				$url   = liftedpath_block_string( $link['url'] ?? '' );
				$label = liftedpath_block_string( $link['title'] ?? '' );
				$extra = '';
				$style = '';
				$icon  = $icons[ $index ] ?? 'heart';

				if ( '' !== $item['hover'] ) {
					$style = ' style="' . esc_attr( '--action-tint:' . $item['hover'] ) . '"';
				}

				if ( '' === $label ) {
					$label = __( 'Read more', 'liftedpath' );
				}

				if ( isset( $link['target'] ) && '_blank' === $link['target'] ) {
					$extra = ' target="_blank" rel="noopener noreferrer"';
				}
				?>
				<?php if ( '' !== $url ) : ?>
					<a class="liftedpath-actions__item" href="<?php echo esc_url( $url ); ?>"<?php echo $style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped hex color. ?><?php echo $extra; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed attributes. ?>>
				<?php else : ?>
					<article class="liftedpath-actions__item"<?php echo $style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped hex color. ?>>
				<?php endif; ?>
					<span class="liftedpath-actions__wash" aria-hidden="true"></span>
					<span class="liftedpath-actions__icon"><?php echo liftedpath_block_icon( $icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static icon. ?></span>
					<?php if ( '' !== $item['title'] ) : ?>
						<h3><?php echo esc_html( $item['title'] ); ?></h3>
					<?php endif; ?>
					<?php if ( '' !== $item['text'] ) : ?>
						<p><?php echo esc_html( $item['text'] ); ?></p>
					<?php endif; ?>
					<?php if ( '' !== $url ) : ?>
						<span class="liftedpath-text-link"><?php echo esc_html( $label ); ?><?php echo liftedpath_block_arrow(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static icon. ?></span>
					<?php endif; ?>
				<?php if ( '' !== $url ) : ?>
					</a>
				<?php else : ?>
					</article>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
	</div>
<?php liftedpath_block_wrapper_close( 'section' ); ?>
