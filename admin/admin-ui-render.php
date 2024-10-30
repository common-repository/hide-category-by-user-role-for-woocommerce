<?php

/**
 * Admin UI setup and render
 *
 * @since 1.0
 * @function	tswchc_general_settings_section_callback()	Callback function for General Settings section
 * @function	tswchc_general_settings_field_callback()	Callback function for General Settings field
 * @function	tswchc_admin_interface_render()				Admin interface renderer
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Admin interface renderer
 *
 * @since 1.0
 */
function tswchc_admin_interface_render() {

	if (!current_user_can('manage_options')) {
		return;
	}

	$prev_rules = json_decode(get_option('tswchc_rules'));

?>

	<div id="ts-wchc-wrapper" class="wraps">

		<form id="ts-wchc-form" class="card" method="post" action="options.php">

			<h1>Hide Category by User Role for WooCommerce</h1>

			<?php tswchc_general_admin_notice(); ?>

			<ul class="nav nav-tabs" id="tswchc_tabs" role="tablist">
				<li class="nav-item" role="presentation">
					<button class="nav-link active" data-bs-toggle="tab" data-bs-target="#hide-role-by-cat" type="button" role="tab" aria-controls="hide-role-by-cat" aria-selected="true">Hide by Category</button>
				</li>
				<li class="nav-item" role="presentation">
					<button class="nav-link" data-bs-toggle="tab" data-bs-target="#hide-cat-by-role" type="button" role="tab" aria-controls="hide-cat-by-role" aria-selected="false">Hide by User Role</button>
				</li>
				<li class="nav-item" role="presentation">
					<button class="nav-link" data-bs-toggle="tab" data-bs-target="#other-settings" type="button" role="tab" aria-controls="other-settings" aria-selected="false">Settings</button>
				</li>
			</ul>

			<div class="tab-content">

				<!-- Hide by Category -->
				<div class="tab-pane active" id="hide-role-by-cat" role="tabpanel" data-toggle="tab" aria-labelledby="hide-role-by-cat">

					<?php tswchc_get_hide_by_category_tab(); ?>

				</div>

				<!-- Hide by User Role -->

				<div class="tab-pane" id="hide-cat-by-role" role="tabpanel" data-toggle="tab" aria-labelledby="hide-cat-by-role-tab">

					<?php tswchc_get_hide_by_role_tab(); ?>

				</div>

				<!-- Settings Tab -->

				<div class="tab-pane" id="other-settings" role="tabpanel" data-toggle="tab" aria-labelledby="other-settings-tab">

					<?php tswchc_get_settings_tab(); ?>

				</div>

			</div>

			<?php settings_fields('tswchc_settings_group'); ?>
			<?php do_settings_sections('tswchc_settings_group'); ?>

			<input style="width: 100%;" type="hidden" id="tswchc_rules" name="tswchc_rules" value="<?php echo esc_attr(get_option('tswchc_rules')); ?>">

			<div class="buttons-wrapper">

				<a href="#" id="reset-settings" class="btn btn-light">Reset Settings</a>

				<div class="float-right">
					<?php submit_button(); ?>
				</div>

				<div id="ts-wchc-spinner" class="spinner-border text-info hidden" role="status">
					<span class="sr-only">Loading...</span>
				</div>

				<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="reset-settings-modal" aria-hidden="true" id="reset-settings-modal">
					<div class="modal-dialog modal-md">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title" id="reset-settings-modal">
									<p>
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#ffcc00" class="bi bi-exclamation-diamond-fill" viewBox="0 0 16 16">
											<path d="M9.05.435c-.58-.58-1.52-.58-2.1 0L.436 6.95c-.58.58-.58 1.519 0 2.098l6.516 6.516c.58.58 1.519.58 2.098 0l6.516-6.516c.58-.58.58-1.519 0-2.098L9.05.435zM8 4c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995A.905.905 0 0 1 8 4zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
										</svg>
									</p>
									This action will reset all your settings.
									<strong>Would you like to proceed?</strong>
								</h4>
							</div>
							<div class="modal-footer">
								<button id="clear-plugin-settings" type="button" class="btn btn-primary">Yes</button>
								<button id="modal-btn-no" type="button" class="btn btn-light">No</button>
							</div>
						</div>
					</div>
				</div>

				<script>
					jQuery(document).ready(function() {
						jQuery('#ts-wchc-wrapper #submit').removeClass('button button-primary').addClass('btn btn-success');
					})
				</script>

			</div>

		</form>

		<div class="banner">

			<div class="ts-card">
				<div class="ts-card-heading">
					<img src="<?php echo esc_html(TSWCHC_PLUGIN_URL) ?>/assets/img/Themesupport-Logo-H.svg" alt="Themesupport Logo">
					<h6>We do WordPress for you</h6>
				</div>

				<div class="ts-card-body">
					<a href="https://themesupport.com" target="_blank" class="ts-card-button button button-themesupport">Contact Us</a>
					<ul>
						<li>WordPress Hosting</li>
						<li>WordPress Development</li>
						<li>WordPress Maintenance</li>
						<li>WordPress Support</li>
						<li>Website SEO Service</li>
						<li>White Label Service</li>
					</ul>
				</div>

				<div class="card-footer">
					<a href="https://wordpress.org/support/plugin/hide-category-by-user-role-for-woocommerce/reviews/?rate=5#new-post" target="_blank" rel="noopener noreferrer">
						<h5>Found the plugin helpful?</h5>
						<div id="rating1" class="star-rating" role="rating" data-rating="3">
							<span class="star" data-value="1">&#9733;</span>
							<span class="star" data-value="2">&#9733;</span>
							<span class="star" data-value="3">&#9733;</span>
							<span class="star" data-value="4">&#9733;</span>
							<span class="star" data-value="5">&#9733;</span>
						</div>
						Your review matters!
					</a>
				</div>
			</div>


		</div>

	</div>

<?php
}
