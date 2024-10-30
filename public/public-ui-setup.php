<?php

/*
* The plugin's own styles and scripts are added
*/
add_action('wp_enqueue_scripts', 'tswchc_enqueue_css_js_front');
function tswchc_enqueue_css_js_front($hook) {
	wp_enqueue_style('ts_wchc-admin-main-css', TSWCHC_PLUGIN_URL . 'assets/css/plugin_style.css', '', TSWCHC_VERSION_NUM);
}

/*
* Prevents hidden products from being added to the cart
*/
add_filter('woocommerce_add_to_cart_validation', 'filter_wc_add_to_cart_validation', 10, 3);
function filter_wc_add_to_cart_validation($passed, $product_id, $quantity) {

	$product = wc_get_product($product_id);
	$terms = get_the_terms($product->get_ID(), 'product_cat');

	if (!is_array($terms)) {
		wc_add_notice(__("The product you are trying to add is not available", "woocommerce"), 'error');
		return false;
	}

	return $passed;
}

/*
* Removes hidden products from the cart in case any of them have been added
*/
add_action('woocommerce_check_cart_items', 'filter_wc_check_cart_items');
function filter_wc_check_cart_items() {

	$cart = WC()->cart;
	$cart_items = $cart->get_cart();
	$has_virtual = $has_physical = false;

	foreach (WC()->cart->get_cart() as $cart_item) {

		$product = wc_get_product($cart_item['product_id']);
		$terms = get_the_terms($product->get_ID(), 'product_cat');

		if (!is_array($terms)) {
			WC()->cart->remove_cart_item($cart_item['key']);
			wc_add_notice(__("The product " . $product->get_name() . " is not available and was removed from your cart", "woocommerce"), 'error');
		}
	}
}

/*
* Returns the rules set in the backend of the plugin
*/
add_action('plugins_loaded', 'tswchc_get_hide_rules', 1);
function tswchc_get_hide_rules() {

	$hide_cats = array();
	$hide_rules  = array();

	if (!is_admin()) {

		$user = wp_get_current_user();

		$user_roles = (array) $user->roles;
		$hide_rules = json_decode(get_option('tswchc_rules'));

		if ($user->ID) {

			if (is_array($hide_rules) && is_array($user_roles)) {

				foreach ($user_roles as $key => $user_role) {

					foreach ($hide_rules as $key => $rule) {

						$hide = 0;

						if ($user_role == $rule->role) {

							$hide++;
						}

						if (count($user_roles) == $hide) {

							if (!in_array($rule->category, $hide_cats)) {

								$hide_cats[] = $rule->category;
							}
						}
					}
				}
			}
		} else {

			if (is_array($hide_rules)) {

				foreach ($hide_rules as $key => $rule) {

					if ('guest' == $rule->role) {

						if (!in_array($rule->category, $hide_cats)) {

							$hide_cats[] = $rule->category;
						}
					}
				}
			}
		}
	}

	return $hide_cats;
}

/*
* Add parameters to exclude hidden categories from WP query
*/
add_action('woocommerce_product_query', 'tswchc_hide_products_category', 1);
function tswchc_hide_products_category($q) {

	if (!is_admin() || is_shop()) {

		$tax_query = (array) $q->get('tax_query');

		$hide_cats = tswchc_get_hide_rules();

		$tax_query[] = array(
			'taxonomy' => 'product_cat',
			'field' => 'slug',
			'terms' => $hide_cats,
			'operator' => 'NOT IN'
		);

		$q->set('tax_query', $tax_query);
	}
}

/*
* Ensures that the correct subcategories are displayed after hiding the parent
* categories set in the backend
*/
add_filter('get_terms', 'tswchc_get_subcategory_terms', 10, 3);
function tswchc_get_subcategory_terms($terms, $taxonomies, $args) {

	$new_terms = array();
	$hide_cats = tswchc_get_hide_rules();

	if (in_array('product_cat', $taxonomies) && !is_admin()) {

		foreach ($terms as $key => $term) {

			// Check if $term is an object, if not, assume it's an ID or slug and fetch the term
			if (!is_object($term)) {

				$term_str = (string) $term;  // Explicitly cast $term to string

				if (ctype_digit($term_str)) {

					// If $term is a numeric string, treat it as an ID
					$term = get_term('id', (int) $term, 'product_cat');
				} else {
					// Otherwise, treat it as a slug
					$term = get_term_by('slug', $term, 'product_cat');
				}
			}

			// Check if $term is an object
			if (is_object($term) && property_exists($term, 'slug') && !in_array($term->slug, $hide_cats)) {
				$new_terms[] = $term;
			}
		}
		$terms = $new_terms;
	}

	return $terms;
}

/*
* If a related product belongs to any hidden category, it will not be displayed in the related products section.
*/

add_filter('woocommerce_related_products', 'tswchc_exclude_related_products', 999, 3);

function tswchc_exclude_related_products($related_posts, $product_id, $args) {
	$excluded_ids = array();
	$hide_cats = tswchc_get_hide_rules(); // Function that returns an array of category slugs to hide

	// Loop through related posts to check their categories
	foreach ($related_posts as $related_post_id) {
		// Get the categories of the related product
		$product_cats = get_the_terms($related_post_id, 'product_cat');

		// If no categories or any parent category is hidden, exclude the related product
		if (empty($product_cats) || tswchc_has_hidden_parent_category($product_cats, $hide_cats)) {
			$excluded_ids[] = $related_post_id;
		}
	}

	return array_diff($related_posts, $excluded_ids);
}

/*
* If a up-sell product belongs to any hidden category, it will not be displayed in the up-sell products section.
*/

add_filter('woocommerce_product_get_upsell_ids', 'tswchc_custom_upsell_ids', 20, 2);

function tswchc_custom_upsell_ids($upsell_ids, $product) {
	$excluded_ids = array();
	$hide_cats = tswchc_get_hide_rules(); // Function that returns an array of category slugs to hide

	// Loop through upsell product IDs to check their categories
	foreach ($upsell_ids as $related_post_id) {
		// Get the categories of the upsell product
		$product_cats = get_the_terms($related_post_id, 'product_cat');

		// If no categories or any parent category is hidden, exclude the upsell product
		if (empty($product_cats) || tswchc_has_hidden_parent_category($product_cats, $hide_cats)) {
			$excluded_ids[] = $related_post_id;
		}
	}

	return array_diff($upsell_ids, $excluded_ids);
}

/*
* If a cross-sell product belongs to any hidden category, it will not be displayed in the cross-sell products section.
*/

add_filter('woocommerce_product_get_cross_sell_ids', 'tswchc_custom_cross_sell_ids', 20, 2);

function tswchc_custom_cross_sell_ids($cross_sell_ids, $product) {
	$excluded_ids = array();
	$hide_cats = tswchc_get_hide_rules(); // Function that returns an array of category slugs to hide

	// Loop through cross-sell product IDs to check their categories
	foreach ($cross_sell_ids as $related_post_id) {
		// Get the categories of the cross-sell product
		$product_cats = get_the_terms($related_post_id, 'product_cat');

		// If no categories or any parent category is hidden, exclude the cross-sell product
		if (empty($product_cats) || tswchc_has_hidden_parent_category($product_cats, $hide_cats)) {
			$excluded_ids[] = $related_post_id;
		}
	}

	return array_diff($cross_sell_ids, $excluded_ids);
}

/*
* Check if any category or its parent categories are hidden.
*/

function tswchc_has_hidden_parent_category($categories, $hide_cats) {
	foreach ($categories as $category) {
		$parent_id = $category->parent;
		while ($parent_id) {
			$parent_category = get_term($parent_id, 'product_cat');
			if (in_array($parent_category->slug, $hide_cats)) {
				return true; // If any parent category is hidden, return true
			}
			$parent_id = $parent_category->parent;
		}
	}
	return false; // If no hidden parent categories found, return false
}

/*
* Display a message or redirect users according to the settings applied in the plugin
*/
add_action('wp', 'tswchc_redirect_product_pages', 10);
function tswchc_redirect_product_pages() {

	nocache_headers();

	$redirect_mode = get_option('tswchc_redirect_mode');
	$queried_object = get_queried_object();

	if (!is_admin()) {

		if (is_archive()) {

			$terms = false;
			global $post;

			if (isset($post->ID)) {
				$terms = get_the_terms($post->ID, 'product_cat');
			}
		} else if (is_product()) {

			if (isset($queried_object->ID)) {

				$terms = get_the_terms($queried_object->ID, 'product_cat');

				if ($queried_object->taxonomy == 'product_cat' && !is_admin() && is_array($terms)) {

					$no_terms = true;

					foreach ($terms as $key => $term) {

						if ($queried_object->slug == $term->slug) {

							$no_terms = false;
						}
					}

					if ($no_terms) {
						$terms = false;
					}
				}
			}
		}

		if ((is_product() || is_archive()) && !$terms) {

			if ($redirect_mode == "url" && !is_admin()) {

				$shop_page_url = esc_attr(get_option('tswchc_redirect_url'));

				if (!$shop_page_url) {
					$shop_page_url = wc_get_page_permalink('shop');
				}

				if (!is_shop()) {
					wp_safe_redirect($shop_page_url);
					exit;
				}
			} else {

				if (!is_admin()) {
					add_action('wp', 'tswchc_dysplay_custom_message', 999);
				}
			}
		}
	}
}

/*
* Shows the message added in the backend, prevents the other elements of the site
* from loading
*/
function tswchc_dysplay_custom_message() {

	get_header();

	$content = get_option('tswchc_dysplay_custom_message');
	$wrapper = get_option('tswchc_message_wrapper');
	$styles = get_option('tswchc_message_styles');

	echo '<div id="ts-wchc-message">' . $content . '</div>';

	if ($wrapper) {

		echo '<script>
	    document.addEventListener("DOMContentLoaded", function () {
	        var messageDiv = document.getElementById("ts-wchc-message");
	        var mainContentContainer = document.querySelector("' . $wrapper . '");
					if (messageDiv && mainContentContainer) {
	            mainContentContainer.appendChild(messageDiv);
	        }
			mainContentContainer.classList.add("ts-wchc-mtop");
	    });
		</script>';
	}

	echo '<style>
		.ts-wchc-mtop {
			margin-top: 40px;
		}
	</style>';

	if ($styles) {
		echo '<style>
		' . tswchc_css_rules_worker($styles) . '
		</style>';
	}

	get_footer();
}

/*
* Removes hidden products and categories from the WP main query preventing them
*	from being displayed by other plugins
*/
function woocommerce_pre_get_posts($query) {

	if (!is_admin() && $query->is_main_query()) {

		tswchc_hide_products_category($query);
	}
}
add_action('pre_get_posts', 'woocommerce_pre_get_posts', 20);
