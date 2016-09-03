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

// components/com_blogfactory/assets/js/postedit.js
jQuery(document).ready(function ($) {
  // Initialise variables.
  var MediaManager = $.BlogFactoryMediaManager();

  // Submit form buttons.
  $('.button-draft, .button-save', '.blogfactory-post-submit').click(function (event) {
    event.preventDefault();

    var elem = $(this);
    var mode = elem.attr('rel');

    $('#mode').val(mode);
    $('form#post-edit').submit();
  });

  // Add media button.
  $('.button-add-media').click(function (event) {
    event.preventDefault();

    MediaManager.open();
  });

  var form = $('form#post-edit');
  if ('0' != form.attr('data-id')) {
    setInterval(function () {
      if ('true' == form.attr('data-revision')) {
        return true;
      }

      tinyMCE.triggerSave();

      var id = form.attr('data-id');
      var data = { id: id, content: $('#jform_content').val(), title: $('#jform_title').val() };

      $('.status', 'div.autosave').hide();
      $('.loader', 'div.autosave').show();

      $.post(base_url + 'index.php?option=com_blogfactory&task=post.autosave&format=raw', { jform: data } , function (response) {
        $('.status', 'div.autosave').show();
        $('.loader', 'div.autosave').hide();

        if (response.status) {
          $('.status', 'div.autosave').text(response.date);
        }

        $('.newer-autosave-warning').remove();
      }, 'json');

      return true;
    }, 60 * 1000);
  }

  // Preview button.
  $('a.button-preview').click(function (event) {
    event.preventDefault();

    tinyMCE.triggerSave();

    var id = form.attr('data-id');
    var data = { id: id, content: $('#jform_content').val(), title: $('#jform_title').val() };

    $.post(base_url + 'index.php?option=com_blogfactory&task=post.preview&format=raw', { jform: data } , function (response) {
      if (response.status) {
        var preview = window.open(base_url + 'index.php?option=com_blogfactory&view=post&preview=' + response.preview, 'com_blogfactory_preview');
        preview.focus();
      }
    }, 'json');
  });
});
;

// components/com_blogfactory/assets/js/fields/alias.js
jQuery(document).ready(function ($) {
  $('.blogfactory-field-alias').each(function (index, element) {
    var elem    = $(element);
    var observe = $('#' + elem.attr('data-observe'));
    var url     = elem.attr('data-url');
    var view    = elem.find('.view');
    var edit    = elem.find('.edit');
    var input   = elem.find('input[type="hidden"]:first');
    var request;

    // Edit button.
    elem.find('.button-edit').click(function (event) {
      event.preventDefault();

      var val = view.find('.current').text();

      view.hide();
      edit.css('display', 'inline-block').find('input:first').val(val).end()
    });

    // Cancel button.
    elem.find('.button-cancel').click(function (event) {
      event.preventDefault();

      view.show();
      edit.hide();
    });

    // Save button.
    elem.find('.button-save').click(function (event) {
      event.preventDefault();

      updateValue(elem.find('.edit input:first').val());

      observe.unbind('change');
      view.show();
      edit.hide();
    });

    if ('' == observe.val()) {
      observe.change(function () {
        updateValue($(this).val());
      });
    }

    function updateValue(query) {
      if (request) {
        request.abort();
      }

      request = $.get(url, { query: query }, function (response) {
        if (response.status) {
          view.find('.current').text(response.alias);
          input.val(response.alias);
        }
      }, 'json');
    }

    return true;
  });
});
;

// components/com_blogfactory/assets/js/fields/posteditor.js
jQuery(document).ready(function ($) {
  tinymce.init({
    selector: 'textarea#jform_content',
    menubar: false,
    height: 300,
    content_css: blogfactory_editor_css,
    plugins: 'link spellchecker fullscreen textcolor charmap code readmore blogfactoryimage wordcount advlist',
    relative_urls: false,
    remove_script_host: false,
    image_advtab: true,
    preview_styles: false,
    toolbar1: 'bold italic strikethrough | numlist bullist blockquote | alignleft aligncenter alignright | link unlink readmore | fullscreen',
    toolbar2: 'styleselect | underline alignjustify forecolor | paste | removeformat charmap | outdent indent | undo redo | code | blogfactoryimage'
  });
});
;

// components/com_blogfactory/assets/js/fields/calendar.js
jQuery(document).ready(function ($) {
  // Edit button.
  $('.blogfactory-calendar-edit').click(function (event) {
    event.preventDefault();

    var elem = $(this);
    var calendar = elem.parents('.blogfactory-calendar:first');

    elem.hide();
    calendar.find('.edit').show('fast');
  });

  // Cancel button.
  $('.blogfactory-calendar-cancel').click(function (event) {
    event.preventDefault();

    var elem = $(this);
    var calendar = elem.parents('.blogfactory-calendar:first');

    calendar.find('.edit').hide('fast');
    calendar.find('.text .blogfactory-calendar-edit').show();
  });

  // Immediately or Never buttons.
  $('.blogfactory-calendar-extra').click(function (event) {
    event.preventDefault();

    var elem = $(this);
    var calendar = elem.parents('.blogfactory-calendar:first');

    calendar.find('.edit').hide('fast');
    calendar.find('.text .blogfactory-calendar-edit').show();
    calendar.find('.text span').html(elem.html());
    calendar.find('input[type="hidden"]').val('');
  });

  // Save button.
  $('.blogfactory-calendar-save').click(function (event) {
    event.preventDefault();

    var elem     = $(this);
    var calendar = elem.parents('.blogfactory-calendar:first');
    var data     = {};
    var input    = calendar.find('input[type="hidden"]');
    var label    = calendar.find('.text span');

    calendar.find('.edit').hide('fast');
    calendar.find('.text .blogfactory-calendar-edit').show();
    label.attr('data-original', label.html());
    label.html('<i class="factory-icon-loader"></i>');

    calendar.find('[name^="date"]').each(function (index, element) {
      var elem = $(element);
      data[elem.attr('name')] = elem.val();
    });

    $.get(base_url + 'index.php?option=com_blogfactory&task=helper.parsedate&format=raw', data, function (response) {
      if (response.status) {
        label.html(response.label);
        input.val(response.date);
      }
      else {
        label.html(label.attr('data-original'));
      }
    }, 'json');
  });
});
