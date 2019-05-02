import $ from 'jquery';
const EDITABLE_PADDING = 24;

export default class Statusbar {
  constructor(context) {
    this.$document = $(document);
    this.$statusbar = context.layoutInfo.statusbar;
    this.$editable = context.layoutInfo.editable;
    this.options = context.options;
  }

  initialize() {
    if (this.options.airMode || this.options.disableResizeEditor) {
      this.destroy();
      return;
    }

    this.$statusbar.on('mousedown', (event) => {
      event.preventDefault();
      event.stopPropagation();

      const editableTop = this.$editable.offset().top - this.$document.scrollTop();
      const onMouseMove = (event) => {
        let height = event.clientY - (editableTop + EDITABLE_PADDING);

        height = (this.options.minheight > 0) ? Math.max(height, this.options.minheight) : height;
        height = (this.options.maxHeight > 0) ? Math.min(height, this.options.maxHeight) : height;

        this.$editable.height(height);
      };

      this.$document.on('mousemove', onMouseMove).one('mouseup', () => {
        this.$document.off('mousemove', onMouseMove);
      });
    });
  }

  destroy() {
    this.$statusbar.off();
    this.$statusbar.addClass('locked');
  }
}
