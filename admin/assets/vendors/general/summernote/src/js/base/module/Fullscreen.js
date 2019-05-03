import $ from 'jquery';

export default class Fullscreen {
  constructor(context) {
    this.context = context;

    this.$editor = context.layoutInfo.editor;
    this.$toolbar = context.layoutInfo.toolbar;
    this.$editable = context.layoutInfo.editable;
    this.$codable = context.layoutInfo.codable;

    this.$window = $(window);
    this.$scrollbar = $('html, body');

    this.onResize = () => {
      this.resizeTo({
        h: this.$window.height() - this.$toolbar.outerHeight()
      });
    };
  }

  resizeTo(size) {
    this.$editable.css('height', size.h);
    this.$codable.css('height', size.h);
    if (this.$codable.data('cmeditor')) {
      this.$codable.data('cmeditor').setsize(null, size.h);
    }
  }

  /**
   * toggle fullscreen
   */
  toggle() {
    this.$editor.toggleClass('fullscreen');
    if (this.isFullscreen()) {
      this.$editable.data('orgHeight', this.$editable.css('height'));
      this.$editable.data('orgMaxHeight', this.$editable.css('maxHeight'));
      this.$editable.css('maxHeight', '');
      this.$window.on('resize', this.onResize).trigger('resize');
      this.$scrollbar.css('overflow', 'hidden');
    } else {
      this.$window.off('resize', this.onResize);
      this.resizeTo({ h: this.$editable.data('orgHeight') });
      this.$editable.css('maxHeight', this.$editable.css('orgMaxHeight'));
      this.$scrollbar.css('overflow', 'visible');
    }

    this.context.invoke('toolbar.updateFullscreen', this.isFullscreen());
  }

  isFullscreen() {
    return this.$editor.hasClass('fullscreen');
  }
}
