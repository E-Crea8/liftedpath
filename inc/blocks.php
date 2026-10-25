<?php
/**
 * LiftedPath editor blocks.
 *
 * Each block is registered from blocks/<name>/block.json. ACF supplies the
 * fields and the render callback. The theme still loads when ACF is inactive.
 *
 * @package LiftedPath
 */

/**
 * Add the inserter category.
 *
 * @param array<int, array<string, string>> $categories Block categories.
 * @return array<int, array<string, string>>
 */
function liftedpath_block_categories( array $categories ): array {
	array_unshift(
		$categories,
		array(
			'slug'  => 'liftedpath',
			'title' => __( 'LiftedPath', 'liftedpath' ),
		)
	);

	return $categories;
}
add_filter( 'block_categories_all', 'liftedpath_block_categories' );

/**
 * Block folder names. The editor visibility and layout fields use this same list.
 *
 * @return string[]
 */
function liftedpath_block_slugs(): array {
	return array(
		'hero',
		'page-hero',
		'stat',
		'stat-group',
		'marquee',
		'cta-band',
		'about-tabs',
		'about-story',
		'beliefs',
		'program-cards',
		'pathway-cards',
		'work-layout',
		'footer-cta-2',
		'action-grid',
		'sponsor-callout',
		'stewardship',
		'process',
		'pillars',
		'goals',
		'faq',
		'contact-form',
		'donate-panel',
		'team',
		'gallery',
	);
}

/**
 * Register the editor blocks.
 */
function liftedpath_register_blocks(): void {
	if ( ! function_exists( 'acf_register_block_type' ) ) {
		return;
	}

	foreach ( liftedpath_block_slugs() as $block ) {
		register_block_type( get_template_directory() . '/blocks/' . $block );
	}
}
add_action( 'init', 'liftedpath_register_blocks' );

/**
 * Host and editor presentation for the blocks.
 *
 * Tokens load first so blocks.css --color-* names resolve from theme.json.
 */
function liftedpath_block_styles(): void {
	wp_enqueue_style(
		'liftedpath-wp-tokens',
		get_template_directory_uri() . '/assets/wp-tokens.css',
		array(),
		LIFTEDPATH_VERSION
	);
	wp_enqueue_style(
		'liftedpath-blocks',
		get_template_directory_uri() . '/assets/blocks.css',
		array( 'liftedpath-wp-tokens' ),
		LIFTEDPATH_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'liftedpath_block_styles' );
add_action( 'enqueue_block_assets', 'liftedpath_block_styles' );

/**
 * Hex color at an opacity WordPress will keep in a style attribute.
 *
 * @param string $hex     #rgb or #rrggbb.
 * @param int    $opacity 0 to 100.
 */
function liftedpath_block_rgba( string $hex, int $opacity ): string {
	$hex = ltrim( $hex, '#' );

	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}

	$red   = hexdec( substr( $hex, 0, 2 ) );
	$green = hexdec( substr( $hex, 2, 2 ) );
	$blue  = hexdec( substr( $hex, 4, 2 ) );
	$alpha = number_format( max( 0, min( 100, $opacity ) ) / 100, 2, '.', '' );

	return 'rgba(' . $red . ', ' . $green . ', ' . $blue . ', ' . $alpha . ')';
}

/**
 * Design 01 colors an editor can paint behind any LiftedPath block.
 *
 * @return array<string, string>
 */
function liftedpath_block_background_palette(): array {
	return array(
		'paper'   => '#fcfdfd',
		'surface' => '#e7ecec',
		'teal'    => '#043c42',
		'deep'    => '#02272b',
		'gold'    => '#b78d4b',
		'bronze'  => '#a57d3c',
		'sand'    => '#d6b886',
	);
}

/**
 * Background chosen on the block, or empty values when the block keeps its design.
 *
 * @return array{class: string, style: string, media: string}
 */
function liftedpath_block_background(): array {
	$empty = array(
		'class' => '',
		'style' => '',
		'media' => '',
	);

	if ( ! function_exists( 'get_field' ) ) {
		return $empty;
	}

	$kind = get_field( 'background_kind' );

	if ( ! is_string( $kind ) || ! in_array( $kind, array( 'color', 'image', 'video' ), true ) ) {
		return $empty;
	}

	$palette = liftedpath_block_background_palette();
	$variant = get_field( 'background_variant' );
	$variant = is_string( $variant ) ? $variant : 'paper';
	$hex     = 'custom' === $variant ? liftedpath_block_color( get_field( 'background_color' ) ) : ( $palette[ $variant ] ?? '' );

	if ( '' === $hex ) {
		$hex = $palette['paper'];
	}

	$opacity = get_field( 'background_opacity' );

	if ( null === $opacity || false === $opacity || '' === $opacity ) {
		$opacity = 100;
	}

	$opacity = max( 0, min( 100, (int) $opacity ) );
	$mix     = liftedpath_block_rgba( $hex, $opacity );
	$class   = 'has-block-background has-block-background-' . $kind;
	$style   = '';
	$media   = '';

	if ( 'color' === $kind ) {
		$alpha = number_format( $opacity / 100, 2, '.', '' );
		$style = '--block-bg:' . $hex . ';--block-opacity:' . $alpha . ';background-image:none;';
	}

	if ( 'image' === $kind ) {
		$image_id  = liftedpath_attachment_id( get_field( 'background_image' ) );
		$image_url = $image_id > 0 ? wp_get_attachment_image_url( $image_id, 'full' ) : '';
		$image_url = is_string( $image_url ) ? esc_url_raw( $image_url ) : '';

		if ( '' !== $image_url ) {
			$style = "background-image:url('" . $image_url . "');background-size:cover;background-position:center;background-color:transparent;";
		}

		$media = '<span class="liftedpath-block-bg-shade" style="background-color:' . esc_attr( $mix ) . '" aria-hidden="true"></span>';
	}

	if ( 'video' === $kind ) {
		$link  = get_field( 'background_video_url' );
		$media = liftedpath_block_video(
			get_field( 'background_video' ),
			'liftedpath-block-bg-video',
			'',
			is_string( $link ) ? $link : '',
			true,
			true,
			true
		);
		$media .= '<span class="liftedpath-block-bg-shade" style="background-color:' . esc_attr( $mix ) . '" aria-hidden="true"></span>';
	}

	return array(
		'class' => $class,
		'style' => $style,
		'media' => $media,
	);
}

/**
 * Open a block wrapper. Classes include the generated block class and alignment.
 *
 * @param string $tag   Element name.
 * @param string $class Block class.
 */
function liftedpath_block_wrapper_open( string $tag, string $class, string $style = '' ): void {
	if ( function_exists( 'liftedpath_block_state_classes' ) ) {
		$state = liftedpath_block_state_classes();

		if ( '' !== $state ) {
			$class = trim( $class . ' ' . $state );
		}
	}

	$background = liftedpath_block_background();

	if ( '' !== $background['class'] ) {
		$class = trim( $class . ' ' . $background['class'] );
	}

	if ( '' !== $background['style'] ) {
		$style = $background['style'] . $style;
	}

	$attributes = array( 'class' => $class );

	if ( '' !== $style ) {
		$attributes['style'] = $style;
	}

	echo '<' . tag_escape( $tag ) . ' ' . get_block_wrapper_attributes( $attributes ) . '>';

	if ( '' !== $background['media'] ) {
		echo $background['media']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper escapes the video and shade.
	}
}

/**
 * Close a block wrapper.
 *
 * @param string $tag Element name.
 */
function liftedpath_block_wrapper_close( string $tag ): void {
	echo '</' . tag_escape( $tag ) . '>';
}

/**
 * Editor prompt. The host front does not print this.
 *
 * @param string $message Prompt.
 */
function liftedpath_block_placeholder( string $message ): void {
	echo '<p class="liftedpath-block-placeholder">' . esc_html( $message ) . '</p>';
}

/**
 * Attachment ID from an ACF image field.
 *
 * @param mixed $image Field value.
 */
function liftedpath_attachment_id( $image ): int {
	if ( is_array( $image ) && isset( $image['ID'] ) ) {
		return (int) $image['ID'];
	}

	if ( is_numeric( $image ) ) {
		return (int) $image;
	}

	return 0;
}

/**
 * Print an attachment. Decorative images keep an empty alt.
 *
 * @param mixed  $image      Field value.
 * @param string $size       Registered size.
 * @param string $class      Image class.
 * @param bool   $decorative Empty the alt text.
 */
function liftedpath_block_image( $image, string $size, string $class, bool $decorative = false ): string {
	$id = liftedpath_attachment_id( $image );

	if ( ! $id ) {
		return '';
	}

	$attr = array(
		'class' => $class,
	);

	if ( $decorative ) {
		$attr['alt'] = '';
	}

	$html = wp_get_attachment_image( $id, $size, false, $attr );

	return is_string( $html ) ? $html : '';
}

/**
 * A hero playback switch. Missing values use the default.
 *
 * @param mixed $value Field value.
 */
function liftedpath_block_flag( $value, bool $default ): bool {
	if ( null === $value || '' === $value ) {
		return $default;
	}

	if ( is_bool( $value ) ) {
		return $value;
	}

	if ( is_numeric( $value ) ) {
		return 1 === (int) $value;
	}

	if ( ! is_string( $value ) ) {
		return $default;
	}

	$value = strtolower( $value );

	if ( in_array( $value, array( '1', 'true', 'yes', 'on' ), true ) ) {
		return true;
	}

	if ( in_array( $value, array( '0', 'false', 'no', 'off' ), true ) ) {
		return false;
	}

	return $default;
}

/**
 * An http or https video address, or an empty string.
 */
function liftedpath_block_video_link( string $url ): string {
	$url   = trim( $url );
	$valid = wp_http_validate_url( $url );

	if ( ! is_string( $valid ) || '' === $valid ) {
		return '';
	}

	$scheme = wp_parse_url( $valid, PHP_URL_SCHEME );

	return 'http' === $scheme || 'https' === $scheme ? $valid : '';
}

/**
 * Background video from a link, or from an uploaded file when the link is empty.
 *
 * @param mixed  $video    File field value.
 * @param string $class    Video class.
 * @param string $poster   Poster image URL.
 * @param string $url      Cloudinary or other video link.
 * @param bool   $muted    Mute the audio.
 * @param bool   $loop     Replay when the video ends.
 * @param bool   $autoplay Start as soon as the page loads.
 */
function liftedpath_block_video( $video, string $class, string $poster = '', string $url = '', bool $muted = true, bool $loop = true, bool $autoplay = true ): string {
	$src  = liftedpath_block_video_link( $url );
	$mime = '';

	if ( '' === $src ) {
		$id   = liftedpath_attachment_id( $video );
		$file = $id > 0 ? wp_get_attachment_url( $id ) : false;
		$type = $id > 0 ? get_post_mime_type( $id ) : '';

		if ( ! is_string( $file ) || '' === $file || ! is_string( $type ) || ! str_starts_with( $type, 'video/' ) ) {
			return '';
		}

		$src  = $file;
		$mime = $type;
	}

	$attributes = ' playsinline preload="auto"';

	if ( $muted ) {
		$attributes .= ' muted';
	}

	if ( $loop ) {
		$attributes .= ' loop';
	}

	if ( $autoplay ) {
		$attributes .= ' autoplay';
	}

	if ( '' !== $poster ) {
		$attributes .= ' poster="' . esc_url( $poster ) . '"';
	}

	$type_attr = '' !== $mime ? ' type="' . esc_attr( $mime ) . '"' : '';

	return '<video class="' . esc_attr( $class ) . '"' . $attributes . '><source src="' . esc_url( $src ) . '"' . $type_attr . ' /></video>';
}

/**
 * Hero background video using the current block's playback fields.
 *
 * @param mixed  $video  File field value.
 * @param string $class  Video class.
 * @param string $poster Poster image URL.
 */
function liftedpath_hero_video( $video, string $class, string $poster ): string {
	$link = function_exists( 'get_field' ) ? get_field( 'video_url' ) : '';

	return liftedpath_block_video(
		$video,
		$class,
		$poster,
		is_string( $link ) ? $link : '',
		liftedpath_block_flag( function_exists( 'get_field' ) ? get_field( 'video_muted' ) : null, true ),
		liftedpath_block_flag( function_exists( 'get_field' ) ? get_field( 'video_loop' ) : null, true ),
		liftedpath_block_flag( function_exists( 'get_field' ) ? get_field( 'video_autoplay' ) : null, true )
	);
}

/**
 * Poster URL for a hero photograph.
 *
 * @param mixed $image Image field value.
 */
function liftedpath_block_poster( $image ): string {
	$id  = liftedpath_attachment_id( $image );
	$url = $id > 0 ? wp_get_attachment_image_url( $id, 'liftedpath-hero' ) : false;

	return is_string( $url ) ? $url : '';
}

/**
 * Gold pill link from an ACF link field.
 *
 * @param mixed $link Field value.
 */
function liftedpath_block_button( $link ): string {
	if ( ! is_array( $link ) || empty( $link['url'] ) || empty( $link['title'] ) ) {
		return '';
	}

	$target = '';
	$rel    = '';

	if ( isset( $link['target'] ) && '_blank' === $link['target'] ) {
		$target = ' target="_blank"';
		$rel    = ' rel="noopener noreferrer"';
	}

	return '<a class="liftedpath-button" href="' . esc_url( $link['url'] ) . '"' . $target . $rel . '>' . esc_html( $link['title'] ) . liftedpath_block_arrow() . '</a>';
}

/**
 * Arrow used after button labels. Static markup, no user input.
 */
function liftedpath_block_arrow(): string {
	return '<svg class="liftedpath-button__icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M7 17 17 7"/><path d="M7 7h10v10"/></svg>';
}

/**
 * Text link with the same arrow as a button.
 *
 * @param mixed $link Field value.
 */
function liftedpath_block_text_link( $link ): string {
	if ( ! is_array( $link ) || empty( $link['url'] ) || empty( $link['title'] ) ) {
		return '';
	}

	$target = '';
	$rel    = '';

	if ( isset( $link['target'] ) && '_blank' === $link['target'] ) {
		$target = ' target="_blank"';
		$rel    = ' rel="noopener noreferrer"';
	}

	return '<a class="liftedpath-text-link" href="' . esc_url( $link['url'] ) . '"' . $target . $rel . '>' . esc_html( $link['title'] ) . liftedpath_block_arrow() . '</a>';
}

/**
 * Coerce an ACF value to a string.
 *
 * @param mixed $value Field value.
 */
function liftedpath_block_string( $value ): string {
	return is_string( $value ) ? $value : '';
}

/**
 * Paragraphs from a textarea. A blank line or a new line starts the next paragraph.
 *
 * @return array<int, string>
 */
function liftedpath_block_paragraphs( string $body ): array {
	$parts = preg_split( '/\n+/', $body );

	if ( ! is_array( $parts ) ) {
		return array();
	}

	$paragraphs = array();

	foreach ( $parts as $part ) {
		$part = trim( $part );

		if ( '' !== $part ) {
			$paragraphs[] = $part;
		}
	}

	return $paragraphs;
}

/**
 * Repeater rows, or an empty list.
 *
 * @param mixed $rows Field value.
 * @return array<int, mixed>
 */
function liftedpath_block_rows( $rows ): array {
	return is_array( $rows ) ? array_values( $rows ) : array();
}

/**
 * A hex color from a color field, or an empty string.
 *
 * @param mixed $value Field value.
 */
function liftedpath_block_color( $value ): string {
	$color = is_string( $value ) ? trim( $value ) : '';

	return 1 === preg_match( '/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $color ) ? $color : '';
}

/**
 * An action-title weight, or an empty string.
 *
 * @param mixed $value Field value.
 */
function liftedpath_block_title_weight( $value ): string {
	$weight = is_scalar( $value ) ? (string) $value : '';

	return in_array( $weight, array( '400', '500', '600', '700' ), true ) ? $weight : '';
}

/**
 * Lucide 0.575 symbols, the set used by Design 01. Static markup, no user input.
 *
 * @param string $name Icon name.
 */
function liftedpath_block_icon( string $name ): string {
	$paths = array(
		'target'    => '<circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/>',
		'check'     => '<path d="M20 6 9 17l-5-5"/>',
		'heart'     => '<path d="M2 9.5a5.5 5.5 0 0 1 9.591-3.676.56.56 0 0 0 .818 0A5.49 5.49 0 0 1 22 9.5c0 2.29-1.5 4-3 5.5l-5.492 5.313a2 2 0 0 1-3 .019L5 15c-1.5-1.5-3-3.2-3-5.5"/>',
		'link'      => '<path d="M9 17H7A5 5 0 0 1 7 7h2"/><path d="M15 7h2a5 5 0 1 1 0 10h-2"/><line x1="8" x2="16" y1="12" y2="12"/>',
		'sparkles'  => '<path d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z"/><path d="M20 2v4"/><path d="M22 4h-4"/><circle cx="4" cy="20" r="2"/>',
		'megaphone' => '<path d="M11 6a13 13 0 0 0 8.4-2.8A1 1 0 0 1 21 4v12a1 1 0 0 1-1.6.8A13 13 0 0 0 11 14H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2z"/><path d="M6 14a12 12 0 0 0 2.4 7.2 2 2 0 0 0 3.2-2.4A8 8 0 0 1 10 14"/><path d="M8 6v8"/>',
		'phone'     => '<path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"/>',
		'mail'      => '<path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"/><rect x="2" y="4" width="20" height="16" rx="2"/>',
		'map-pin'   => '<path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/>',
		'chevron'        => '<path d="m6 9 6 6 6-6"/>',
		'graduation-cap' => '<path d="M21.42 10.922a1 1 0 0 0-.019-1.838L12.83 5.18a2 2 0 0 0-1.66 0L2.6 9.08a1 1 0 0 0 0 1.832l8.57 3.908a2 2 0 0 0 1.66 0z"/><path d="M22 10v6"/><path d="M6 12.5V16a6 3 0 0 0 12 0v-3.5"/>',
		'book-open'      => '<path d="M12 7v14"/><path d="M3 18a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h5a4 4 0 0 1 4 4 4 4 0 0 1 4-4h5a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1h-6a3 3 0 0 0-3 3 3 3 0 0 0-3-3z"/>',
		'wrench'         => '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>',
		'users'          => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
	);

	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}

	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $paths[ $name ] . '</svg>';
}

/**
 * Mark for a gift amount, chosen from that amount's title.
 *
 * @param string $title Amount title from the donate block.
 */
function liftedpath_donate_detail_icon( string $title ): string {
	$label = strtolower( $title );

	if ( str_contains( $label, 'skill' ) || str_contains( $label, 'tool' ) ) {
		return 'wrench';
	}

	if ( str_contains( $label, 'family' ) || str_contains( $label, 'care' ) || str_contains( $label, 'food' ) ) {
		return 'users';
	}

	if ( str_contains( $label, 'school' ) || str_contains( $label, 'essential' ) || str_contains( $label, 'material' ) ) {
		return 'book-open';
	}

	return 'graduation-cap';
}

/**
 * Format a naira amount in one of the gift currencies.
 *
 * @param float  $ngn      Amount in naira.
 * @param string $currency NGN, USD, or GBP.
 * @param float  $per_usd  Naira per one dollar.
 * @param float  $per_gbp  Naira per one pound.
 */
function liftedpath_block_money( float $ngn, string $currency, float $per_usd, float $per_gbp ): string {
	$symbols = array(
		'NGN' => '₦',
		'USD' => '$',
		'GBP' => '£',
	);
	$per     = array(
		'NGN' => 1.0,
		'USD' => $per_usd > 0 ? $per_usd : 1500.0,
		'GBP' => $per_gbp > 0 ? $per_gbp : 2000.0,
	);

	if ( ! isset( $symbols[ $currency ] ) ) {
		$currency = 'NGN';
	}

	$value = $per[ $currency ] > 0 ? $ngn / $per[ $currency ] : $ngn;

	return $symbols[ $currency ] . number_format( (float) round( $value ) );
}

/**
 * Breadcrumb chevron. Static markup, no user input.
 */
function liftedpath_block_chevron(): string {
	return '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false"><path d="m9 18 6-6-6-6"/></svg>';
}

/**
 * Portrait attachment IDs from the hero repeater.
 *
 * @param mixed $rows Repeater rows.
 * @return array<int, int>
 */
function liftedpath_portrait_ids( $rows ): array {
	if ( ! is_array( $rows ) ) {
		return array();
	}

	$ids = array();

	foreach ( $rows as $row ) {
		$image = ( is_array( $row ) && isset( $row['image'] ) ) ? $row['image'] : null;
		$id    = liftedpath_attachment_id( $image );

		if ( $id ) {
			$ids[] = $id;
		}
	}

	return $ids;
}

/**
 * Marquee phrases from the block, or from LiftedPath settings when the block list is empty.
 *
 * @return array<int, string>
 */
function liftedpath_marquee_phrases(): array {
	$rows = get_field( 'phrases' );

	if ( ! is_array( $rows ) || array() === $rows ) {
		$rows = get_field( 'marquee_phrases', 'option' );
	}

	if ( ! is_array( $rows ) ) {
		return array();
	}

	$phrases = array();

	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) || ! isset( $row['phrase'] ) || ! is_string( $row['phrase'] ) ) {
			continue;
		}

		$phrase = trim( $row['phrase'] );

		if ( '' !== $phrase ) {
			$phrases[] = $phrase;
		}
	}

	return $phrases;
}

/**
 * One marquee sequence, phrases separated by a star.
 *
 * @param array<int, string> $phrases Phrases.
 * @param bool               $hidden  Hide the copy used to loop the animation.
 */
function liftedpath_marquee_group( array $phrases, bool $hidden = false ): string {
	$html = '<p class="liftedpath-marquee__group"' . ( $hidden ? ' aria-hidden="true"' : '' ) . '>';

	foreach ( $phrases as $phrase ) {
		$html .= '<span class="liftedpath-marquee__phrase">' . esc_html( $phrase ) . '</span>';
		$html .= '<span class="liftedpath-marquee__star" aria-hidden="true">✦</span>';
	}

	return $html . '</p>';
}
