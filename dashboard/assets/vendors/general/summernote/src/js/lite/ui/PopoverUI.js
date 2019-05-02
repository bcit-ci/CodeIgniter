class PopoverUI {
  constructor($node, options) {
    this.$node = $node;
    this.options = $.extend({}, {
      title: '',
      content: '',
      target: options.container,
      trigger: 'hover focus',
      placement: 'bottom'
    }, options);

    // create popover node
    this.$popover = $([
      '<div class="note-popover in">',
      ' <div class="note-popover-arrow" />',
      ' <div class="note-popover-content" />',
      '</div>'
    ].join(''));

    // define event
    if (this.options.trigger !== 'manual') {
      const showCallback = this.show.bind(this);
      const hideCallback = this.hide.bind(this);
      const toggleCallback = this.toggle.bind(this);
      this.options.trigger.split(' ').forEach(function(eventName) {
        if (eventName === 'hover') {
          $node.off('mouseenter').on('mouseenter', showCallback);
          $node.off('mouseleave').on('mouseleave', hideCallback);
        } else if (eventName === 'click') {
          $node.on('click', toggleCallback);
        } else if (eventName === 'focus') {
          $node.on('focus', showCallback);
          $node.on('blur', hideCallback);
        }
      });
    }
  }

  show() {
    const $node = this.$node;
    const offset = $node.offset();
    const $popover = this.$popover;
    const content = this.options.content || $node.data('content');
    const placement = $node.data('placement') || this.options.placement;
    const dist = 6;

    $popover.addClass(placement);
    $popover.addClass('in');
    $popover.find('.note-popover-content').html(content);
    $popover.appendTo(this.options.target);

    const nodeWidth = $node.outerWidth();
    const nodeHeight = $node.outerHeight();
    const popoverWidth = $popover.outerWidth();
    const popoverHeight = $popover.outerHeight();

    if (placement === 'bottom') {
      $popover.css({
        top: offset.top + nodeHeight + dist,
        left: offset.left + (nodeWidth / 2 - popoverWidth / 2)
      });
    } else if (placement === 'top') {
      $popover.css({
        top: offset.top - popoverHeight - dist,
        left: offset.left + (nodeWidth / 2 - popoverWidth / 2)
      });
    } else if (placement === 'left') {
      $popover.css({
        top: offset.top + (nodeHeight / 2 - popoverHeight / 2),
        left: offset.left - popoverWidth - dist
      });
    } else if (placement === 'right') {
      $popover.css({
        top: offset.top + (nodeHeight / 2 - popoverHeight / 2),
        left: offset.left + nodeWidth + dist
      });
    }
  }

  hide() {
    this.$popover.removeClass('in');
    this.$popover.remove();
  }

  toggle() {
    if (this.$popover.hasClass('in')) {
      this.hide();
    } else {
      this.show();
    }
  }
}

export default PopoverUI;
