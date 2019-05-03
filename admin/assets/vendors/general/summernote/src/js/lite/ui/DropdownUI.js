class DropdownUI {
  constructor($node, options) {
    this.$button = $node;
    this.options = $.extend({}, {
      target: options.container
    }, options);
    this.setEvent();
  }

  setEvent() {
    this.$button.on('click', (e) => {
      this.toggle();
      e.stopImmediatePropagation();
    });
  }

  clear() {
    var $parent = $('.note-btn-group.open');
    $parent.find('.note-btn.active').removeClass('active');
    $parent.removeClass('open');
  }

  show() {
    this.$button.addClass('active');
    this.$button.parent().addClass('open');

    var $dropdown = this.$button.next();
    var offset = $dropdown.offset();
    var width = $dropdown.outerWidth();
    var windowWidth = $(window).width();
    var targetMarginRight = parseFloat($(this.options.target).css('margin-right'));

    if (offset.left + width > windowWidth - targetMarginRight) {
      $dropdown.css('margin-left', windowWidth - targetMarginRight - (offset.left + width));
    } else {
      $dropdown.css('margin-left', '');
    }
  }

  hide() {
    this.$button.removeClass('active');
    this.$button.parent().removeClass('open');
  }

  toggle() {
    var isOpened = this.$button.parent().hasClass('open');

    this.clear();

    if (isOpened) {
      this.hide();
    } else {
      this.show();
    }
  }
}

$(document).on('click', function(e) {
  if (!$(e.target).closest('.note-btn-group').length) {
    $('.note-btn-group.open').removeClass('open');
  }
});

$(document).on('click.note-dropdown-menu', function(e) {
  $(e.target).closest('.note-dropdown-menu').parent().removeClass('open');
});

export default DropdownUI;
