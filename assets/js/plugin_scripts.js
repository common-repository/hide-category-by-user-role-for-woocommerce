(function (jQuery) {

  jQuery(document).ready(function () {

    /***************************************************************************/

    jQuery('#ts-wchc-spinner').appendTo(jQuery('p.submit'));

    var hide_rules = [];
    var prev_rules = jQuery('#tswchc_rules').val();

    if (!prev_rules) {
      prev_rules = [];
    }

    if (prev_rules.length) {

      hide_rules = JSON.parse(prev_rules);

    }

    jQuery('#ts-wchc-wrapper .btn-check').click(function () {

      var category = jQuery(this).data('category');
      var role = jQuery(this).data('role');

      var rule = {
        "category": category,
        "role": role
      };

      if (!tswchc_exists(rule)) {

        hide_rules.push(rule);

      } else {

        tswchc_remove_rule(rule);

      }

      var input_rules = "";

      if (hide_rules.length) {

        var input_rules = JSON.stringify(hide_rules);

      }

      jQuery('#tswchc_rules').val(input_rules);

      tswchc_update_rules_counters(jQuery(this));

    });

    /***/

    jQuery('#ts-wchc-wrapper .check-all').click(function () {

      jQuery('#ts-wchc-wrapper').addClass('loading');

      var accordion_body = jQuery(this).parents('.accordion-body');

      setTimeout(function () {

        jQuery(accordion_body).find('.btn-check').each(function () {

          if (!jQuery(this).prop('checked')) {
            jQuery(this).prop('checked', 'checked')
          }

          var category = jQuery(this).data('category');
          var role = jQuery(this).data('role');

          var rule = {
            "category": category,
            "role": role
          };

          if (!tswchc_exists(rule)) {

            hide_rules.push(rule);

          }

          tswchc_update_rules_counters(jQuery(this));

          var input_rules = "";

          if (hide_rules.length) {

            var input_rules = JSON.stringify(hide_rules);

          }

          jQuery('#tswchc_rules').val(input_rules);

          jQuery('#ts-wchc-wrapper').removeClass('loading');

        }, 100)

      })


    });

    /**/

    jQuery('#ts-wchc-wrapper .tswchc-clear-all').click(function () {

      jQuery('#ts-wchc-wrapper').addClass('loading');

      var accordion_body = jQuery(this).parents('.accordion-body');

      setTimeout(function () {

        jQuery(accordion_body).find('.btn-check').each(function () {

          if (jQuery(this).prop('checked')) {
            jQuery(this).prop('checked', '')
          }

          var category = jQuery(this).data('category');
          var role = jQuery(this).data('role');

          var rule = {
            "category": category,
            "role": role
          };

          if (tswchc_exists(rule)) {

            tswchc_remove_rule(rule);

          }

          tswchc_update_rules_counters(jQuery(this));

        })

        var input_rules = "";

        if (hide_rules.length) {

          var input_rules = JSON.stringify(hide_rules);

        }

        jQuery('#tswchc_rules').val(input_rules);

        jQuery('#ts-wchc-wrapper').removeClass('loading');

      }, 100);


    });

    /**/

    jQuery('#ts-wchc-wrapper  #ts-wchc-form').submit(function () {

      jQuery('#ts-wchc-spinner').fadeIn(300);

      jQuery('#ts-wchc-wrapper  #submit').prop('disabled', true);

    });

    /**/

    function tswchc_ajax_worker(data) {

      jQuery('#ts-wchc-wrapper').addClass('loading');

      jQuery.ajax({
        type: 'POST',
        url: tswchc_ajax_object.ajax_url,
        data: data,

        success: function (response) {

          response = JSON.parse(response);

          jQuery('#ts-wchc-wrapper').removeClass('loading');

          switch (data.action) {
            case 'tswchc_generate_plugin_options_json':
              tswchc_generate_download_link(response.file_path);
              break;

            case 'tswchc_import_plugin_options_json':
              tswchc_display_import_message(response.message, response.errors);
              break;

            case 'tswchc_reset_plugin_options':
              location.reload();
              break;


            default:
              break;
          }

          jQuery('#ts-wchc-spinner').appendTo(jQuery('p.submit')).hide();

          console.log(response);

        },
        error: function (response) {

          alert('Network error');

        }

      });

    }

    /**/

    function tswchc_exists(rule) {

      var _exists = false;

      jQuery(hide_rules).each(function (x, y) {

        if (rule.category == y.category && rule.role == y.role) {

          _exists = true;

        }

      });

      return _exists;

    }

    /**/

    function tswchc_remove_rule(rule) {

      jQuery(hide_rules).each(function (x, y) {

        if (rule.category == y.category && rule.role == y.role) {

          hide_rules.splice(x, 1);

        }

      });

    }

    /**/

    function tswchc_update_rules_counters(element) {

      jQuery('.btn-check').not(jQuery(element)).each(function () {

        if (jQuery(this).data('category') == jQuery(element).data('category') && jQuery(this).data('role') == jQuery(element).data('role')) {

          var prop_checked = jQuery(element).prop('checked');

          jQuery(this).prop('checked', prop_checked).change();

        }

      })

      jQuery('.btn-group').each(function (x, button_g) {

        var counter = 0;
        var new_counter_text = "";
        var type = "";

        jQuery(this).find(':checked').each(function () {

          counter++;
          type = jQuery(this).data('type');

        });

        if (counter > 0) {

          if (type == "role") {

            new_counter_text = "Hidden for " + counter + " Role" + (counter > 1 ? "s" : "");

          } else {

            new_counter_text = counter + " Hidden Categor" + (counter > 1 ? "ies" : "y");

          }

        }

        jQuery('small#' + jQuery(button_g).data('counter')).text(new_counter_text);

      });

    }

    /**/

    jQuery('#tswchc_redirect_mode').change(function () {

      var selected = jQuery(this).find(":selected").val();

      jQuery('.redirect-mode').each(function () {

        jQuery(this).fadeOut(0);

        if (jQuery(this).data('mode') == selected) {

          jQuery(this).fadeIn();

        }

      })

    })

    /**/

    function delay(callback, ms) {
      var timer = 0;
      return function () {
        var context = this,
          args = arguments;
        clearTimeout(timer);
        timer = setTimeout(function () {
          callback.apply(context, args);
        }, ms || 0);
      };
    }

    /**/

    function tswchc_generate_download_link(file_path) {

      var link = '<p><a href="' + file_path + '" class="link-success" target="blank">Download Settings</a></p>';

      jQuery(link).insertAfter(jQuery('#tswchc-export-settings'));

    }

    /**/

    function tswchc_display_import_message(message, success) {

      var message_class = "text-success";

      if (success) {
        message_class = "text-warning";
      }

      var message = '<p class="' + message_class + '">' + message + '</p>';

      jQuery(message).insertAfter(jQuery('#tswchc-import-settings'));

    }

    /***/

    jQuery('.ts-wchc-filter').keyup(delay(function (e) {

      var value = jQuery(this).val();
      value = value.toLowerCase();

      var selector = jQuery(this).parent().data('selector');
      var target = jQuery(this).parent().data('target');

      if (target == 'category') {

        jQuery(selector).each(function () {

          var cat_name = jQuery(this).data('category-name').toLowerCase();
          var cat_slug = jQuery(this).data('category-slug').toLowerCase();

          if (!cat_name.includes(value) && !cat_slug.includes(value)) {

            jQuery(this).parent().addClass('hidden');

          } else {

            jQuery(this).parent().removeClass('hidden');

          }

        })

      } else if (target == 'role') {

        jQuery(selector).each(function () {

          var role = jQuery(this).data('role').toLowerCase();

          if (!role.includes(value)) {

            jQuery(this).parent().addClass('hidden');

          } else {

            jQuery(this).parent().removeClass('hidden');

          }

        })

      }

    }, 300));

    /***/

    jQuery('#reset-settings').click(function (event) {
      event.preventDefault();
      event.stopPropagation();
      jQuery("#reset-settings-modal").modal('show');
    });

    jQuery('#clear-plugin-settings').click(function (event) {

      event.preventDefault();

      jQuery('#ts-wchc-spinner').insertAfter(jQuery(this));

      jQuery('#ts-wchc-spinner').fadeIn(300);

      jQuery('#reset-settings-modal button').prop('disabled', true);

      var data = {
        action: 'tswchc_reset_plugin_options',
        nonce: tswchc_ajax_object.nonce,
      };

      tswchc_ajax_worker(data);

      jQuery('#reset-settings-modal .modal-footer').append('<span>Reloading the page...</span>');

    });

    jQuery('#modal-btn-no').click(function () {
      jQuery("#reset-settings-modal").modal('hide');
    });

    /**/

    jQuery('#tswchc-export-settings').click(function (event) {

      event.preventDefault();

      jQuery('#ts-wchc-spinner').insertAfter(jQuery(this));

      jQuery('#ts-wchc-spinner').fadeIn(300);

      var data = {
        action: 'tswchc_generate_plugin_options_json',
        nonce: tswchc_ajax_object.nonce,
      };

      tswchc_ajax_worker(data);

      console.log(data);

    });

    /**/

    var json_settings = '';

    jQuery('#settings_file').on('change', function (event) {
      const file_input = event.target;
      const file = file_input.files[0];

      if (!file) {
        alert('No file selected.');
        return;
      }

      const reader = new FileReader();

      reader.onload = function (e) {
        try {
          const json_data = JSON.parse(e.target.result);
          json_settings = json_data;
          jQuery('#tswchc-import-settings').removeClass('disabled');
        } catch (error) {
          console.error('Error parsing JSON:', error);
          alert('Error parsing JSON. Please check if the file is valid JSON.');
        }
      };

      reader.readAsText(file);

    });

    jQuery('#tswchc-import-settings').click(function (event) {

      event.preventDefault();

      jQuery('#ts-wchc-spinner').insertAfter(jQuery(this));

      jQuery('#ts-wchc-spinner').fadeIn(300);

      var data = {
        action: 'tswchc_import_plugin_options_json',
        settings: json_settings,
        nonce: tswchc_ajax_object.nonce,
      };

      tswchc_ajax_worker(data);

    });

    /***/

    setTimeout(function () {

      jQuery('#tswchc_redirect_mode').change();

    }, 100)

  })


}(jQuery));
