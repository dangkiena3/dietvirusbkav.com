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
;

// components/com_blogfactory/assets/js/modal.js
(function($){
  $.extend({
    FactoryModal: function (options) {
      var modal = this;
      var defaults = {
        url: null,
        onSubmit: function (modal) {}
      };
      var opts = $.extend({}, defaults, options);

      this.init = function () {
        var body    = $('body');
        var overlay = $('<div></div>');
        var wrapper = $('<div></div>');
        var content = $('<div></div>');

        var bodyHeight = body.outerHeight();

        overlay.addClass('factory-modal-overlay');
        wrapper.addClass('factory-modal-wrapper');
        content.addClass('factory-modal-content');

        wrapper.append(content);
        body.append(overlay);
        body.append(wrapper);

        content.html('<i class="factory-icon-loader"></i>');

        body.css({
          overflow: 'hidden'
        });

        overlay.css({
          height: bodyHeight
        });

        modal.wrapper = wrapper;
        modal.overlay = overlay;
        modal.content = content;

        this.resize();
      }

      this.resize = function () {
        var bodyWidth = $('body').outerWidth();

        modal.wrapper.css({
          left: (bodyWidth - modal.wrapper.outerWidth()) / 2,
          top: $(document).scrollTop() + ($(window).height() - modal.wrapper.outerHeight()) / 2
        });
      }

      this.load = function (url) {
        $.get(url, function (response) {
          // Load data.
          modal.content.html(response);

          // Resize window.
          modal.resize();

          // Close button.
          modal.content.find('a.factory-modal-close').click(function (event) {
            event.preventDefault();

            modal.close();
          });

          // Submit button.
          modal.content.find('a.factory-modal-submit').click(function (event) {
            event.preventDefault();

            modal.content.addClass('factory-modal-loading');
            opts.onSubmit.call(this, modal);
          });
        });
      }

      this.close = function () {
        modal.overlay.remove();
        modal.wrapper.remove();

        $('body').css({
          overflow: 'auto'
        });
      }

      this.init();
      this.load(opts.url);

      return modal;
    }
  });
})(window.jQuery);
;

// components/com_blogfactory/assets/js/comments.js
jQuery(document).ready(function ($) {
  // Voting buttons.
  $('.comment-ratings a[class^="comment-vote-"]').click(function (event) {
    event.preventDefault();

    var elem      = $(this);
    var vote      = elem.hasClass('comment-vote-up') ? 1 : -1;
    var comment   = elem.parents('.blogfactory-post-comment:first');
    var commentId = comment.attr('id').replace('blogfactory-post-comment-', '');
    var iconClass = 'factory-icon-thumb-' + (1 == vote ? 'up' : 'down');

    elem.find('i:first').toggleClass(iconClass + ' factory-icon-loader');

    var data = {
      id: commentId,
      vote: vote
    }

    $.post(base_url + 'index.php?option=com_blogfactory&task=comment.vote', { format: 'raw', data: data },  function (response) {
      elem.find('i:first').toggleClass(iconClass + ' factory-icon-loader');

      elem.FactoryTooltipNotification(response);

      if (response.status) {
        comment.find('.comment-vote-up span').html(response.rating.up);
        comment.find('.comment-vote-down span').html(response.rating.down);
      }
    }, 'json');
  });

  // Delete button.
  $('a.comment-delete').click(function (event) {
    event.preventDefault();

    var elem   = $(this);
    var url    = elem.attr('href');
    var loader = elem.find('span:first');

    loader.show();

    $.get(url, function (response) {
      loader.hide();

      if (response.status) {
        elem.parents('.comment-bubble:first').html(response.message);
      }
      else {
        elem.FactoryTooltipNotification(response);
      }
    }, 'json');
  });

  // Report button.
  $('a.comment-report').click(function (event) {
    event.preventDefault();

    var elem = $(this);

    $.FactoryModal({
      url: $(this).attr('href'),
      onSubmit: function (modal) {
        var form = modal.content.find('form');

        $.ajax({
          url: form.attr('action'),
          type: form.attr('method'),
          data: form.serialize(),
          dataType: 'json',
          success: function (response) {
            modal.close();
            elem.FactoryTooltipNotification(response);
          }
        });
      }
    });
  });
});
