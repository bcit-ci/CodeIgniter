import $ from 'jquery';
import env from './base/core/env';
import lists from './base/core/lists';
import Context from './base/Context';

$.fn.extend({
  /**
   * Summernote API
   *
   * @param {Object|String}
   * @return {this}
   */
  summernote: function() {
    const type = $.type(lists.head(arguments));
    const isExternalAPICalled = type === 'string';
    const hasInitOptions = type === 'object';

    const options = $.extend({}, $.summernote.options, hasInitOptions ? lists.head(arguments) : {});

    // Update options
    options.langInfo = $.extend(true, {}, $.summernote.lang['en-US'], $.summernote.lang[options.lang]);
    options.icons = $.extend(true, {}, $.summernote.options.icons, options.icons);
    options.tooltip = options.tooltip === 'auto' ? !env.isSupportTouch : options.tooltip;

    this.each((idx, note) => {
      const $note = $(note);
      if (!$note.data('summernote')) {
        const context = new Context($note, options);
        $note.data('summernote', context);
        $note.data('summernote').triggerEvent('init', context.layoutInfo);
      }
    });

    const $note = this.first();
    if ($note.length) {
      const context = $note.data('summernote');
      if (isExternalAPICalled) {
        return context.invoke.apply(context, lists.from(arguments));
      } else if (options.focus) {
        context.invoke('editor.focus');
      }
    }

    return this;
  }
});
