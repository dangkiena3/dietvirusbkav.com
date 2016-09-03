// components/com_blogfactory/assets/js/list.js
jQuery(document).ready(function ($) {
  // Check all checkbox.
  $('input[type="checkbox"].check-all').change(function (event) {
    var elem    = $(this);
    var checked = elem.is(':checked');

    $('input[type="checkbox"].check-all').attr('checked', checked);
    elem.parents('table:first').find('input[type="checkbox"][name="batch[]"]').attr('checked', checked);
  });

  // Batch actions button.
  $('.button-batch a').click(function (event) {
    event.preventDefault();

    var elem = $(this);
    var form = $('#blogfactory-form-list');

    // Check if any item was selected.
    var selected = $('input[type="checkbox"][name="batch[]"]:checked', 'table.blogfactory-list');

    if (!selected.length) {
      return true;
    }

    $('#task').val(elem.attr('data-task'));
    form.submit();

    return true;
  });
});
;

// components/com_blogfactory/assets/js/tooltip.js
(function($){
  var FactoryTooltip = function(element, options){
    this.init('tooltip', element, options);
  }

  FactoryTooltip.prototype = $.extend({}, $.fn.tooltip.Constructor.prototype, {
    constructor: FactoryTooltip,
    tip: function () {
      var tip = this.$tip = this.$tip || $(this.options.template);

      if (undefined !== this.options.className || '' !== this.options.className) {
        tip.addClass(this.options.className);
      }

      return tip;
    }
  });

  $.fn.FactoryTooltip = function(option){
    return this.each(function() {
      var $this = $(this)
      , data = $this.data('tooltip')
      , options = typeof option == 'object' && option;
      if(!data) $this.data('tooltip', (data = new FactoryTooltip(this, options)));
      if(typeof option == 'string') data[option]();
    });
  }
})(window.jQuery);
;

// components/com_blogfactory/assets/js/notification.js
(function($){
  var timer = null;
  var tooltip = null;

  $.fn.extend({
    FactoryTooltipNotification: function (response) {
      return this.each(function() {
        var elem      = $(this);
        var className = response.status ? '' : 'tooltip-error';
        var message   = response.message;

        if (response.error) {
          message +=  '&nbsp;' + response.error
        }

        if (null != tooltip) {
          tooltip.FactoryTooltip('hide');
        }

        elem
          .FactoryTooltip('destroy')
          .FactoryTooltip({ title: message, trigger: 'manual', className: className })
          .FactoryTooltip('show');

        if (null != timer) {
          clearTimeout(timer);
        }

        timer = setTimeout(function () {
          elem.FactoryTooltip('hide');
        }, 5 * 1000);

        tooltip = elem;
      });
    }
  });

  $('html').click(function () {
    if (null != tooltip) {
      tooltip.FactoryTooltip('hide');
    }
  });
})(window.jQuery);
;

// components/com_blogfactory/assets/js/managesubscriptions.js
jQuery(document).ready(function ($) {
  // Subscription delete button.
  $('.item-delete').click(function (event) {
    event.preventDefault();

    var elem   = $(this);
    var url    = elem.attr('href');
    var loader = elem.find('i:first');

    loader.show();

    $.post(url, function (response) {
      loader.hide();

      if (!response.status) {
        elem.FactoryTooltipNotification(response);
      }
      else {
        elem.parents('tr:first').html('<td colspan="10">' + response.message + '</td>');
      }
    }, 'json');
  });
});
