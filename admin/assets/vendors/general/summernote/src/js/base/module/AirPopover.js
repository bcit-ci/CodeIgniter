import $ from 'jquery';
import env from '../core/env';
import func from '../core/func';
import lists from '../core/lists';
import dom from '../core/dom';

const AIR_MODE_POPOVER_X_OFFSET = 20;

export default class AirPopover {
  constructor(context) {
    this.context = context;
    this.ui = $.summernote.ui;
    this.options = context.options;
    this.events = {
      'summernote.keyup summernote.mouseup summernote.scroll': () => {
        this.update();
      },
      'summernote.disable summernote.change summernote.dialog.shown': () => {
        this.hide();
      },
      'summernote.focusout': (we, e) => {
        // [workaround] Firefox doesn't support relatedTarget on focusout
        //  - Ignore hide action on focus out in FF.
        if (env.isFF) {
          return;
        }

        if (!e.relatedTarget || !dom.ancestor(e.relatedTarget, func.eq(this.$popover[0]))) {
          this.hide();
        }
      }
    };
  }

  shouldInitialize() {
    return this.options.airMode && !lists.isEmpty(this.options.popover.air);
  }

  initialize() {
    this.$popover = this.ui.popover({
      className: 'note-air-popover'
    }).render().appendTo(this.options.container);
    const $content = this.$popover.find('.popover-content');

    this.context.invoke('buttons.build', $content, this.options.popover.air);
  }

  destroy() {
    this.$popover.remove();
  }

  update() {
    const styleInfo = this.context.invoke('editor.currentStyle');
    if (styleInfo.range && !styleInfo.range.isCollapsed()) {
      const rect = lists.last(styleInfo.range.getClientRects());
      if (rect) {
        const bnd = func.rect2bnd(rect);
        this.$popover.css({
          display: 'block',
          left: Math.max(bnd.left + bnd.width / 2, 0) - AIR_MODE_POPOVER_X_OFFSET,
          top: bnd.top + bnd.height
        });
        this.context.invoke('buttons.updateCurrentStyle', this.$popover);
      }
    } else {
      this.hide();
    }
  }

  hide() {
    this.$popover.hide();
  }
}
