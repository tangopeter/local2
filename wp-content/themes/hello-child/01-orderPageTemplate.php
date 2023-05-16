<?php

/**
 * The template for displaying singular post-types: posts, pages and user-defined custom post types.
 *
 * @package HelloElementor
 * Template Name: 01-Order Page
 * Template Post Type: page
 */
?>

<?php get_header(); ?>

<main id="content" <?php post_class('site-main'); ?> role="main">
	<?php if (apply_filters('hello_elementor_page_title', true)) : ?>
		<header class="page-header">
			<!-- <?php the_title('<h1 class="entry-title">', '</h1>'); ?> -->
		</header>
	<?php endif; ?>
	<div class="page-content">

		<h1>Select your files to upload</h1>
		<?php
		$current_user = wp_get_current_user();
		$user = $current_user->user_login;
		echo '<h4>' . 'Welcome ' . $user . '</h4>';
		echo '<ul>';
		echo '<li>' . 'Please select your files to upload' . '</li>';
		echo '<li>' . 'You may upload as many images as required,' . '<strong>' . ' but they must be the same settings/Quanity' . '</strong> ' . 'for each upload.' . '</li>';
		echo '<li>' . 'Repeat upload for each different setting' . '</li>';
		echo '<li>' . 'Click "Complete order" to confirm your details' . '</li>';
		echo '</ul>';

		$ORDER_NUMBER = get_option('ORDER_NUMBER');
		?>

		<?php the_content(); ?>
		<?php drawOrderTable($ORDER_NUMBER); ?>

		<div class="myDiv2">
			<form method="POST">
				<input type="submit" class="button1" id="btn-next" name="btn-comp" value="Complete Order">
			</form>
		</div>

</main>
</div>
<div class="myFooter">
	<?php get_footer(); ?>
</div>