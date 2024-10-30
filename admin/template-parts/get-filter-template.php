<?php

function tswchc_get_filter_template($args = array("selector" => '', 'layout' => 'full', 'target' => 'accordion', 'placeholder' => 'Search' )) { ?>

  <div data-selector="<?php echo esc_html($args['selector']) ?>" data-target="<?php echo esc_html($args['target']) ?>" class="ts-wchc-filter-wrapper input-group <?php echo esc_html($args['layout']) ?>">

    <input class="form-control ts-wchc-filter" type="text" placeholder="<?php echo esc_html($args['placeholder']) ?>" aria-label="Search">

    <?php if ($args['layout'] != 'small'): ?>

      <div class="input-group-append">
        <span class="input-group-text">
          <i class="fas fa-search text-grey" aria-hidden="true"></i>
        </span>
      </div>

    <?php endif; ?>

  </div>

<?php } ?>
