import $ from 'jquery';
import func from '../core/func';
import lists from '../core/lists';
import env from '../core/env';

export default class Buttons {
  constructor(context) {
    this.ui = $.summernote.ui;
    this.context = context;
    this.$toolbar = context.layoutInfo.toolbar;
    this.options = context.options;
    this.lang = this.options.langInfo;
    this.invertedKeyMap = func.invertObject(
      this.options.keyMap[env.isMac ? 'mac' : 'pc']
    );
  }

  representShortcut(editorMethod) {
    let shortcut = this.invertedKeyMap[editorMethod];
    if (!this.options.shortcuts || !shortcut) {
      return '';
    }

    if (env.isMac) {
      shortcut = shortcut.replace('CMD', '⌘').replace('SHIFT', '⇧');
    }

    shortcut = shortcut.replace('BACKSLASH', '\\')
      .replace('SLASH', '/')
      .replace('LEFTBRACKET', '[')
      .replace('RIGHTBRACKET', ']');

    return ' (' + shortcut + ')';
  }

  button(o) {
    if (!this.options.tooltip && o.tooltip) {
      delete o.tooltip;
    }
    o.container = this.options.container;
    return this.ui.button(o);
  }

  initialize() {
    this.addToolbarButtons();
    this.addImagePopoverButtons();
    this.addLinkPopoverButtons();
    this.addTablePopoverButtons();
    this.fontInstalledMap = {};
  }

  destroy() {
    delete this.fontInstalledMap;
  }

  isFontInstalled(name) {
    if (!this.fontInstalledMap.hasOwnProperty(name)) {
      this.fontInstalledMap[name] = env.isFontInstalled(name) ||
        lists.contains(this.options.fontNamesIgnoreCheck, name);
    }

    return this.fontInstalledMap[name];
  }

  isFontDeservedToAdd(name) {
    const genericFamilies = ['sans-serif', 'serif', 'monospace', 'cursive', 'fantasy'];
    name = name.toLowerCase();

    return ((name !== '') && this.isFontInstalled(name) && ($.inArray(name, genericFamilies) === -1));
  }

  colorPalette(className, tooltip, backColor, foreColor) {
    return this.ui.buttonGroup({
      className: 'note-color ' + className,
      children: [
        this.button({
          className: 'note-current-color-button',
          contents: this.ui.icon(this.options.icons.font + ' note-recent-color'),
          tooltip: tooltip,
          click: (e) => {
            const $button = $(e.currentTarget);
            if (backColor && foreColor) {
              this.context.invoke('editor.color', {
                backColor: $button.attr('data-backColor'),
                foreColor: $button.attr('data-foreColor')
              });
            } else if (backColor) {
              this.context.invoke('editor.color', {
                backColor: $button.attr('data-backColor')
              });
            } else if (foreColor) {
              this.context.invoke('editor.color', {
                foreColor: $button.attr('data-foreColor')
              });
            }
          },
          callback: ($button) => {
            const $recentColor = $button.find('.note-recent-color');
            if (backColor) {
              $recentColor.css('background-color', '#FFFF00');
              $button.attr('data-backColor', '#FFFF00');
            }
            if (!foreColor) {
              $recentColor.css('color', 'transparent');
            }
          }
        }),
        this.button({
          className: 'dropdown-toggle',
          contents: this.ui.dropdownButtonContents('', this.options),
          tooltip: this.lang.color.more,
          data: {
            toggle: 'dropdown'
          }
        }),
        this.ui.dropdown({
          items: (backColor ? [
            '<div class="note-palette">',
            '  <div class="note-palette-title">' + this.lang.color.background + '</div>',
            '  <div>',
            '    <button type="button" class="note-color-reset btn btn-light" data-event="backColor" data-value="inherit">',
            this.lang.color.transparent,
            '    </button>',
            '  </div>',
            '  <div class="note-holder" data-event="backColor"/>',
            '  <div>',
            '    <button type="button" class="note-color-select btn" data-event="openPalette" data-value="backColorPicker">',
            this.lang.color.cpSelect,
            '    </button>',
            '    <input type="color" id="backColorPicker" class="note-btn note-color-select-btn" value="#FFFF00" data-event="backColorPalette">',
            '  </div>',
            '  <div class="note-holder-custom" id="backColorPalette" data-event="backColor"/>',
            '</div>'
          ].join('') : '') +
          (foreColor ? [
            '<div class="note-palette">',
            '  <div class="note-palette-title">' + this.lang.color.foreground + '</div>',
            '  <div>',
            '    <button type="button" class="note-color-reset btn btn-light" data-event="removeFormat" data-value="foreColor">',
            this.lang.color.resetToDefault,
            '    </button>',
            '  </div>',
            '  <div class="note-holder" data-event="foreColor"/>',
            '  <div>',
            '    <button type="button" class="note-color-select btn" data-event="openPalette" data-value="foreColorPicker">',
            this.lang.color.cpSelect,
            '    </button>',
            '    <input type="color" id="foreColorPicker" class="note-btn note-color-select-btn" value="#000000" data-event="foreColorPalette">',
            '  <div class="note-holder-custom" id="foreColorPalette" data-event="foreColor"/>',
            '</div>'
          ].join('') : ''),
          callback: ($dropdown) => {
            $dropdown.find('.note-holder').each((idx, item) => {
              const $holder = $(item);
              $holder.append(this.ui.palette({
                colors: this.options.colors,
                colorsName: this.options.colorsName,
                eventName: $holder.data('event'),
                container: this.options.container,
                tooltip: this.options.tooltip
              }).render());
            });
            /* TODO: do we have to record recent custom colors within cookies? */
            var customColors = [
              ['#FFFFFF', '#FFFFFF', '#FFFFFF', '#FFFFFF', '#FFFFFF', '#FFFFFF', '#FFFFFF', '#FFFFFF']
            ];
            $dropdown.find('.note-holder-custom').each((idx, item) => {
              const $holder = $(item);
              $holder.append(this.ui.palette({
                colors: customColors,
                colorsName: customColors,
                eventName: $holder.data('event'),
                container: this.options.container,
                tooltip: this.options.tooltip
              }).render());
            });
            $dropdown.find('input[type=color]').each((idx, item) => {
              $(item).change(function() {
                const $chip = $dropdown.find('#' + $(this).data('event')).find('.note-color-btn').first();
                const color = this.value.toUpperCase();
                $chip.css('background-color', color)
                  .attr('aria-label', color)
                  .attr('data-value', color)
                  .attr('data-original-title', color);
                $chip.click();
              });
            });
          },
          click: (event) => {
            event.stopPropagation();

            const $parent = $('.' + className);
            const $button = $(event.target);
            const eventName = $button.data('event');
            let value = $button.attr('data-value');

            if (eventName === 'openPalette') {
              const $picker = $parent.find('#' + value);
              const $palette = $($parent.find('#' + $picker.data('event')).find('.note-color-row')[0]);

              // Shift palette chips
              const $chip = $palette.find('.note-color-btn').last().detach();

              // Set chip attributes
              const color = $picker.val();
              $chip.css('background-color', color)
                .attr('aria-label', color)
                .attr('data-value', color)
                .attr('data-original-title', color);
              $palette.prepend($chip);
              $picker.click();
            } else if (lists.contains(['backColor', 'foreColor'], eventName)) {
              const key = eventName === 'backColor' ? 'background-color' : 'color';
              const $color = $button.closest('.note-color').find('.note-recent-color');
              const $currentButton = $button.closest('.note-color').find('.note-current-color-button');

              $color.css(key, value);
              $currentButton.attr('data-' + eventName, value);
              this.context.invoke('editor.' + eventName, value);
            }
          }
        })
      ]
    }).render();
  }

  addToolbarButtons() {
    this.context.memo('button.style', () => {
      return this.ui.buttonGroup([
        this.button({
          className: 'dropdown-toggle',
          contents: this.ui.dropdownButtonContents(
            this.ui.icon(this.options.icons.magic), this.options
          ),
          tooltip: this.lang.style.style,
          data: {
            toggle: 'dropdown'
          }
        }),
        this.ui.dropdown({
          className: 'dropdown-style',
          items: this.options.styleTags,
          title: this.lang.style.style,
          template: (item) => {
            if (typeof item === 'string') {
              item = { tag: item, title: (this.lang.style.hasOwnProperty(item) ? this.lang.style[item] : item) };
            }

            const tag = item.tag;
            const title = item.title;
            const style = item.style ? ' style="' + item.style + '" ' : '';
            const className = item.className ? ' class="' + item.className + '"' : '';

            return '<' + tag + style + className + '>' + title + '</' + tag + '>';
          },
          click: this.context.createInvokeHandler('editor.formatBlock')
        })
      ]).render();
    });

    for (let styleIdx = 0, styleLen = this.options.styleTags.length; styleIdx < styleLen; styleIdx++) {
      const item = this.options.styleTags[styleIdx];

      this.context.memo('button.style.' + item, () => {
        return this.button({
          className: 'note-btn-style-' + item,
          contents: '<div data-value="' + item + '">' + item.toUpperCase() + '</div>',
          tooltip: this.lang.style[item],
          click: this.context.createInvokeHandler('editor.formatBlock')
        }).render();
      });
    }

    this.context.memo('button.bold', () => {
      return this.button({
        className: 'note-btn-bold',
        contents: this.ui.icon(this.options.icons.bold),
        tooltip: this.lang.font.bold + this.representShortcut('bold'),
        click: this.context.createInvokeHandlerAndUpdateState('editor.bold')
      }).render();
    });

    this.context.memo('button.italic', () => {
      return this.button({
        className: 'note-btn-italic',
        contents: this.ui.icon(this.options.icons.italic),
        tooltip: this.lang.font.italic + this.representShortcut('italic'),
        click: this.context.createInvokeHandlerAndUpdateState('editor.italic')
      }).render();
    });

    this.context.memo('button.underline', () => {
      return this.button({
        className: 'note-btn-underline',
        contents: this.ui.icon(this.options.icons.underline),
        tooltip: this.lang.font.underline + this.representShortcut('underline'),
        click: this.context.createInvokeHandlerAndUpdateState('editor.underline')
      }).render();
    });

    this.context.memo('button.clear', () => {
      return this.button({
        contents: this.ui.icon(this.options.icons.eraser),
        tooltip: this.lang.font.clear + this.representShortcut('removeFormat'),
        click: this.context.createInvokeHandler('editor.removeFormat')
      }).render();
    });

    this.context.memo('button.strikethrough', () => {
      return this.button({
        className: 'note-btn-strikethrough',
        contents: this.ui.icon(this.options.icons.strikethrough),
        tooltip: this.lang.font.strikethrough + this.representShortcut('strikethrough'),
        click: this.context.createInvokeHandlerAndUpdateState('editor.strikethrough')
      }).render();
    });

    this.context.memo('button.superscript', () => {
      return this.button({
        className: 'note-btn-superscript',
        contents: this.ui.icon(this.options.icons.superscript),
        tooltip: this.lang.font.superscript,
        click: this.context.createInvokeHandlerAndUpdateState('editor.superscript')
      }).render();
    });

    this.context.memo('button.subscript', () => {
      return this.button({
        className: 'note-btn-subscript',
        contents: this.ui.icon(this.options.icons.subscript),
        tooltip: this.lang.font.subscript,
        click: this.context.createInvokeHandlerAndUpdateState('editor.subscript')
      }).render();
    });

    this.context.memo('button.fontname', () => {
      const styleInfo = this.context.invoke('editor.currentStyle');

      // Add 'default' fonts into the fontnames array if not exist
      $.each(styleInfo['font-family'].split(','), (idx, fontname) => {
        fontname = fontname.trim().replace(/['"]+/g, '');
        if (this.isFontDeservedToAdd(fontname)) {
          if ($.inArray(fontname, this.options.fontNames) === -1) {
            this.options.fontNames.push(fontname);
          }
        }
      });

      return this.ui.buttonGroup([
        this.button({
          className: 'dropdown-toggle',
          contents: this.ui.dropdownButtonContents(
            '<span class="note-current-fontname"/>', this.options
          ),
          tooltip: this.lang.font.name,
          data: {
            toggle: 'dropdown'
          }
        }),
        this.ui.dropdownCheck({
          className: 'dropdown-fontname',
          checkClassName: this.options.icons.menuCheck,
          items: this.options.fontNames.filter(this.isFontInstalled.bind(this)),
          title: this.lang.font.name,
          template: (item) => {
            return '<span style="font-family: \'' + item + '\'">' + item + '</span>';
          },
          click: this.context.createInvokeHandlerAndUpdateState('editor.fontName')
        })
      ]).render();
    });

    this.context.memo('button.fontsize', () => {
      return this.ui.buttonGroup([
        this.button({
          className: 'dropdown-toggle',
          contents: this.ui.dropdownButtonContents('<span class="note-current-fontsize"/>', this.options),
          tooltip: this.lang.font.size,
          data: {
            toggle: 'dropdown'
          }
        }),
        this.ui.dropdownCheck({
          className: 'dropdown-fontsize',
          checkClassName: this.options.icons.menuCheck,
          items: this.options.fontSizes,
          title: this.lang.font.size,
          click: this.context.createInvokeHandlerAndUpdateState('editor.fontSize')
        })
      ]).render();
    });

    this.context.memo('button.color', () => {
      return this.colorPalette('note-color-all', this.lang.color.recent, true, true);
    });

    this.context.memo('button.forecolor', () => {
      return this.colorPalette('note-color-fore', this.lang.color.foreground, false, true);
    });

    this.context.memo('button.backcolor', () => {
      return this.colorPalette('note-color-back', this.lang.color.background, true, false);
    });

    this.context.memo('button.ul', () => {
      return this.button({
        contents: this.ui.icon(this.options.icons.unorderedlist),
        tooltip: this.lang.lists.unordered + this.representShortcut('insertUnorderedList'),
        click: this.context.createInvokeHandler('editor.insertUnorderedList')
      }).render();
    });

    this.context.memo('button.ol', () => {
      return this.button({
        contents: this.ui.icon(this.options.icons.orderedlist),
        tooltip: this.lang.lists.ordered + this.representShortcut('insertOrderedList'),
        click: this.context.createInvokeHandler('editor.insertOrderedList')
      }).render();
    });

    const justifyLeft = this.button({
      contents: this.ui.icon(this.options.icons.alignLeft),
      tooltip: this.lang.paragraph.left + this.representShortcut('justifyLeft'),
      click: this.context.createInvokeHandler('editor.justifyLeft')
    });

    const justifyCenter = this.button({
      contents: this.ui.icon(this.options.icons.alignCenter),
      tooltip: this.lang.paragraph.center + this.representShortcut('justifyCenter'),
      click: this.context.createInvokeHandler('editor.justifyCenter')
    });

    const justifyRight = this.button({
      contents: this.ui.icon(this.options.icons.alignRight),
      tooltip: this.lang.paragraph.right + this.representShortcut('justifyRight'),
      click: this.context.createInvokeHandler('editor.justifyRight')
    });

    const justifyFull = this.button({
      contents: this.ui.icon(this.options.icons.alignJustify),
      tooltip: this.lang.paragraph.justify + this.representShortcut('justifyFull'),
      click: this.context.createInvokeHandler('editor.justifyFull')
    });

    const outdent = this.button({
      contents: this.ui.icon(this.options.icons.outdent),
      tooltip: this.lang.paragraph.outdent + this.representShortcut('outdent'),
      click: this.context.createInvokeHandler('editor.outdent')
    });

    const indent = this.button({
      contents: this.ui.icon(this.options.icons.indent),
      tooltip: this.lang.paragraph.indent + this.representShortcut('indent'),
      click: this.context.createInvokeHandler('editor.indent')
    });

    this.context.memo('button.justifyLeft', func.invoke(justifyLeft, 'render'));
    this.context.memo('button.justifyCenter', func.invoke(justifyCenter, 'render'));
    this.context.memo('button.justifyRight', func.invoke(justifyRight, 'render'));
    this.context.memo('button.justifyFull', func.invoke(justifyFull, 'render'));
    this.context.memo('button.outdent', func.invoke(outdent, 'render'));
    this.context.memo('button.indent', func.invoke(indent, 'render'));

    this.context.memo('button.paragraph', () => {
      return this.ui.buttonGroup([
        this.button({
          className: 'dropdown-toggle',
          contents: this.ui.dropdownButtonContents(this.ui.icon(this.options.icons.alignLeft), this.options),
          tooltip: this.lang.paragraph.paragraph,
          data: {
            toggle: 'dropdown'
          }
        }),
        this.ui.dropdown([
          this.ui.buttonGroup({
            className: 'note-align',
            children: [justifyLeft, justifyCenter, justifyRight, justifyFull]
          }),
          this.ui.buttonGroup({
            className: 'note-list',
            children: [outdent, indent]
          })
        ])
      ]).render();
    });

    this.context.memo('button.height', () => {
      return this.ui.buttonGroup([
        this.button({
          className: 'dropdown-toggle',
          contents: this.ui.dropdownButtonContents(this.ui.icon(this.options.icons.textHeight), this.options),
          tooltip: this.lang.font.height,
          data: {
            toggle: 'dropdown'
          }
        }),
        this.ui.dropdownCheck({
          items: this.options.lineHeights,
          checkClassName: this.options.icons.menuCheck,
          className: 'dropdown-line-height',
          title: this.lang.font.height,
          click: this.context.createInvokeHandler('editor.lineHeight')
        })
      ]).render();
    });

    this.context.memo('button.table', () => {
      return this.ui.buttonGroup([
        this.button({
          className: 'dropdown-toggle',
          contents: this.ui.dropdownButtonContents(this.ui.icon(this.options.icons.table), this.options),
          tooltip: this.lang.table.table,
          data: {
            toggle: 'dropdown'
          }
        }),
        this.ui.dropdown({
          title: this.lang.table.table,
          className: 'note-table',
          items: [
            '<div class="note-dimension-picker">',
            '  <div class="note-dimension-picker-mousecatcher" data-event="insertTable" data-value="1x1"/>',
            '  <div class="note-dimension-picker-highlighted"/>',
            '  <div class="note-dimension-picker-unhighlighted"/>',
            '</div>',
            '<div class="note-dimension-display">1 x 1</div>'
          ].join('')
        })
      ], {
        callback: ($node) => {
          const $catcher = $node.find('.note-dimension-picker-mousecatcher');
          $catcher.css({
            width: this.options.insertTableMaxSize.col + 'em',
            height: this.options.insertTableMaxSize.row + 'em'
          }).mousedown(this.context.createInvokeHandler('editor.insertTable'))
            .on('mousemove', this.tableMoveHandler.bind(this));
        }
      }).render();
    });

    this.context.memo('button.link', () => {
      return this.button({
        contents: this.ui.icon(this.options.icons.link),
        tooltip: this.lang.link.link + this.representShortcut('linkDialog.show'),
        click: this.context.createInvokeHandler('linkDialog.show')
      }).render();
    });

    this.context.memo('button.picture', () => {
      return this.button({
        contents: this.ui.icon(this.options.icons.picture),
        tooltip: this.lang.image.image,
        click: this.context.createInvokeHandler('imageDialog.show')
      }).render();
    });

    this.context.memo('button.video', () => {
      return this.button({
        contents: this.ui.icon(this.options.icons.video),
        tooltip: this.lang.video.video,
        click: this.context.createInvokeHandler('videoDialog.show')
      }).render();
    });

    this.context.memo('button.hr', () => {
      return this.button({
        contents: this.ui.icon(this.options.icons.minus),
        tooltip: this.lang.hr.insert + this.representShortcut('insertHorizontalRule'),
        click: this.context.createInvokeHandler('editor.insertHorizontalRule')
      }).render();
    });

    this.context.memo('button.fullscreen', () => {
      return this.button({
        className: 'btn-fullscreen',
        contents: this.ui.icon(this.options.icons.arrowsAlt),
        tooltip: this.lang.options.fullscreen,
        click: this.context.createInvokeHandler('fullscreen.toggle')
      }).render();
    });

    this.context.memo('button.codeview', () => {
      return this.button({
        className: 'btn-codeview',
        contents: this.ui.icon(this.options.icons.code),
        tooltip: this.lang.options.codeview,
        click: this.context.createInvokeHandler('codeview.toggle')
      }).render();
    });

    this.context.memo('button.redo', () => {
      return this.button({
        contents: this.ui.icon(this.options.icons.redo),
        tooltip: this.lang.history.redo + this.representShortcut('redo'),
        click: this.context.createInvokeHandler('editor.redo')
      }).render();
    });

    this.context.memo('button.undo', () => {
      return this.button({
        contents: this.ui.icon(this.options.icons.undo),
        tooltip: this.lang.history.undo + this.representShortcut('undo'),
        click: this.context.createInvokeHandler('editor.undo')
      }).render();
    });

    this.context.memo('button.help', () => {
      return this.button({
        contents: this.ui.icon(this.options.icons.question),
        tooltip: this.lang.options.help,
        click: this.context.createInvokeHandler('helpDialog.show')
      }).render();
    });
  }

  /**
   * image : [
   *   ['imagesize', ['imageSize100', 'imageSize50', 'imageSize25']],
   *   ['float', ['floatLeft', 'floatRight', 'floatNone' ]],
   *   ['remove', ['removeMedia']]
   * ],
   */
  addImagePopoverButtons() {
    // Image Size Buttons
    this.context.memo('button.imageSize100', () => {
      return this.button({
        contents: '<span class="note-fontsize-10">100%</span>',
        tooltip: this.lang.image.resizeFull,
        click: this.context.createInvokeHandler('editor.resize', '1')
      }).render();
    });
    this.context.memo('button.imageSize50', () => {
      return this.button({
        contents: '<span class="note-fontsize-10">50%</span>',
        tooltip: this.lang.image.resizeHalf,
        click: this.context.createInvokeHandler('editor.resize', '0.5')
      }).render();
    });
    this.context.memo('button.imageSize25', () => {
      return this.button({
        contents: '<span class="note-fontsize-10">25%</span>',
        tooltip: this.lang.image.resizeQuarter,
        click: this.context.createInvokeHandler('editor.resize', '0.25')
      }).render();
    });

    // Float Buttons
    this.context.memo('button.floatLeft', () => {
      return this.button({
        contents: this.ui.icon(this.options.icons.alignLeft),
        tooltip: this.lang.image.floatLeft,
        click: this.context.createInvokeHandler('editor.floatMe', 'left')
      }).render();
    });

    this.context.memo('button.floatRight', () => {
      return this.button({
        contents: this.ui.icon(this.options.icons.alignRight),
        tooltip: this.lang.image.floatRight,
        click: this.context.createInvokeHandler('editor.floatMe', 'right')
      }).render();
    });

    this.context.memo('button.floatNone', () => {
      return this.button({
        contents: this.ui.icon(this.options.icons.alignJustify),
        tooltip: this.lang.image.floatNone,
        click: this.context.createInvokeHandler('editor.floatMe', 'none')
      }).render();
    });

    // Remove Buttons
    this.context.memo('button.removeMedia', () => {
      return this.button({
        contents: this.ui.icon(this.options.icons.trash),
        tooltip: this.lang.image.remove,
        click: this.context.createInvokeHandler('editor.removeMedia')
      }).render();
    });
  }

  addLinkPopoverButtons() {
    this.context.memo('button.linkDialogShow', () => {
      return this.button({
        contents: this.ui.icon(this.options.icons.link),
        tooltip: this.lang.link.edit,
        click: this.context.createInvokeHandler('linkDialog.show')
      }).render();
    });

    this.context.memo('button.unlink', () => {
      return this.button({
        contents: this.ui.icon(this.options.icons.unlink),
        tooltip: this.lang.link.unlink,
        click: this.context.createInvokeHandler('editor.unlink')
      }).render();
    });
  }

  /**
   * table : [
   *  ['add', ['addRowDown', 'addRowUp', 'addColLeft', 'addColRight']],
   *  ['delete', ['deleteRow', 'deleteCol', 'deleteTable']]
   * ],
   */
  addTablePopoverButtons() {
    this.context.memo('button.addRowUp', () => {
      return this.button({
        className: 'btn-md',
        contents: this.ui.icon(this.options.icons.rowAbove),
        tooltip: this.lang.table.addRowAbove,
        click: this.context.createInvokeHandler('editor.addRow', 'top')
      }).render();
    });
    this.context.memo('button.addRowDown', () => {
      return this.button({
        className: 'btn-md',
        contents: this.ui.icon(this.options.icons.rowBelow),
        tooltip: this.lang.table.addRowBelow,
        click: this.context.createInvokeHandler('editor.addRow', 'bottom')
      }).render();
    });
    this.context.memo('button.addColLeft', () => {
      return this.button({
        className: 'btn-md',
        contents: this.ui.icon(this.options.icons.colBefore),
        tooltip: this.lang.table.addColLeft,
        click: this.context.createInvokeHandler('editor.addCol', 'left')
      }).render();
    });
    this.context.memo('button.addColRight', () => {
      return this.button({
        className: 'btn-md',
        contents: this.ui.icon(this.options.icons.colAfter),
        tooltip: this.lang.table.addColRight,
        click: this.context.createInvokeHandler('editor.addCol', 'right')
      }).render();
    });
    this.context.memo('button.deleteRow', () => {
      return this.button({
        className: 'btn-md',
        contents: this.ui.icon(this.options.icons.rowRemove),
        tooltip: this.lang.table.delRow,
        click: this.context.createInvokeHandler('editor.deleteRow')
      }).render();
    });
    this.context.memo('button.deleteCol', () => {
      return this.button({
        className: 'btn-md',
        contents: this.ui.icon(this.options.icons.colRemove),
        tooltip: this.lang.table.delCol,
        click: this.context.createInvokeHandler('editor.deleteCol')
      }).render();
    });
    this.context.memo('button.deleteTable', () => {
      return this.button({
        className: 'btn-md',
        contents: this.ui.icon(this.options.icons.trash),
        tooltip: this.lang.table.delTable,
        click: this.context.createInvokeHandler('editor.deleteTable')
      }).render();
    });
  }

  build($container, groups) {
    for (let groupIdx = 0, groupLen = groups.length; groupIdx < groupLen; groupIdx++) {
      const group = groups[groupIdx];
      const groupName = $.isArray(group) ? group[0] : group;
      const buttons = $.isArray(group) ? ((group.length === 1) ? [group[0]] : group[1]) : [group];

      const $group = this.ui.buttonGroup({
        className: 'note-' + groupName
      }).render();

      for (let idx = 0, len = buttons.length; idx < len; idx++) {
        const btn = this.context.memo('button.' + buttons[idx]);
        if (btn) {
          $group.append(typeof btn === 'function' ? btn(this.context) : btn);
        }
      }
      $group.appendTo($container);
    }
  }

  /**
   * @param {jQuery} [$container]
   */
  updateCurrentStyle($container) {
    const $cont = $container || this.$toolbar;

    const styleInfo = this.context.invoke('editor.currentStyle');
    this.updateBtnStates($cont, {
      '.note-btn-bold': () => {
        return styleInfo['font-bold'] === 'bold';
      },
      '.note-btn-italic': () => {
        return styleInfo['font-italic'] === 'italic';
      },
      '.note-btn-underline': () => {
        return styleInfo['font-underline'] === 'underline';
      },
      '.note-btn-subscript': () => {
        return styleInfo['font-subscript'] === 'subscript';
      },
      '.note-btn-superscript': () => {
        return styleInfo['font-superscript'] === 'superscript';
      },
      '.note-btn-strikethrough': () => {
        return styleInfo['font-strikethrough'] === 'strikethrough';
      }
    });

    if (styleInfo['font-family']) {
      const fontNames = styleInfo['font-family'].split(',').map((name) => {
        return name.replace(/[\'\"]/g, '')
          .replace(/\s+$/, '')
          .replace(/^\s+/, '');
      });
      const fontName = lists.find(fontNames, this.isFontInstalled.bind(this));

      $cont.find('.dropdown-fontname a').each((idx, item) => {
        const $item = $(item);
        // always compare string to avoid creating another func.
        const isChecked = ($item.data('value') + '') === (fontName + '');
        $item.toggleClass('checked', isChecked);
      });
      $cont.find('.note-current-fontname').text(fontName).css('font-family', fontName);
    }

    if (styleInfo['font-size']) {
      const fontSize = styleInfo['font-size'];
      $cont.find('.dropdown-fontsize a').each((idx, item) => {
        const $item = $(item);
        // always compare with string to avoid creating another func.
        const isChecked = ($item.data('value') + '') === (fontSize + '');
        $item.toggleClass('checked', isChecked);
      });
      $cont.find('.note-current-fontsize').text(fontSize);
    }

    if (styleInfo['line-height']) {
      const lineHeight = styleInfo['line-height'];
      $cont.find('.dropdown-line-height li a').each((idx, item) => {
        // always compare with string to avoid creating another func.
        const isChecked = ($(item).data('value') + '') === (lineHeight + '');
        this.className = isChecked ? 'checked' : '';
      });
    }
  }

  updateBtnStates($container, infos) {
    $.each(infos, (selector, pred) => {
      this.ui.toggleBtnActive($container.find(selector), pred());
    });
  }

  tableMoveHandler(event) {
    const PX_PER_EM = 18;
    const $picker = $(event.target.parentNode); // target is mousecatcher
    const $dimensionDisplay = $picker.next();
    const $catcher = $picker.find('.note-dimension-picker-mousecatcher');
    const $highlighted = $picker.find('.note-dimension-picker-highlighted');
    const $unhighlighted = $picker.find('.note-dimension-picker-unhighlighted');

    let posOffset;
    // HTML5 with jQuery - e.offsetX is undefined in Firefox
    if (event.offsetX === undefined) {
      const posCatcher = $(event.target).offset();
      posOffset = {
        x: event.pageX - posCatcher.left,
        y: event.pageY - posCatcher.top
      };
    } else {
      posOffset = {
        x: event.offsetX,
        y: event.offsetY
      };
    }

    const dim = {
      c: Math.ceil(posOffset.x / PX_PER_EM) || 1,
      r: Math.ceil(posOffset.y / PX_PER_EM) || 1
    };

    $highlighted.css({ width: dim.c + 'em', height: dim.r + 'em' });
    $catcher.data('value', dim.c + 'x' + dim.r);

    if (dim.c > 3 && dim.c < this.options.insertTableMaxSize.col) {
      $unhighlighted.css({ width: dim.c + 1 + 'em' });
    }

    if (dim.r > 3 && dim.r < this.options.insertTableMaxSize.row) {
      $unhighlighted.css({ height: dim.r + 1 + 'em' });
    }

    $dimensionDisplay.html(dim.c + ' x ' + dim.r);
  }
}
