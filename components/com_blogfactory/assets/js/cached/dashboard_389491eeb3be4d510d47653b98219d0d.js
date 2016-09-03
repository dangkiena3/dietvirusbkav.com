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

// components/com_blogfactory/assets/js/jquery.cookie.js
/*!
 * jQuery Cookie Plugin v1.3.1
 * https://github.com/carhartl/jquery-cookie
 *
 * Copyright 2013 Klaus Hartl
 * Released under the MIT license
 */
(function (factory) {
	if (typeof define === 'function' && define.amd) {
		// AMD. Register as anonymous module.
		define(['jquery'], factory);
	} else {
		// Browser globals.
		factory(jQuery);
	}
}(function ($) {

	var pluses = /\+/g;

	function raw(s) {
		return s;
	}

	function decoded(s) {
		return decodeURIComponent(s.replace(pluses, ' '));
	}

	function converted(s) {
		if (s.indexOf('"') === 0) {
			// This is a quoted cookie as according to RFC2068, unescape
			s = s.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, '\\');
		}
		try {
			return config.json ? JSON.parse(s) : s;
		} catch(er) {}
	}

	var config = $.cookie = function (key, value, options) {

		// write
		if (value !== undefined) {
			options = $.extend({}, config.defaults, options);

			if (typeof options.expires === 'number') {
				var days = options.expires, t = options.expires = new Date();
				t.setDate(t.getDate() + days);
			}

			value = config.json ? JSON.stringify(value) : String(value);

			return (document.cookie = [
				config.raw ? key : encodeURIComponent(key),
				'=',
				config.raw ? value : encodeURIComponent(value),
				options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
				options.path    ? '; path=' + options.path : '',
				options.domain  ? '; domain=' + options.domain : '',
				options.secure  ? '; secure' : ''
			].join(''));
		}

		// read
		var decode = config.raw ? raw : decoded;
		var cookies = document.cookie.split('; ');
		var result = key ? undefined : {};
		for (var i = 0, l = cookies.length; i < l; i++) {
			var parts = cookies[i].split('=');
			var name = decode(parts.shift());
			var cookie = decode(parts.join('='));

			if (key && key === name) {
				result = converted(cookie);
				break;
			}

			if (!key) {
				result[name] = converted(cookie);
			}
		}

		return result;
	};

	config.defaults = {};

	$.removeCookie = function (key, options) {
		if ($.cookie(key) !== undefined) {
			// Must not alter options, thus extending a fresh object...
			$.cookie(key, '', $.extend({}, options, { expires: -1 }));
			return true;
		}
		return false;
	};

}));
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

// components/com_blogfactory/assets/js/media.js
(function($) {
  $.extend({
    BlogFactoryMediaManager: function () {
      var manager = this;
      var initialised = false;
      var uploading = false;
      var wrapper, overlay, media, activeFolder, activeFile, xmlHttpRequest;
      var options = {
        mode: 'editor'
      }

      this.init = function () {
        initialised = true;

        wrapper = $('<div id="blogfactory-media-manager"></div>');
        media   = $('<div class="blogfactory-media-explorer blogfactory-media-pane loading"></div>');
        overlay = $('<div class="blogfactory-media-manager-overlay"></div>');

        wrapper.append(overlay, media);
        $('body').append(wrapper);

        media.load(base_url + 'index.php?option=com_blogfactory&view=media&format=raw', function () {
          // Remove loading class.
          media.removeClass('loading');

          // Close button.
          $('.pane-buttons')
          .on('click', '.media-close', function (event) {
            event.preventDefault();

            manager.close();
          })
          .on('click', '.button-new-folder', function (event) {
            event.preventDefault();

            media.find('.pane-new-folder').show();
          })
          .on('click', '.button-delete', function (event) {
            event.preventDefault();

            var elem = $(this);
            var icon = elem.find('i:first');
            var active = media.find('.active');

            if (!active.length) {
              return false;
            }

            // Check if we are removing the user folder.
            if (active.find('i:eq(1)').hasClass('factory-icon-blue-folder')) {
              return false;
            }

            icon.toggleClass('factory-icon-cross factory-icon-loader');

            if (active.parents('.pane-folders').length) {
              // We are removing a folder.
              var folder = active.attr('href').replace('#', '');

              $.post(base_url + 'index.php?option=com_blogfactory&task=media.removefolder&format=raw', { folder: folder }, function (response) {
                icon.toggleClass('factory-icon-cross factory-icon-loader');

                if (response.status) {
                  active.parents('li:eq(1)').find('a:first').trigger('activate');
                  active.parents('li:first').remove();

                  // Update free space.
                  $('.space-remaining-value').html(response.free_space);
                }
              }, 'json');
            }
            else if (active.parents('.pane-files').length) {
              // We are removing a file.
              var file = active.find('.title').text()

              $.post(base_url + 'index.php?option=com_blogfactory&task=media.removefile&format=raw', { folder: activeFolder, file: file }, function (response) {
                icon.toggleClass('factory-icon-cross factory-icon-loader');

                $('.details', '.pane-file-details').hide();

                if (response.status) {
                  active.remove();
                  $('.pane-status').trigger('updateFile');

                  // Update free space.
                  $('.space-remaining-value').html(response.free_space);
                }
              }, 'json');
            }

            return true;
          })
          .on('click', '.button-upload', function (event) {
            event.preventDefault();

            media.find('.pane-files .active').removeClass('active');
            $('.pane-status').trigger('updateFile');

            media.find('.pane-files, .pane-folders, .pane-file-details').hide();
            media.find('.pane-upload').show();
          })
          ;

          $('.pane-new-folder')
          .on('click', '.button-cancel', function (event) {
            event.preventDefault();

            $(this).parents('.pane-new-folder').hide();
          })
          .on('click', '.button-submit', function (event) {
            event.preventDefault();

            var elem = $(this);
            var loader = elem.find('i:first');
            var pane = $('.pane-new-folder');
            var title = pane.find('#folder_title');

            loader.show();

            $.post(
            base_url + 'index.php?option=com_blogfactory&task=media.createfolder&format=raw',
            { title: title.val(), folder: activeFolder },
            function (response) {
              loader.hide();

              if (response.status) {
                title.val('');
                pane.hide();

                var active = $('.pane-folders').find('a[href="#' + activeFolder + '"]');
                var fold = active.find('i:first');
                var ul = active.parents('li:first').find('ul:first');

                active.addClass('foldable');

                if (fold.hasClass('factory-icon-blank')) {
                  fold.toggleClass('factory-icon-blank factory-icon-toggle-small-expand')
                }

                ul.append('<li><a href="#' + response.path + '" class="foldable"><i class="factory-icon-blank" rel="fold-handle"></i><i class="factory-icon-folder"></i>' + response.title + '</a><ul style="display: none;"></ul></li>')
              }
            }, 'json');
          })
          ;

          $('.pane-folders')
          .on('click', 'a', function (event) {
            event.preventDefault();

            var elem = $(this);

            if ($(event.target).attr('rel') == 'fold-handle') {
              elem.trigger('fold');
            }
            else {
              elem.trigger('activate');
            }
          })
          .on('dblclick', 'a', function (event) {
            event.preventDefault();

            $(this).trigger('fold');
          })
          .on('fold', 'a', function (event) {
            event.preventDefault();

            var elem = $(this);
            var icon = elem.find('i:first');

            if (!elem.hasClass('foldable')) {
              return false;
            }

            elem.parents('li:first').find('ul:first').toggle();
            icon.toggleClass('factory-icon-toggle-small factory-icon-toggle-small-expand');

            return true;
          })
          .on('activate', 'a', function (event) {
            event.preventDefault();

            var elem = $(this);

            if (elem.hasClass('active')) {
              return true;
            }

            var icon = elem.find('i:eq(1)');
            icon.data('original-icon', icon.attr('class'));
            icon.attr('class', 'factory-icon-loader');

            media.find('.active').removeClass('active');
            $(this).addClass('active');

            $('.details', '.pane-file-details').hide();

            $('.pane-status').trigger('update');

            activeFolder = elem.attr('href').replace('#', '');

            $.get(base_url + 'index.php?option=com_blogfactory&view=media&format=raw&layout=files', { folder: activeFolder }, function (response) {
              icon.attr('class', icon.data('original-icon'));
              $('.pane-files').html(response);
            });

            return true;
          })
          ;

          $('.pane-status')
          .on('click', 'a', function (event) {
            event.preventDefault();

            var elem = $(this);
            var path = elem.attr('href');

            $('.pane-folders a[href="' + path + '"]').trigger('activate');
          })
          .bind('update', function () {
            var elem   = $(this);
            var active = $('.pane-folders a.active');
            var path   = [];

            active.parents('li').each(function (index, element) {
              var a    = $(element).find('a:first');
              var text = a.text().trim();
              var href = a.attr('href');

              path.push('<a href="' + href + '">' + text + '</a>');
            });

            path.reverse();

            elem.html(path.join(' <span class="muted">/</span> '));
          })
          .bind('updateFile', function () {
            var active = $('.pane-files .active .title').text();
            var elem = $(this);
            var file = elem.find('span.file');

            if (!active.length) {
              file.remove();
            }

            if (!file.length) {
              elem.append('<span class="file"> <span class="muted">/</span> <b></b></span>');
              file = elem.find('span.file');
            }

            file.find('b').text(active);
          })
          ;

          $('.pane-upload')
          .on('click', '.button-select-files', function (event) {
            event.preventDefault();

            $('#upload_files').click();
          })
          .on('change', '#upload_files', function (event) {
            var elem = $(this);
            var status = $('.upload-status', '.pane-upload');

            for (var i = 0, count = elem.get(0).files.length; i < count; i++) {
              var file = elem.get(0).files[i];

              status.append('<li>' +
              '<a href="#" class="btn btn-mini btn-danger"><i class="icon-delete"></i></a>' +
              '<div class="info">' +
              '<div>' + file.name +
              '<span class="muted small upload-file-status">Pending</span>' +
              '</div>' +
              '<progress value="0" max="100" style="display: none;"></progress>' +
              '</div>' +
              '</li>');

              status.find('li:last').data('file', file).data('folder', activeFolder).addClass('pending');
            }

            manager.upload();
          })
          .on('click', '.upload-status li a', function (event) {
            event.preventDefault();

            var elem = $(this);
            var li = elem.parents('li:first');

            if (li.hasClass('uploading')) {
              xmlHttpRequest.abort();
            }

            $(this).parent('li:first').remove();
          })
          .on('click', '.button-close', function (event) {
            event.preventDefault();

            media.find('.pane-files, .pane-folders').show();
            media.find('.pane-upload').hide();

            if ('editor' == options.mode) {
              media.find('.pane-file-details').show();
            }

            $('.pane-folders').find('a[href="#' + activeFolder + '"]').removeClass('active').trigger('activate');
          })
          .on('click', '.button-clear-list', function (event) {
            event.preventDefault();

            $('li', '.pane-upload .upload-status').remove();
          })
          ;

          $('.pane-files')
          .on('click', 'div.file', function (event) {
            $(this).trigger('activate');
          })
          .on('activate', 'div.file', function (event) {
            var elem = $(this);
            var title = elem.find('div.title').text();
            var type = elem.attr('data-file-type');
            var extension = title.split('.').pop();
            title = title.replace('.' + extension, '');

            activeFile = elem;

            media.find('.active').removeClass('active');
            elem.addClass('active');

            $('.pane-status').trigger('updateFile');

            if ('manager' == options.mode) {
              return true;
            }

            $('.details', '.pane-file-details').show();
            $('#details_title, #details_alt_text', '.pane-file-details').val(title);

            $('#details_link_type').val('media').change();

            if ('image' == type) {
              $('#details_size_wrapper, #details_alt_text_wrapper', '.pane-file-details').show();
              $('#details_title_wrapper', '.pane-file-details').hide();
            }
            else {
              $('#details_size_wrapper, #details_alt_text_wrapper', '.pane-file-details').hide();
              $('#details_title_wrapper', '.pane-file-details').show();
            }

            return true;
          })
          ;

          $('.pane-file-details')
          .on('click', 'a.button-insert-file', function (event) {
            event.preventDefault();

            var type = activeFile.attr('data-file-type');
            var url = activeFile.attr('data-url');
            var title = $('#details_title').val();
            var alt = $('#details_alt_text').val();
            var size = $('#details_size').val();
            var align = $('#details_align').val();
            var linkType = $('#details_link_type').val();
            var link = $('#details_link').val();
            var content, src, style, href;

            href = 'media' == linkType ? url : link;

            if ('image' == type) {
              src = url;

              if ('original' != size) {
                var extension = src.split('.').pop();
                src = src.replace('.' + extension, '_' + size + '.' + extension);
              }

              if ('right' == align || 'left' == align) {
                style = 'float: ' + align + ';';
              }

              if ('none' == linkType) {
                content = '<img src="' + src + '" alt="' + alt + '" style="' + style + '" />';
              }
              else {
                content = '<a href="' + href + '"><img src="' + src + '" alt="' + alt + '" style="' + style + '" /></a>';
              }
            }
            else {
              if ('none' == linkType) {
                content = title;
              }
              else {
                content = '<a href="' + href + '">' + title + '</a>';
              }
            }

            tinyMCE.activeEditor.execCommand('mceInsertContent', false, content);

            manager.close();
          })
          .on('change', 'select#details_link_type', function (event) {
            var elem = $(this);
            var link = $('#details_link');

            switch (elem.val()) {
              case 'none':
                link.hide();
                break;

              case 'media':
                link.val(activeFile.attr('data-url')).attr('readonly', 'readonly').show();
                break;

              case 'custom':
                link.val('').attr('readonly', false).show();
                break;
            }
          })
          ;

          $('.pane-folders a:first').trigger('activate');

          if ('manager' == options.mode) {
            media.find('.pane-file-details').hide();
            media.find('.pane-files').addClass('extended');
          }

          return true;
        });
      }

      this.open = function (opts) {
        options = $.extend(options, opts);

        if (!initialised) {
          this.init();
        }

        wrapper.show();
      }

      this.close = function () {
        wrapper.hide();
      }

      this.upload = function () {
        if (uploading) {
          return true;
        }

        var item = media.find('.pane-upload .upload-status li.pending:first');

        if (!item.length) {
          return true;
        }

        var progress = item.find('progress');
        var file = item.data('file');
        var folder = item.data('folder');
        var formData = new FormData();

        xmlHttpRequest = new XMLHttpRequest();

        formData.append('file', file);
        formData.append('folder', folder);
        xmlHttpRequest.open('POST', base_url + 'index.php?option=com_blogfactory&task=media.upload&format=raw', true);

        // On progress event.
        xmlHttpRequest.upload.onprogress = function(e) {
          if (e.lengthComputable) {
            var percentComplete = Math.ceil((e.loaded / e.total) * 100);
            progress.val(percentComplete);
          }
        };

        // On start event.
        xmlHttpRequest.upload.onloadstart = function (e) {
          progress.show();

          item.removeClass('pending').addClass('uploading');
          item.find('.upload-file-status').text('Uploading');
        }

        // On success event
        xmlHttpRequest.onreadystatechange = function(e) {
          if (xmlHttpRequest.readyState == 4) {
            uploading = false;

            item.removeClass('uploading').addClass('completed');
            progress.hide();

            if (xmlHttpRequest.status == 200) {
              var response = $.parseJSON(xmlHttpRequest.responseText);
              var button = item.find('a:first');
              var text = response.status ? response.message : response.error;

              item.find('.upload-file-status').text(text);

              if (response.status) {
                button.removeClass('btn-danger').addClass('btn-success')
                .find('i:first').removeClass('icon-delete').addClass('icon-ok');

                // Update free space.
                $('.space-remaining-value').html(response.free_space);
              }
              else {
                button.removeClass('btn-danger').addClass('btn-warning')
                .find('i:first').removeClass('icon-delete').addClass('icon-unpublish');
              }
            }

            manager.upload();
          }
        };

        // Send Ajax form
        xmlHttpRequest.setRequestHeader('X_REQUESTED_WITH', 'XMLHttpRequest');
        xmlHttpRequest.send(formData);

        return true;
      }

      return this;
    }
  });
}(jQuery));
;

// components/com_blogfactory/assets/js/dashboard.js
jQuery(document).ready(function ($) {
  // Initialise variables.
  var MediaManager = $.BlogFactoryMediaManager();

  // Sortable columns.
  $('.blogfactory-dashboard-column').sortable({
    connectWith: '.blogfactory-dashboard-column',
    handle: '.blogfactory-portlet-header',
    placeholder: 'sortable-placeholder',
    tolerance: 'pointer',
    forcePlaceholderSize: true,
    start: function (event, ui) {
      $('.sortable-placeholder').height($(ui.helper).height());
      ui.item.css('opacity', .5);
    },
    stop: function (event, ui) {
      ui.item.css('opacity', 1);

      dashboardSaveState();
    }
  })

  function reconnect(columns) {
    var array = [];
    var i;

    for (i = 1; i <= columns; i++) {
      array.push('.blogfactory-dashboard-column:eq(' + (i - 1) + ')');
    }

    $('.blogfactory-dashboard-column').sortable({ connectWith: array.join(',') });

    console.log(columns);

    switch (columns) {
      case '2':
        $('.blogfactory-dashboard-column:eq(2) .blogfactory-portlet').appendTo($('.blogfactory-dashboard-column:eq(1)'));
        break;

      case '1':
        $('.blogfactory-dashboard-column:eq(2) .blogfactory-portlet, .blogfactory-dashboard-column:eq(1) .blogfactory-portlet').appendTo($('.blogfactory-dashboard-column:eq(0)'));
        break;
    }
  }

  function dashboardSaveState() {
    var array = [];

    $('.blogfactory-portlet').each(function (index, element) {
      var elem   = $(element);
      var column = elem.parents('.blogfactory-dashboard-column:first').index();

      array.push(elem.attr('id').replace('portlet-', '') + '/' + (elem.hasClass('portlet-minimized') ? 1 : 0) + '/' + column);
    });

    console.log(array);

    $.cookie('com_blogfactory_dashboard', array.join(';'), { path: '/' });
  }

  $('.blogfactory-portlet-toggle-handle').click(function (event) {
    event.preventDefault();

    var elem = $(this);
    elem.parents('.blogfactory-portlet:first').toggleClass('portlet-minimized');

    dashboardSaveState();
  });

  $('#options').click(function (event) {
    event.preventDefault();

    var elem = $(this);
    var url  = elem.attr('href');

    $.FactoryModal({
      url: url,
      onSubmit: function (modal) {
        var form = modal.content.find('form');

        $.ajax({
          url: form.attr('action'),
          type: form.attr('method'),
          data: form.serialize(),
          dataType: 'json',
          success: function (response) {
            modal.close();

            if (response.status) {
              $('.blogfactory-dashboard-columns')
                .removeClass('columns-1 columns-2 columns-3')
                .addClass('columns-' + response.data.columns);

              reconnect(response.data.columns);
            }
          }
        });
      }
    });
  })

  // Reset QuickPost form.
  $('.quickpost-action-reset').click(function (event) {
    event.preventDefault();

    var elem = $(this);
    var form = elem.parents('.blogfactory-portlet-content:first').find('form:first');

    form.find('input, textarea').val('');
  });

  // Save QuickPost form.
  $('.quickpost-action-save').click(function (event) {
    event.preventDefault();

    var elem   = $(this);
    var loader = elem.find('i:first');
    var form   = elem.parents('.blogfactory-portlet-content:first').find('form:first');
    var data   = form.serialize();
    var url    = elem.attr('href');
    var update = elem.parents('.blogfactory-portlet-content:first').find('.quickpost-save-result:first');

    loader.show();
    update.html('')

    $.post(url, data, function (response) {
      loader.hide();

      var html = [];
      var result = response.status ? 'success' : 'error';

      html.push('<div class="alert alert-' + result + '">');
      html.push('<button type="button" class="close" data-dismiss="alert">&times;</button>');
      html.push(response.message);

      if (!response.status && response.error) {
        html.push(response.error);
      }

      html.push('</div>');

      update.html(html.join("\n"));

      if (response.status) {
        $('.quickpost-action-reset').click();

        // Add post to the Recent Posts portlet.
        $('.blogfactory-portlet-content', '#portlet-posts')
          .prepend(response.portlet_posts_update)
          .find('.portlet-no-results').remove().end()
          .find('.portlet-footer').show().end();
      }
    }, 'json');
  });

  // Media manager.
  $('.button-media-manager').click(function (event) {
    event.preventDefault();

    MediaManager.open({ mode: 'manager' });
  });
});
