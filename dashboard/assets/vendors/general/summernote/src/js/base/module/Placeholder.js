import $ from 'jquery';
export default class Placeholder {
  constructor(context) {
    this.context = context;

    this.$editingArea = context.layoutInfo.editingArea;
    this.options = context.options;
    this.events = {
      'summernote.init summernote.change': () => {
        this.update();
      },
      'summernote.codeview.toggled': () => {
        this.update();
      }
    };
  }

  shouldInitialize() {
    return !!this.options.placeholder;
  }

  initialize() {
    this.$placeholder = $('<div class="note-placeholder">');
    this.$placeholder.on('click', () => {
      this.context.invoke('focus');
    }).html(this.options.placeholder).prependTo(this.$editingArea);

    this.update();
  }

  destroy() {
    this.$placeholder.remove();
  }

  update() {
    const isShow = !this.context.invoke('codeview.isActivated') && this.context.invoke('editor.isEmpty');
    this.$placeholder.toggle(isShow);
  }
}
