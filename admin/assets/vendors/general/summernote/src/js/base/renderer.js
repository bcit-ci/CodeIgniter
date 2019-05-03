import $ from 'jquery';

class Renderer {
  constructor(markup, children, options, callback) {
    this.markup = markup;
    this.children = children;
    this.options = options;
    this.callback = callback;
  }

  render($parent) {
    const $node = $(this.markup);

    if (this.options && this.options.contents) {
      $node.html(this.options.contents);
    }

    if (this.options && this.options.className) {
      $node.addClass(this.options.className);
    }

    if (this.options && this.options.data) {
      $.each(this.options.data, (k, v) => {
        $node.attr('data-' + k, v);
      });
    }

    if (this.options && this.options.click) {
      $node.on('click', this.options.click);
    }

    if (this.children) {
      const $container = $node.find('.note-children-container');
      this.children.forEach((child) => {
        child.render($container.length ? $container : $node);
      });
    }

    if (this.callback) {
      this.callback($node, this.options);
    }

    if (this.options && this.options.callback) {
      this.options.callback($node);
    }

    if ($parent) {
      $parent.append($node);
    }

    return $node;
  }
}

export default {
  create: (markup, callback) => {
    return function() {
      const options = typeof arguments[1] === 'object' ? arguments[1] : arguments[0];
      let children = $.isArray(arguments[0]) ? arguments[0] : [];
      if (options && options.children) {
        children = options.children;
      }
      return new Renderer(markup, children, options, callback);
    };
  }
};
