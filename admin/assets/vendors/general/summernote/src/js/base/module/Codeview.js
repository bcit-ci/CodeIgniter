import env from '../core/env';
import dom from '../core/dom';

let CodeMirror;
if (env.hasCodeMirror) {
  if (env.isSupportAmd) {
    require(['codemirror'], function(cm) {
      CodeMirror = cm;
    });
  } else {
    CodeMirror = window.CodeMirror;
  }
}

/**
 * @class Codeview
 */
export default class CodeView {
  constructor(context) {
    this.context = context;
    this.$editor = context.layoutInfo.editor;
    this.$editable = context.layoutInfo.editable;
    this.$codable = context.layoutInfo.codable;
    this.options = context.options;
  }

  sync() {
    const isCodeview = this.isActivated();
    if (isCodeview && env.hasCodeMirror) {
      this.$codable.data('cmEditor').save();
    }
  }

  /**
   * @return {Boolean}
   */
  isActivated() {
    return this.$editor.hasClass('codeview');
  }

  /**
   * toggle codeview
   */
  toggle() {
    if (this.isActivated()) {
      this.deactivate();
    } else {
      this.activate();
    }
    this.context.triggerEvent('codeview.toggled');
  }

  /**
   * activate code view
   */
  activate() {
    this.$codable.val(dom.html(this.$editable, this.options.prettifyHtml));
    this.$codable.height(this.$editable.height());

    this.context.invoke('toolbar.updateCodeview', true);
    this.$editor.addClass('codeview');
    this.$codable.focus();

    // activate CodeMirror as codable
    if (env.hasCodeMirror) {
      const cmEditor = CodeMirror.fromTextArea(this.$codable[0], this.options.codemirror);

      // CodeMirror TernServer
      if (this.options.codemirror.tern) {
        const server = new CodeMirror.TernServer(this.options.codemirror.tern);
        cmEditor.ternServer = server;
        cmEditor.on('cursorActivity', (cm) => {
          server.updateArgHints(cm);
        });
      }

      cmEditor.on('blur', (event) => {
        this.context.triggerEvent('blur.codeview', cmEditor.getValue(), event);
      });

      // CodeMirror hasn't Padding.
      cmEditor.setSize(null, this.$editable.outerHeight());
      this.$codable.data('cmEditor', cmEditor);
    } else {
      this.$codable.on('blur', (event) => {
        this.context.triggerEvent('blur.codeview', this.$codable.val(), event);
      });
    }
  }

  /**
   * deactivate code view
   */
  deactivate() {
    // deactivate CodeMirror as codable
    if (env.hasCodeMirror) {
      const cmEditor = this.$codable.data('cmEditor');
      this.$codable.val(cmEditor.getValue());
      cmEditor.toTextArea();
    }

    const value = dom.value(this.$codable, this.options.prettifyHtml) || dom.emptyPara;
    const isChange = this.$editable.html() !== value;

    this.$editable.html(value);
    this.$editable.height(this.options.height ? this.$codable.height() : 'auto');
    this.$editor.removeClass('codeview');

    if (isChange) {
      this.context.triggerEvent('change', this.$editable.html(), this.$editable);
    }

    this.$editable.focus();

    this.context.invoke('toolbar.updateCodeview', false);
  }

  destroy() {
    if (this.isActivated()) {
      this.deactivate();
    }
  }
}
