import $ from 'jquery';
import func from '../core/func';
import lists from '../core/lists';
import dom from '../core/dom';
import range from '../core/range';
import key from '../core/key';

const POPOVER_DIST = 5;

export default class HintPopover {
  constructor(context) {
    this.context = context;

    this.ui = $.summernote.ui;
    this.$editable = context.layoutInfo.editable;
    this.options = context.options;
    this.hint = this.options.hint || [];
    this.direction = this.options.hintDirection || 'bottom';
    this.hints = $.isArray(this.hint) ? this.hint : [this.hint];

    this.events = {
      'summernote.keyup': (we, e) => {
        if (!e.isDefaultPrevented()) {
          this.handleKeyup(e);
        }
      },
      'summernote.keydown': (we, e) => {
        this.handleKeydown(e);
      },
      'summernote.disable summernote.dialog.shown': () => {
        this.hide();
      }
    };
  }

  shouldInitialize() {
    return this.hints.length > 0;
  }

  initialize() {
    this.lastWordRange = null;
    this.$popover = this.ui.popover({
      className: 'note-hint-popover',
      hideArrow: true,
      direction: ''
    }).render().appendTo(this.options.container);

    this.$popover.hide();
    this.$content = this.$popover.find('.popover-content,.note-popover-content');
    this.$content.on('click', '.note-hint-item', (e) => {
      this.$content.find('.active').removeClass('active');
      $(e.currentTarget).addClass('active');
      this.replace();
    });
  }

  destroy() {
    this.$popover.remove();
  }

  selectItem($item) {
    this.$content.find('.active').removeClass('active');
    $item.addClass('active');

    this.$content[0].scrollTop = $item[0].offsetTop - (this.$content.innerHeight() / 2);
  }

  moveDown() {
    const $current = this.$content.find('.note-hint-item.active');
    const $next = $current.next();

    if ($next.length) {
      this.selectItem($next);
    } else {
      let $nextGroup = $current.parent().next();

      if (!$nextGroup.length) {
        $nextGroup = this.$content.find('.note-hint-group').first();
      }

      this.selectItem($nextGroup.find('.note-hint-item').first());
    }
  }

  moveUp() {
    const $current = this.$content.find('.note-hint-item.active');
    const $prev = $current.prev();

    if ($prev.length) {
      this.selectItem($prev);
    } else {
      let $prevGroup = $current.parent().prev();

      if (!$prevGroup.length) {
        $prevGroup = this.$content.find('.note-hint-group').last();
      }

      this.selectItem($prevGroup.find('.note-hint-item').last());
    }
  }

  replace() {
    const $item = this.$content.find('.note-hint-item.active');

    if ($item.length) {
      const node = this.nodeFromItem($item);
      // XXX: consider to move codes to editor for recording redo/undo.
      this.lastWordRange.insertNode(node);
      range.createFromNode(node).collapse().select();

      this.lastWordRange = null;
      this.hide();
      this.context.triggerEvent('change', this.$editable.html(), this.$editable[0]);
      this.context.invoke('editor.focus');
    }
  }

  nodeFromItem($item) {
    const hint = this.hints[$item.data('index')];
    const item = $item.data('item');
    let node = hint.content ? hint.content(item) : item;
    if (typeof node === 'string') {
      node = dom.createText(node);
    }
    return node;
  }

  createItemTemplates(hintIdx, items) {
    const hint = this.hints[hintIdx];
    return items.map((item, idx) => {
      const $item = $('<div class="note-hint-item"/>');
      $item.append(hint.template ? hint.template(item) : item + '');
      $item.data({
        'index': hintIdx,
        'item': item
      });
      return $item;
    });
  }

  handleKeydown(e) {
    if (!this.$popover.is(':visible')) {
      return;
    }

    if (e.keyCode === key.code.ENTER) {
      e.preventDefault();
      this.replace();
    } else if (e.keyCode === key.code.UP) {
      e.preventDefault();
      this.moveUp();
    } else if (e.keyCode === key.code.DOWN) {
      e.preventDefault();
      this.moveDown();
    }
  }

  searchKeyword(index, keyword, callback) {
    const hint = this.hints[index];
    if (hint && hint.match.test(keyword) && hint.search) {
      const matches = hint.match.exec(keyword);
      hint.search(matches[1], callback);
    } else {
      callback();
    }
  }

  createGroup(idx, keyword) {
    const $group = $('<div class="note-hint-group note-hint-group-' + idx + '"/>');
    this.searchKeyword(idx, keyword, (items) => {
      items = items || [];
      if (items.length) {
        $group.html(this.createItemTemplates(idx, items));
        this.show();
      }
    });

    return $group;
  }

  handleKeyup(e) {
    if (!lists.contains([key.code.ENTER, key.code.UP, key.code.DOWN], e.keyCode)) {
      const wordRange = this.context.invoke('editor.createRange').getWordRange();
      const keyword = wordRange.toString();
      if (this.hints.length && keyword) {
        this.$content.empty();

        const bnd = func.rect2bnd(lists.last(wordRange.getClientRects()));
        if (bnd) {
          this.$popover.hide();
          this.lastWordRange = wordRange;
          this.hints.forEach((hint, idx) => {
            if (hint.match.test(keyword)) {
              this.createGroup(idx, keyword).appendTo(this.$content);
            }
          });
          // select first .note-hint-item
          this.$content.find('.note-hint-item:first').addClass('active');

          // set position for popover after group is created
          if (this.direction === 'top') {
            this.$popover.css({
              left: bnd.left,
              top: bnd.top - this.$popover.outerHeight() - POPOVER_DIST
            });
          } else {
            this.$popover.css({
              left: bnd.left,
              top: bnd.top + bnd.height + POPOVER_DIST
            });
          }
        }
      } else {
        this.hide();
      }
    }
  }

  show() {
    this.$popover.show();
  }

  hide() {
    this.$popover.hide();
  }
}
