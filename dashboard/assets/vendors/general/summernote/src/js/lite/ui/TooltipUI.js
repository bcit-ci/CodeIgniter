class TooltipUI {
  constructor($node, options) {
    this.$node = $node;
    this.options = $.extend({}, {
      title: '',
      target: options.container,
      trigger: 'hover focus',
      placement: 'bottom'
    }, options);

    // create tooltip node
    this.$tooltip = $([
      '<div class="note-tooltip in">',
      '  <div class="note-tooltip-arrow"/>',
      '  <div class="note-tooltip-content"/>',
      '</div>'
    ].join(''));

    // define event
    if (this.options.trigger !== 'manual') {
      const showCallback = this.show.bind(this);
      const hideCallback = this.hide.bind(this);
      const toggleCallback = this.toggle.bind(this);

      this.options.trigger.split(' ').forEach(function(eventName) {
        if (eventName === 'hover') {
          $node.off('mouseenter mouseleave');
          $node.on('mouseenter', showCallback).on('mouseleave', hideCallback);
        } else if (eventName === 'click') {
          $node.on('click', toggleCallback);
        } else if (eventName === 'focus') {
          $node.on('focus', showCallback).on('blur', hideCallback);
        }
      });
    }
  }

  show() {
    const $node = this.$node;
    const offset = $node.offset();

    const $tooltip = this.$tooltip;
    const title = this.options.title || $node.attr('title') || $node.data('title');
    const placement = this.options.placement || $node.data('placement');

    $tooltip.addClass(placement);
    $tooltip.addClass('in');
    $tooltip.find('.note-tooltip-content').text(title);
    $tooltip.appendTo(this.options.target);

    const nodeWidth = $node.outerWidth();
    const nodeHeight = $node.outerHeight();
    const tooltipWidth = $tooltip.outerWidth();
    const tooltipHeight = $tooltip.outerHeight();

    if (placement === 'bottom') {
      $tooltip.css({
        top: offset.top + nodeHeight,
        left: offset.left + (nodeWidth / 2 - tooltipWidth / 2)
      });
    } else if (placement === 'top') {
      $tooltip.css({
        top: offset.top - tooltipHeight,
        left: offset.left + (nodeWidth / 2 - tooltipWidth / 2)
      });
    } else if (placement === 'left') {
      $tooltip.css({
        top: offset.top + (nodeHeight / 2 - tooltipHeight / 2),
        left: offset.left - tooltipWidth
      });
    } else if (placement === 'right') {
      $tooltip.css({
        top: offset.top + (nodeHeight / 2 - tooltipHeight / 2),
        left: offset.left + nodeWidth
      });
    }
  }

  hide() {
    this.$tooltip.removeClass('in');
    this.$tooltip.remove();
  }

  toggle() {
    if (this.$tooltip.hasClass('in')) {
      this.hide();
    } else {
      this.show();
    }
  }
}

export default TooltipUI;
