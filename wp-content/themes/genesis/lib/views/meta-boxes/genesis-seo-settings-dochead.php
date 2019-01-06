<?php
/**
 * Genesis Framework.
 *
 * WARNING: This file is part of the core Genesis Framework. DO NOT edit this file under any circumstances.
 * Please do all modifications in the form of a child theme.
 *
 * @package StudioPress\Genesis
 * @author  StudioPress
 * @license GPL-2.0-or-later
 * @link    https://my.studiopress.com/themes/genesis/
 */

?>
<p><span class="description">
	<?php
	$abbrev = sprintf( '<abbr title="%s">%s</abbr>', __( 'Search engine optimization', 'genesis' ), __( 'SEO', 'genesis' ) );

	/* translators: Escaped HTML head tag, abbreviation expansion for SEO. */
	printf( esc_html__( 'By default, WordPress places several tags in your document %1$s. Most of these tags are completely unnecessary, and provide no %2$s value whatsoever; they just make your site slower to load. Choose which tags you would like included in your document %1$s. If you do not know what something is, leave it unchecked.', 'genesis' ), genesis_code( '<head>' ), $abbrev );
	?>
</span></p>

<table class="form-table">
<tbody>

	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Relationship Link Tags', 'genesis' ); ?></th>
		<td>
			<p>
				<label for="<?php $this->field_id( 'head_adjacent_posts_rel_link' ); ?>"><input type="checkbox" name="<?php $this->field_name( 'head_adjacent_posts_rel_link' ); ?>" id="<?php $this->field_id( 'head_adjacent_posts_rel_link' ); ?>" value="1" <?php checked( $this->get_field_value( 'head_adjacent_posts_rel_link' ) ); ?> />
				<?php
					/* translators: Meta rel attribute. */
					printf( esc_html__( 'Adjacent Posts %s link tags', 'genesis' ), genesis_code( 'rel' ) );
				?>
				</label>
			</p>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Windows Live Writer', 'genesis' ); ?></th>
		<td>
			<p>
				<label for="<?php $this->field_id( 'head_wlmanifest_link' ); ?>"><input type="checkbox" name="<?php $this->field_name( 'head_wlwmanifest_link' ); ?>" id="<?php $this->field_id( 'head_wlmanifest_link' ); ?>" value="1" <?php checked( $this->get_field_value( 'head_wlwmanifest_link' ) ); ?> />
				<?php esc_html_e( 'Include Windows Live Writer Support Tag?', 'genesis' ); ?></label>
			</p>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Shortlink Tag', 'genesis' ); ?></th>
		<td>
			<p>
				<label for="<?php $this->field_id( 'head_shortlink' ); ?>"><input type="checkbox" name="<?php $this->field_name( 'head_shortlink' ); ?>" id="<?php $this->field_id( 'head_shortlink' ); ?>" value="1" <?php checked( $this->get_field_value( 'head_shortlink' ) ); ?> />
				<?php esc_html_e( 'Include Shortlink tag?', 'genesis' ); ?></label>
			</p>
			<p>
				<span class="description">
				<?php
					/* translators: Open and close span tags, abbreviation expansion for SEO. */
					printf( esc_html__( '%sNote:%s The shortlink tag might have some use for 3rd party service discoverability, but it has no %s value whatsoever.', 'genesis' ), '<span class="genesis-admin-note">', '</span>', $abbrev );
				?></span>
			</p>
		</td>
	</tr>

</tbody>
</table>
