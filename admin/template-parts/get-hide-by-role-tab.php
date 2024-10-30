<?php

function tswchc_get_hide_by_role_tab() {

  $categories = tswchc_get_categories_hierarchy();
  $roles = tswchc_get_available_roles();
  $prev_rules = json_decode(get_option('tswchc_rules'));
  $format = ['separator' => ' / ', 'link' => false, 'inclusive' => false, ];

  $filter_args = array(
    "selector" => '#accordion-hide-by-category .accordion-item',
    'layout' => 'full',
    'target' => 'role',
    'placeholder' => 'Search'
  );

  tswchc_get_filter_template($filter_args);

?>

<div class="accordion" id="accordion-hide-by-category">

  <?php foreach ($roles as $key_roles => $role): ?>

    <?php $has_rules = tswchc_get_rules($key_roles, $prev_rules, true); ?>

    <div class="">

      <div class="accordion-item"  data-role='<?php echo esc_html($key_roles); ?>'>

        <h4 class="accordion-header" id="heading-<?php echo esc_html($key_roles); ?>">

          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo esc_html($key_roles); ?>" aria-expanded="true" aria-controls="collapse-<?php echo esc_html($key_roles); ?>">

            <span class="<?php echo esc_html($key_roles); ?>">

              <?php echo ucfirst(esc_html($role['name'])) ?>

              <small id="counter-<?php echo esc_html($key_roles); ?>" class="rules-counter">
                <?php if ($has_rules): ?>
                  <?php echo esc_html($has_rules); ?> Hidden Categor<?php echo esc_html($has_rules > 1 ? 'ies' : 'y'); ?>
                <?php endif; ?>
              </small>
              
            </span>

          </button>

        </h4>

        <div id="collapse-<?php echo esc_html($key_roles); ?>" class="accordion-collapse collapse" aria-labelledby="heading-<?php echo esc_html($key_roles); ?>">

          <div class="accordion-body">

            <div class="actions-bar">

              <h6>Hide Categories</h6>

              <nav class="navbar navbar-expand-lg navbar-light bg-light">

                <div class="collapse navbar-collapse" id="navbarSupportedContent">

                  <?php   $filter_args = array(
                      "selector" => '#btn-group-' . $key_roles . ' .btn-check',
                      'layout' => 'small',
                      'target' => 'category',
                      'placeholder' => 'Filter'
                    );

                    tswchc_get_filter_template($filter_args);

                  ?>
                  <ul class="navbar-nav mr-auto">
                    <li class="nav-item active">
                      <button type="button" class="btn tswchc-clear-all btn-clear btn-sm">Clear All</button>
                    </li>
                    <li class="nav-item">
                      <button type="button" class="btn check-all btn-warning btn-sm">Check All</button>

                    </li>
                  </ul>

                </div>
              </nav>

            </div>

            <div id="btn-group-<?php echo esc_html($key_roles); ?>" class="btn-group btn-group-xs" data-counter="counter-<?php echo esc_html($key_roles); ?>" role="group-<?php echo esc_html($key_roles); ?>" aria-label="Products Categories">

              <?php foreach ($categories as $key_cats => $category): ?>

                <div class="">

                  <?php

                    $hierarchy = get_term_parents_list($category->term_id, 'product_cat', $format);
                    $checked = tswchc_is_checked($category->slug, $key_roles, $prev_rules);
                    $cat_role_id = $key_roles . "-" . $category->slug;

                  ?>

                  <input type="checkbox" class="btn-check"  data-category-name='<?php echo esc_html($hierarchy . $category->name . $category->term_id) ?>'
                          data-category-slug='<?php echo esc_html($category->slug); ?>' data-type="category" data-category="<?php echo esc_html($category->slug); ?>"
                          data-role="<?php echo esc_html($key_roles); ?>" id="<?php echo esc_html($cat_role_id); ?>" <?php echo esc_html($checked ? 'checked="checked"' : '') ?> autocomplete="off">
                  <label class="btn btn-outline-primary" for="<?php echo esc_html($cat_role_id); ?>">
                    <?php if ($hierarchy): ?>

                      <small><?php echo esc_html($hierarchy); ?></small>

                    <?php endif; ?>
                    <?php echo esc_html($category->name); ?>
                    <small>| ID: <?php echo esc_html($category->term_id); ?></small>
                  </label>

                </div>

              <?php endforeach; ?>

            </div>

          </div>

        </div>

      </div>

    </div>

  <?php endforeach; ?>

</div>

<?php } ?>
