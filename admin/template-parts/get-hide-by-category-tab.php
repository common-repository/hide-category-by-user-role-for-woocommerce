<?php

function tswchc_get_hide_by_category_tab() {

  $categories = tswchc_get_categories_hierarchy();
  $roles = tswchc_get_available_roles();
  $prev_rules = json_decode(get_option('tswchc_rules'));
  $format = ['separator' => ' / ', 'link' => false, 'inclusive' => false, ];

  $filter_args = array(
    "selector" => '#accordion-hide-by-role .accordion-header',
    'layout' => 'full',
    'target' => 'category',
    'placeholder' => 'Search'
  );

  tswchc_get_filter_template($filter_args);

?>

<div class="accordion" id="accordion-hide-by-role">

  <?php foreach ($categories as $key_cats => $category): ?>

    <?php
      $hierarchy = get_term_parents_list($category->term_id, 'product_cat', $format);
      $has_rules = tswchc_get_rules($category->slug, $prev_rules);
    ?>

    <div class="accordion-item">

      <h4 class="accordion-header" id="heading-<?php echo esc_html($key_cats) ?>"  data-category-name='<?php echo esc_html($hierarchy . $category->name . $category->term_id) ?>' data-category-slug='<?php echo esc_html($category->slug) ?>'>

        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo esc_html($key_cats) ?>" aria-expanded="true" aria-controls="collapse-<?php echo esc_html($key_cats) ?>">

          <?php if ($hierarchy): ?>

          <p><?php echo esc_html($hierarchy); ?></p>

          <?php endif; ?>

          <span class="<?php echo esc_html($category->slug); ?>">
            <?php echo ucfirst(esc_html($category->name)) ?>
            <small class="cat-id">ID: <?php echo esc_html($category->term_id); ?></small>
            <small id="counter-<?php echo esc_html($key_cats) ?>" class="rules-counter">
              <?php if ($has_rules): ?>
                (Hidden for <?php echo esc_html($has_rules) ?> Role<?php echo esc_html($has_rules > 1 ? 's' : '') ?>)
              <?php endif; ?>
            </small>
          </span>

        </button>

      </h4>

      <div id="collapse-<?php echo esc_html($key_cats) ?>" class="accordion-collapse collapse" aria-labelledby="heading-<?php echo esc_html($key_cats) ?>">

        <div class="accordion-body">

          <div class="actions-bar">

            <h6>Hide For</h6>

            <nav class="navbar navbar-expand-lg navbar-light bg-light">

              <div class="collapse navbar-collapse" id="navbarSupportedContent">

                <?php

                  $filter_args = array(
                    "selector" => '#btn-group-' . $category->slug . ' .btn-check',
                    'layout' => 'small',
                    'target' => 'role',
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

          <div id="btn-group-<?php echo esc_html($category->slug); ?>" class="btn-group btn-group-xs" data-counter="counter-<?php echo esc_html($key_cats) ?>" role="group-<?php echo esc_html($key_cats) ?>" aria-label="Roles Available on Site">

            <?php foreach ($roles as $key_roles => $role): ?>

              <div class="">

                <?php $checked = tswchc_is_checked($category->slug, $key_roles, $prev_rules) ?>

                <?php $cat_role_id = $category->slug . "-" . $key_roles; ?>

                <input type="checkbox" class="btn-check" data-type="role" data-type="category" data-category="<?php echo esc_html($category->slug); ?>" data-role="<?php echo esc_html($key_roles); ?>" id="<?php echo esc_html($cat_role_id) ?>" <?php echo esc_html($checked ? 'checked="checked"' : '') ?>autocomplete="off">
                <label class="btn btn-outline-primary" for="<?php echo esc_html($cat_role_id); ?>"><?php echo ucwords($role['name']) ?></label>

              </div>

            <?php endforeach; ?>

          </div>

        </div>

      </div>

    </div>

  <?php endforeach; ?>

</div>

<?php } ?>
