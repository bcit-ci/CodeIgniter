import $ from 'jquery';
import lists from '../core/lists';
import dom from '../core/dom';

/**
 * Image popover module
 *  mouse events that show/hide popover will be handled by Handle.js.
 *  Handle.js will receive the events and invoke 'imagePopover.update'.
 */
export default class ImagePopover {
  constructor(context) {
    this.context = context;
    this.ui = $.summernote.ui;

    this.editable = context.layoutInfo.editable[0];
    this.options = context.options;

    this.events = {
      'summernote.disable': () => {
        this.hide();
      }
    };
  }

  shouldInitialize() {
    return !lists.isEmpty(this.options.popover.image);
  }

  initialize() {
    this.$popover = this.ui.popover({
      className: 'note-image-popover'
    }).render().appendTo(this.options.container);
    const $content = this.$popover.find('.popover-content,.note-popover-content');
    this.context.invoke('buttons.build', $content, this.options.popover.image);
  }

  destroy() {
    this.$popover.remove();
  }

  update(target) {
    if (dom.isImg(target)) {
      const pos = dom.posFromPlaceholder(target);
      const posEditor = dom.posFromPlaceholder(this.editable);
      this.$popover.css({
        display: 'block',
        left: this.options.popatmouse ? event.pageX - 20 : pos.left,
        top: this.options.popatmouse ? event.pageY : Math.min(pos.top, posEditor.top)
      });
    } else {
      this.hide();
    }
  }

  hide() {
    this.$popover.hide();
  }
}
