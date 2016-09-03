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

// components/com_blogfactory/assets/js/post.js
jQuery(document).ready(function ($) {
  // Post vote buttons.
  $('a', 'div.post-rating').click(function (event) {
    event.preventDefault();

    var elem = $(this);
    var url = elem.attr('href');

    $.post(url, function (response) {
      elem.parent().FactoryTooltipNotification(response);

      if (response.status) {
        var className = 'vote-' + response.vote;

        $('div.post-rating a.' + className + ' span').addClass('voted').text(response.total);

        $('div.post-rating a').each(function (index, element) {
          $(element).replaceWith($(element).html());
        });
      }
    }, 'json');
  });

  // Button subscribe.
  $('.button-subscribe').click(function (event) {
    event.preventDefault();

    var elem       = $(this);
    var icon       = elem.find('i:first');
    var url        = elem.attr('href');
    var subscribed = elem.attr('rel');

    if (icon.hasClass('factory-icon-loader')) {
      return false;
    }

    icon.toggleClass('factory-icon-loader factory-icon-mail');

    $.post(url, { subscribed: subscribed }, function (response) {
      icon.toggleClass('factory-icon-loader factory-icon-mail');

      elem.FactoryTooltipNotification(response);

      if (response.status) {
        elem.find('span:first').html(response.text);
        elem.attr('rel', (1 == subscribed ? 0 : 1));
      }
    }, 'json');

    return true;
  });
});
