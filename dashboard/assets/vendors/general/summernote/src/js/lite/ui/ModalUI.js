class ModalUI {
  constructor($node, options) {
    this.options = $.extend({}, {
      target: options.container || 'body'
    }, options);

    this.$modal = $node;
    this.$backdrop = $('<div class="note-modal-backdrop" />');
  }

  show() {
    if (this.options.target === 'body') {
      this.$backdrop.css('position', 'fixed');
      this.$modal.css('position', 'fixed');
    } else {
      this.$backdrop.css('position', 'absolute');
      this.$modal.css('position', 'absolute');
    }

    this.$backdrop.appendTo(this.options.target).show();
    this.$modal.appendTo(this.options.target).addClass('open').show();

    this.$modal.trigger('note.modal.show');
    this.$modal.off('click', '.close').on('click', '.close', this.hide.bind(this));
  }

  hide() {
    this.$modal.removeClass('open').hide();
    this.$backdrop.hide();
    this.$modal.trigger('note.modal.hide');
  }
}

export default ModalUI;
