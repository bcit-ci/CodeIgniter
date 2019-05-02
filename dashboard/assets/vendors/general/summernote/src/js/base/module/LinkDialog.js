import $ from 'jquery';
import env from '../core/env';
import key from '../core/key';

export default class LinkDialog {
  constructor(context) {
    this.context = context;

    this.ui = $.summernote.ui;
    this.$body = $(document.body);
    this.$editor = context.layoutInfo.editor;
    this.options = context.options;
    this.lang = this.options.langInfo;

    context.memo('help.linkDialog.show', this.options.langInfo.help['linkDialog.show']);
  }

  initialize() {
    const $container = this.options.dialogsInBody ? this.$body : this.$editor;

    const body = [
      '<div class="form-group note-form-group">',
      `<label class="note-form-label">${this.lang.link.textToDisplay}</label>`,
      '<input class="note-link-text form-control note-form-control note-input" type="text" />',
      '</div>',
      '<div class="form-group note-form-group">',
      `<label class="note-form-label">${this.lang.link.url}</label>`,
      '<input class="note-link-url form-control note-form-control note-input" type="text" value="http://" />',
      '</div>',
      !this.options.disableLinkTarget
        ? $('<div/>').append(this.ui.checkbox({
          className: 'sn-checkbox-open-in-new-window',
          text: this.lang.link.openInNewWindow,
          checked: true
        }).render()).html()
        : ''
    ].join('');

    const buttonClass = 'btn btn-primary note-btn note-btn-primary note-link-btn';
    const footer = `<input type="button" href="#" class="${buttonClass}" value="${this.lang.link.insert}" disabled>`;

    this.$dialog = this.ui.dialog({
      className: 'link-dialog',
      title: this.lang.link.insert,
      fade: this.options.dialogsFade,
      body: body,
      footer: footer
    }).render().appendTo($container);
  }

  destroy() {
    this.ui.hideDialog(this.$dialog);
    this.$dialog.remove();
  }

  bindEnterKey($input, $btn) {
    $input.on('keypress', (event) => {
      if (event.keyCode === key.code.ENTER) {
        event.preventDefault();
        $btn.trigger('click');
      }
    });
  }

  /**
   * toggle update button
   */
  toggleLinkBtn($linkBtn, $linkText, $linkUrl) {
    this.ui.toggleBtn($linkBtn, $linkText.val() && $linkUrl.val());
  }

  /**
   * Show link dialog and set event handlers on dialog controls.
   *
   * @param {Object} linkInfo
   * @return {Promise}
   */
  showLinkDialog(linkInfo) {
    return $.Deferred((deferred) => {
      const $linkText = this.$dialog.find('.note-link-text');
      const $linkUrl = this.$dialog.find('.note-link-url');
      const $linkBtn = this.$dialog.find('.note-link-btn');
      const $openInNewWindow = this.$dialog
        .find('.sn-checkbox-open-in-new-window input[type=checkbox]');

      this.ui.onDialogShown(this.$dialog, () => {
        this.context.triggerEvent('dialog.shown');

        // if no url was given, copy text to url
        if (!linkInfo.url) {
          linkInfo.url = linkInfo.text;
        }

        $linkText.val(linkInfo.text);

        const handleLinkTextUpdate = () => {
          this.toggleLinkBtn($linkBtn, $linkText, $linkUrl);
          // if linktext was modified by keyup,
          // stop cloning text from linkUrl
          linkInfo.text = $linkText.val();
        };

        $linkText.on('input', handleLinkTextUpdate).on('paste', () => {
          setTimeout(handleLinkTextUpdate, 0);
        });

        const handleLinkUrlUpdate = () => {
          this.toggleLinkBtn($linkBtn, $linkText, $linkUrl);
          // display same link on `Text to display` input
          // when create a new link
          if (!linkInfo.text) {
            $linkText.val($linkUrl.val());
          }
        };

        $linkUrl.on('input', handleLinkUrlUpdate).on('paste', () => {
          setTimeout(handleLinkUrlUpdate, 0);
        }).val(linkInfo.url);

        if (!env.isSupportTouch) {
          $linkUrl.trigger('focus');
        }

        this.toggleLinkBtn($linkBtn, $linkText, $linkUrl);
        this.bindEnterKey($linkUrl, $linkBtn);
        this.bindEnterKey($linkText, $linkBtn);

        const isNewWindowChecked = linkInfo.isNewWindow !== undefined
          ? linkInfo.isNewWindow : this.context.options.linkTargetBlank;

        $openInNewWindow.prop('checked', isNewWindowChecked);

        $linkBtn.one('click', (event) => {
          event.preventDefault();

          deferred.resolve({
            range: linkInfo.range,
            url: $linkUrl.val(),
            text: $linkText.val(),
            isNewWindow: $openInNewWindow.is(':checked')
          });
          this.ui.hideDialog(this.$dialog);
        });
      });

      this.ui.onDialogHidden(this.$dialog, () => {
        // detach events
        $linkText.off('input paste keypress');
        $linkUrl.off('input paste keypress');
        $linkBtn.off('click');

        if (deferred.state() === 'pending') {
          deferred.reject();
        }
      });

      this.ui.showDialog(this.$dialog);
    }).promise();
  }

  /**
   * @param {Object} layoutInfo
   */
  show() {
    const linkInfo = this.context.invoke('editor.getLinkInfo');

    this.context.invoke('editor.saveRange');
    this.showLinkDialog(linkInfo).then((linkInfo) => {
      this.context.invoke('editor.restoreRange');
      this.context.invoke('editor.createLink', linkInfo);
    }).fail(() => {
      this.context.invoke('editor.restoreRange');
    });
  }
}
