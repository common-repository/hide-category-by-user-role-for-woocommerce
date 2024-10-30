<?php

function tswchc_get_settings_tab() {

  $categories = tswchc_get_categories_hierarchy();
  $roles = tswchc_get_available_roles();
  $prev_rules = json_decode(get_option('tswchc_rules'));
  $format = ['separator' => ' / ', 'link' => false, 'inclusive' => false,];

?>

  <div class="accordion" id="accordion-settings">

    <div class="accordion-item">

      <h4 class="accordion-header">

        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-redirect-mode" aria-expanded="true" aria-controls="collapse-redirect-mode">

          Redirect Mode

        </button>

      </h4>

      <div id="collapse-redirect-mode" class="accordion-collapse collapse show" aria-labelledby="heading-redirect-mode">

        <div class="accordion-body">

          <?php ?>

          <div class="form-group">

            <label for="tswchc_redirect_mode">Redirect Mode</label>

            <select class="form-select" id="tswchc_redirect_mode" name="tswchc_redirect_mode" aria-label="ML App Country">
              <option value="url" <?php echo esc_html(get_option('tswchc_redirect_mode') == 'url' ? 'selected="selected"' : '') ?>>Custom URL</option>
              <option value="display-message" <?php echo esc_html(get_option('tswchc_redirect_mode') == 'display-message' ? 'selected="selected"' : '') ?>>Display a Message</option>
            </select>

            <small class="form-text text-muted">Select if you want to redirect users to an URL or display a custom message if they attempt to access a hidden category or product page.</small>

          </div>

          <div class="form-group redirect-mode hidden" data-mode="url">

            <label for="tswchc_redirect_url">Redirect URL</label>
            <input type="text" class="form-control" id="tswchc_redirect_url" name="tswchc_redirect_url" value="<?php echo esc_attr(get_option('tswchc_redirect_url')); ?>">
            <small class="form-text text-muted">Redirects to shop page if empty | <i><?php echo wc_get_page_permalink('shop') ?></i></small>

          </div>

          <div class="form-group redirect-mode hidden" data-mode="display-message">

            <label for="tswchc_dysplay_custom_message">Message to Display</label>
            <?php
            $content = get_option('tswchc_dysplay_custom_message');
            wp_editor($content, 'tswchc_dysplay_custom_message', $settings = array('textarea_rows' => '25', 'editor_height' => 300,));
            ?>

          </div>

          <div class="form-group redirect-mode hidden" data-mode="display-message">
            <label for="tswchc_message_wrapper">Message Wrapper</label>
            <input type="text" class="form-control" id="tswchc_message_wrapper" name="tswchc_message_wrapper" value="<?php echo esc_attr(get_option('tswchc_message_wrapper')); ?>">
            <small class="form-text text-muted">
              Provide the selector for the element that will contain the message.<br>
              Use ID or Class selectors. For example: <i>#element-id</i> <strong>OR</strong> <i>.class-name</i>
            </small>
          </div>

          <div class="form-group redirect-mode hidden" data-mode="display-message">
            <label for="tswchc_message_styles">Message Styles</label>
            <textarea class="form-control" id="tswchc_message_styles" name="tswchc_message_styles" rows="8"><?php echo esc_attr(get_option('tswchc_message_styles')); ?></textarea>
            <small class="form-text text-muted">Add styles to personalize your message</small>
          </div>

        </div>

      </div>

    </div>

    <div class="accordion-item">

      <h4 class="accordion-header">

        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-advanced-settings" aria-expanded="true" aria-controls="collapse-advanced-settings">

          Advanced Settings

        </button>

      </h4>

      <div id="collapse-advanced-settings" class="accordion-collapse collapse" aria-labelledby="heading-advanced-settings">

        <div class="accordion-body">

          <div class="form-group">

            <div class="row">

              <div class="col-md-6 how-img">
                <label for="tswchc_import_data">IMPORT</label>
                <small class="form-text text-muted">Select and upload a JSON settings file.</small>
                <input type="file" name="settings_file" id="settings_file" accept="application/JSON">

                <a href="#" id="tswchc-import-settings" class="btn btn-secondary disabled">Import Settings</a>
              </div>
              <div class="col-md-6">
                <label for="tswchc_export_data">EXPORT</label>
                <small class="form-text text-muted">Click in order to generate a download link.</small>
                <a href="#" id="tswchc-export-settings" class="btn btn-secondary">Export Settings</a>
              </div>
            </div>

          </div>

        </div>

      </div>

    </div>

  </div>

<?php } ?>