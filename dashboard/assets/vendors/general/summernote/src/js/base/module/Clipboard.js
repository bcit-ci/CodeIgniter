import lists from '../core/lists';

export default class Clipboard {
  constructor(context) {
    this.context = context;
    this.$editable = context.layoutInfo.editable;
  }

  initialize() {
    this.$editable.on('paste', this.pasteByEvent.bind(this));
  }

  /**
   * paste by clipboard event
   *
   * @param {Event} event
   */
  pasteByEvent(event) {
    const clipboardData = event.originalEvent.clipboardData;
    if (clipboardData && clipboardData.items && clipboardData.items.length) {
      // paste img file
      const item = clipboardData.items.length > 1 ? clipboardData.items[1] : lists.head(clipboardData.items);
      if (item.kind === 'file' && item.type.indexOf('image/') !== -1) {
        this.context.invoke('editor.insertImagesOrCallback', [item.getAsFile()]);
      }
      this.context.invoke('editor.afterCommand');
    }
  }
}
