<?php
/**
 * What We Do work layout: values strip, sidebar, article, end band.
 *
 * @package LiftedPath
 */

$is_preview = ! empty( $is_preview );

$values_rows = liftedpath_block_rows( function_exists( 'get_field' ) ? get_field( 'values' ) : array() );
$values      = array();
foreach ( $values_rows as $row ) {
	$phrase = liftedpath_block_string( is_array( $row ) ? ( $row['phrase'] ?? '' ) : '' );
	if ( '' !== $phrase ) {
		$values[] = $phrase;
	}
}

$sidebar_heading = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'sidebar_heading' ) : '' );
$nav_rows        = liftedpath_block_rows( function_exists( 'get_field' ) ? get_field( 'nav_links' ) : array() );
$show_back       = (bool) ( function_exists( 'get_field' ) ? get_field( 'show_back' ) : false );
$back_label      = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'back_label' ) : '' );
$back_link       = function_exists( 'get_field' ) ? get_field( 'back_link' ) : null;

$contact_image   = function_exists( 'get_field' ) ? get_field( 'contact_image' ) : null;
$contact_heading = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'contact_heading' ) : '' );
$contact_text    = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'contact_text' ) : '' );
$contact_button  = function_exists( 'get_field' ) ? get_field( 'contact_button' ) : null;
$contact_phone   = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'contact_phone' ) : '' );
if ( '' === $contact_phone && function_exists( 'get_field' ) ) {
	$settings_phone = get_field( 'phone', 'option' );
	$contact_phone  = is_string( $settings_phone ) ? $settings_phone : '';
}
$contact_photo = liftedpath_block_image( $contact_image, 'liftedpath-card', 'liftedpath-work__contact-photo', true );
$contact_btn   = liftedpath_block_button( $contact_button );
$phone_digits  = preg_replace( '/[^\d+]/', '', $contact_phone );
$phone_href    = is_string( $phone_digits ) && '' !== $phone_digits ? 'tel:' . $phone_digits : '';

$lead_image   = function_exists( 'get_field' ) ? get_field( 'lead_image' ) : null;
$lead_caption = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'lead_caption' ) : '' );
$lead_eyebrow = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'lead_eyebrow' ) : '' );
$lead_heading = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'lead_heading' ) : '' );
$lead_intro   = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'lead_intro' ) : '' );
$lead_photo   = liftedpath_block_image( $lead_image, 'liftedpath-hero', 'liftedpath-work__lead-photo' );

$audience_kicker  = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'audience_kicker' ) : '' );
$audience_heading = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'audience_heading' ) : '' );
$audience_text    = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'audience_text' ) : '' );
$support_rows     = liftedpath_block_rows( function_exists( 'get_field' ) ? get_field( 'support' ) : array() );

$areas_kicker  = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'areas_kicker' ) : '' );
$areas_heading = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'areas_heading' ) : '' );
$areas_rows    = liftedpath_block_rows( function_exists( 'get_field' ) ? get_field( 'areas' ) : array() );

$why_kicker  = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'why_kicker' ) : '' );
$why_heading = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'why_heading' ) : '' );
$why_text    = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'why_text' ) : '' );
$principles  = liftedpath_block_rows( function_exists( 'get_field' ) ? get_field( 'principles' ) : array() );

$faq_kicker  = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'faq_kicker' ) : '' );
$faq_heading = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'faq_heading' ) : '' );
$faq_items   = liftedpath_block_rows( function_exists( 'get_field' ) ? get_field( 'faq_items' ) : array() );

$end_eyebrow = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'end_eyebrow' ) : '' );
$end_heading = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'end_heading' ) : '' );
$end_text    = liftedpath_block_string( function_exists( 'get_field' ) ? get_field( 'end_text' ) : '' );
$end_button  = liftedpath_block_button( function_exists( 'get_field' ) ? get_field( 'end_button' ) : null );

$empty = array() === $values
	&& '' === $sidebar_heading
	&& '' === $lead_heading
	&& '' === $audience_heading
	&& '' === $areas_heading
	&& '' === $why_heading
	&& '' === $faq_heading
	&& '' === $end_heading
	&& '' === $contact_heading;

if ( $empty ) {
	if ( $is_preview ) {
		liftedpath_block_wrapper_open( 'div', 'liftedpath-work liftedpath-work--empty' );
		liftedpath_block_placeholder( __( 'Add work layout content in the block sidebar.', 'liftedpath' ) );
		liftedpath_block_wrapper_close( 'div' );
	}
	return;
}

$nav_items = array();
foreach ( $nav_rows as $row ) {
	if ( ! is_array( $row ) ) {
		continue;
	}
	$title    = liftedpath_block_string( $row['title'] ?? '' );
	$subtitle = liftedpath_block_string( $row['subtitle'] ?? '' );
	$link     = is_array( $row['link'] ?? null ) ? $row['link'] : array();
	if ( '' === $title && '' === $subtitle ) {
		continue;
	}
	$nav_items[] = array(
		'title'    => $title,
		'subtitle' => $subtitle,
		'link'     => $link,
	);
}

$support_items = array();
foreach ( $support_rows as $row ) {
	if ( ! is_array( $row ) ) {
		continue;
	}
	$title = liftedpath_block_string( $row['title'] ?? '' );
	$text  = liftedpath_block_string( $row['text'] ?? '' );
	if ( '' === $title && '' === $text ) {
		continue;
	}
	$support_items[] = array(
		'title' => $title,
		'text'  => $text,
	);
}

$area_items = array();
foreach ( $areas_rows as $row ) {
	if ( ! is_array( $row ) ) {
		continue;
	}
	$title    = liftedpath_block_string( $row['title'] ?? '' );
	$subtitle = liftedpath_block_string( $row['subtitle'] ?? '' );
	$text     = liftedpath_block_string( $row['text'] ?? '' );
	$link     = is_array( $row['link'] ?? null ) ? $row['link'] : array();
	if ( '' === $title && '' === $text ) {
		continue;
	}
	$area_items[] = array(
		'title'    => $title,
		'subtitle' => $subtitle,
		'text'     => $text,
		'link'     => $link,
	);
}

$principle_items = array();
foreach ( $principles as $row ) {
	$text = liftedpath_block_string( is_array( $row ) ? ( $row['text'] ?? '' ) : '' );
	if ( '' !== $text ) {
		$principle_items[] = $text;
	}
}

$faq_list = array();
foreach ( $faq_items as $row ) {
	if ( ! is_array( $row ) ) {
		continue;
	}
	$question = liftedpath_block_string( $row['question'] ?? '' );
	$answer   = liftedpath_block_string( $row['answer'] ?? '' );
	if ( '' === $question && '' === $answer ) {
		continue;
	}
	$faq_list[] = array(
		'question' => $question,
		'answer'   => $answer,
	);
}

/**
 * Nav or area link markup.
 *
 * @param array<string, mixed> $link    ACF link.
 * @param string               $title   Title.
 * @param string               $subtitle Subtitle.
 * @param string               $class   Class name.
 */
$work_link = static function ( array $link, string $title, string $subtitle, string $class ) {
	$url   = liftedpath_block_string( $link['url'] ?? '' );
	$extra = '';
	if ( isset( $link['target'] ) && '_blank' === $link['target'] ) {
		$extra = ' target="_blank" rel="noopener noreferrer"';
	}
	$body = '<span><strong>' . esc_html( $title ) . '</strong>';
	if ( '' !== $subtitle ) {
		$body .= '<small>' . esc_html( $subtitle ) . '</small>';
	}
	$body .= '</span>' . liftedpath_block_arrow();
	if ( '' !== $url ) {
		echo '<a class="' . esc_attr( $class ) . '" href="' . esc_url( $url ) . '"' . $extra . '>' . $body . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- body and attrs escaped above.
		return;
	}
	echo '<span class="' . esc_attr( $class ) . '">' . $body . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
};
?>
<?php if ( array() !== $values ) : ?>
	<div class="liftedpath-work-values" aria-label="<?php esc_attr_e( 'Our approach', 'liftedpath' ); ?>">
		<?php foreach ( $values as $index => $phrase ) : ?>
			<?php if ( $index > 0 ) : ?>
				<span class="liftedpath-work-values__icon" aria-hidden="true"><?php echo liftedpath_block_icon( 'sparkles' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			<?php endif; ?>
			<span><?php echo esc_html( $phrase ); ?></span>
		<?php endforeach; ?>
	</div>
<?php endif; ?>

<?php liftedpath_block_wrapper_open( 'section', 'liftedpath-section liftedpath-work' ); ?>
	<div class="liftedpath-section__inner liftedpath-work__layout">
		<aside class="liftedpath-work__sidebar">
			<?php if ( '' !== $sidebar_heading || array() !== $nav_items || $show_back ) : ?>
				<nav class="liftedpath-work__nav" aria-label="<?php echo esc_attr( '' !== $sidebar_heading ? $sidebar_heading : __( 'Explore our work', 'liftedpath' ) ); ?>">
					<?php if ( '' !== $sidebar_heading ) : ?>
						<h2><?php echo esc_html( $sidebar_heading ); ?></h2>
					<?php endif; ?>
					<?php if ( array() !== $nav_items ) : ?>
						<div>
							<?php foreach ( $nav_items as $item ) : ?>
								<?php $work_link( $item['link'], $item['title'], $item['subtitle'], 'liftedpath-work__nav-link' ); ?>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
					<?php
					if ( $show_back && is_array( $back_link ) && ! empty( $back_link['url'] ) ) :
						$back_title = '' !== $back_label ? $back_label : liftedpath_block_string( $back_link['title'] ?? '' );
						if ( '' === $back_title ) {
							$back_title = __( 'All our work', 'liftedpath' );
						}
						$back_extra = '';
						if ( isset( $back_link['target'] ) && '_blank' === $back_link['target'] ) {
							$back_extra = ' target="_blank" rel="noopener noreferrer"';
						}
						?>
						<a class="liftedpath-work__back" href="<?php echo esc_url( $back_link['url'] ); ?>"<?php echo $back_extra; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_html( $back_title ); ?><?php echo liftedpath_block_arrow(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
					<?php endif; ?>
				</nav>
			<?php endif; ?>

			<?php if ( '' !== $contact_heading || '' !== $contact_text || '' !== $contact_btn || '' !== $contact_phone ) : ?>
				<div class="liftedpath-work__contact<?php echo '' !== $contact_photo ? ' liftedpath-work__contact--photo' : ''; ?>">
					<?php echo $contact_photo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php if ( '' !== $contact_photo ) : ?>
						<span class="liftedpath-work__contact-shade" aria-hidden="true"></span>
					<?php endif; ?>
					<div class="liftedpath-work__contact-inner">
						<span class="liftedpath-work__contact-icon" aria-hidden="true"><?php echo liftedpath_block_icon( 'heart' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<?php if ( '' !== $contact_heading ) : ?>
							<h3><?php echo esc_html( $contact_heading ); ?></h3>
						<?php endif; ?>
						<?php if ( '' !== $contact_text ) : ?>
							<p><?php echo esc_html( $contact_text ); ?></p>
						<?php endif; ?>
						<?php echo $contact_btn; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php if ( '' !== $contact_phone && '' !== $phone_href ) : ?>
							<a class="liftedpath-work__phone" href="<?php echo esc_url( $phone_href ); ?>"><?php echo liftedpath_block_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( $contact_phone ); ?></a>
						<?php elseif ( '' !== $contact_phone ) : ?>
							<span class="liftedpath-work__phone"><?php echo liftedpath_block_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( $contact_phone ); ?></span>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>
		</aside>

		<article class="liftedpath-work__article">
			<?php if ( '' !== $lead_photo || '' !== $lead_heading || '' !== $lead_intro ) : ?>
				<div class="liftedpath-work__lead">
					<?php if ( '' !== $lead_photo ) : ?>
						<figure>
							<?php echo $lead_photo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php if ( '' !== $lead_caption ) : ?>
								<figcaption><?php echo esc_html( $lead_caption ); ?></figcaption>
							<?php endif; ?>
						</figure>
					<?php endif; ?>
					<?php if ( '' !== $lead_eyebrow ) : ?>
						<span class="liftedpath-eyebrow"><?php echo esc_html( $lead_eyebrow ); ?></span>
					<?php endif; ?>
					<?php if ( '' !== $lead_heading ) : ?>
						<h2><?php echo esc_html( $lead_heading ); ?></h2>
					<?php endif; ?>
					<?php if ( '' !== $lead_intro ) : ?>
						<p class="liftedpath-work__intro"><?php echo esc_html( $lead_intro ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( '' !== $audience_heading || '' !== $audience_text || array() !== $support_items ) : ?>
				<section class="liftedpath-work__block">
					<?php if ( '' !== $audience_kicker ) : ?>
						<span class="liftedpath-work__kicker"><?php echo esc_html( $audience_kicker ); ?></span>
					<?php endif; ?>
					<?php if ( '' !== $audience_heading ) : ?>
						<h2><?php echo esc_html( $audience_heading ); ?></h2>
					<?php endif; ?>
					<?php if ( '' !== $audience_text ) : ?>
						<p><?php echo esc_html( $audience_text ); ?></p>
					<?php endif; ?>
					<?php if ( array() !== $support_items ) : ?>
						<div class="liftedpath-work__support">
							<?php foreach ( $support_items as $index => $item ) : ?>
								<div>
									<span class="liftedpath-work__support-icon" aria-hidden="true"><?php echo liftedpath_block_icon( 0 === $index ? 'heart' : 'sparkles' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
									<?php if ( '' !== $item['title'] ) : ?>
										<h3><?php echo esc_html( $item['title'] ); ?></h3>
									<?php endif; ?>
									<?php if ( '' !== $item['text'] ) : ?>
										<p><?php echo esc_html( $item['text'] ); ?></p>
									<?php endif; ?>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</section>
			<?php endif; ?>

			<?php if ( array() !== $area_items ) : ?>
				<section class="liftedpath-work__block">
					<?php if ( '' !== $areas_kicker ) : ?>
						<span class="liftedpath-work__kicker"><?php echo esc_html( $areas_kicker ); ?></span>
					<?php endif; ?>
					<?php if ( '' !== $areas_heading ) : ?>
						<h2><?php echo esc_html( $areas_heading ); ?></h2>
					<?php endif; ?>
					<div class="liftedpath-work__child-list">
						<?php foreach ( $area_items as $index => $item ) : ?>
							<div class="liftedpath-work__child">
								<span class="liftedpath-work__child-number"><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></span>
								<div>
									<?php $work_link( $item['link'], $item['title'], $item['subtitle'], 'liftedpath-work__nav-link' ); ?>
									<?php if ( '' !== $item['text'] ) : ?>
										<p><?php echo esc_html( $item['text'] ); ?></p>
									<?php endif; ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endif; ?>

			<?php if ( '' !== $why_heading || '' !== $why_text || array() !== $principle_items ) : ?>
				<section class="liftedpath-work__block">
					<?php if ( '' !== $why_kicker ) : ?>
						<span class="liftedpath-work__kicker"><?php echo esc_html( $why_kicker ); ?></span>
					<?php endif; ?>
					<?php if ( '' !== $why_heading ) : ?>
						<h2><?php echo esc_html( $why_heading ); ?></h2>
					<?php endif; ?>
					<?php if ( '' !== $why_text ) : ?>
						<p><?php echo esc_html( $why_text ); ?></p>
					<?php endif; ?>
					<?php if ( array() !== $principle_items ) : ?>
						<ul class="liftedpath-work__principles">
							<?php foreach ( $principle_items as $item ) : ?>
								<li><?php echo liftedpath_block_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( $item ); ?></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</section>
			<?php endif; ?>

			<?php if ( '' !== $faq_heading || array() !== $faq_list ) : ?>
				<section class="liftedpath-work__block liftedpath-work__questions">
					<?php if ( '' !== $faq_kicker ) : ?>
						<span class="liftedpath-work__kicker"><?php echo esc_html( $faq_kicker ); ?></span>
					<?php endif; ?>
					<?php if ( '' !== $faq_heading ) : ?>
						<h2><?php echo esc_html( $faq_heading ); ?></h2>
					<?php endif; ?>
					<?php if ( array() !== $faq_list ) : ?>
						<div class="liftedpath-faq__list">
							<?php foreach ( $faq_list as $index => $item ) : ?>
								<details class="liftedpath-faq__item"<?php echo 0 === $index ? ' open' : ''; ?>>
									<summary>
										<span><?php echo esc_html( $item['question'] ?: $item['answer'] ); ?></span>
										<span class="liftedpath-faq__chevron"><?php echo liftedpath_block_icon( 'chevron' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
									</summary>
									<?php if ( '' !== $item['answer'] && '' !== $item['question'] ) : ?>
										<p><?php echo esc_html( $item['answer'] ); ?></p>
									<?php endif; ?>
								</details>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</section>
			<?php endif; ?>
		</article>
	</div>
<?php liftedpath_block_wrapper_close( 'section' ); ?>

<?php if ( '' !== $end_heading || '' !== $end_text || '' !== $end_button ) : ?>
	<section class="liftedpath-section liftedpath-work-end">
		<div class="liftedpath-section__inner liftedpath-work-end__inner">
			<div>
				<?php if ( '' !== $end_eyebrow ) : ?>
					<span class="liftedpath-eyebrow"><?php echo esc_html( $end_eyebrow ); ?></span>
				<?php endif; ?>
				<?php if ( '' !== $end_heading ) : ?>
					<h2><?php echo esc_html( $end_heading ); ?></h2>
				<?php endif; ?>
				<?php if ( '' !== $end_text ) : ?>
					<p><?php echo esc_html( $end_text ); ?></p>
				<?php endif; ?>
			</div>
			<?php echo $end_button; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</section>
<?php endif; ?>
