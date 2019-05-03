import $ from 'jquery';
import env from '../core/env';

export default class HelpDialog {
  constructor(context) {
    this.context = context;

    this.ui = $.summernote.ui;
    this.$body = $(document.body);
    this.$editor = context.layoutInfo.editor;
    this.options = context.options;
    this.lang = this.options.langInfo;
  }

  initialize() {
    const $container = this.options.dialogsInBody ? this.$body : this.$editor;

    const body = [
      '<p class="text-center">',
      '<a href="http://summernote.org/" target="_blank">Summernote @@VERSION@@</a> · ',
      '<a href="https://github.com/summernote/summernote" target="_blank">Project</a> · ',
      '<a href="https://github.com/summernote/summernote/issues" target="_blank">Issues</a>',
      '</p>'
    ].join('');

    this.$dialog = this.ui.dialog({
      title: this.lang.options.help,
      fade: this.options.dialogsFade,
      body: this.createShortcutList(),
      footer: body,
      callback: ($node) => {
        $node.find('.modal-body,.note-modal-body').css({
          'max-height': 300,
          'overflow': 'scroll'
        });
      }
    }).render().appendTo($container);
  }

  destroy() {
    this.ui.hideDialog(this.$dialog);
    this.$dialog.remove();
  }

  createShortcutList() {
    const keyMap = this.options.keyMap[env.isMac ? 'mac' : 'pc'];
    return Object.keys(keyMap).map((key) => {
      const command = keyMap[key];
      const $row = $('<div><div class="help-list-item"/></div>');
      $row.append($('<label><kbd>' + key + '</kdb></label>').css({
        'width': 180,
        'margin-right': 10
      })).append($('<span/>').html(this.context.memo('help.' + command) || command));
      return $row.html();
    }).join('');
  }

  /**
   * show help dialog
   *
   * @return {Promise}
   */
  showHelpDialog() {
    return $.Deferred((deferred) => {
      this.ui.onDialogShown(this.$dialog, () => {
        this.context.triggerEvent('dialog.shown');
        deferred.resolve();
      });
      this.ui.showDialog(this.$dialog);
    }).promise();
  }

  show() {
    this.context.invoke('editor.saveRange');
    this.showHelpDialog().then(() => {
      this.context.invoke('editor.restoreRange');
    });
  }
}
