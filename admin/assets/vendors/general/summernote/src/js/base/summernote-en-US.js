import $ from 'jquery';

$.summernote = $.summernote || {
  lang: {}
};

$.extend($.summernote.lang, {
  'en-US': {
    font: {
      bold: 'Bold',
      italic: 'Italic',
      underline: 'Underline',
      clear: 'Remove Font Style',
      height: 'Line Height',
      name: 'Font Family',
      strikethrough: 'Strikethrough',
      subscript: 'Subscript',
      superscript: 'Superscript',
      size: 'Font Size'
    },
    image: {
      image: 'Picture',
      insert: 'Insert Image',
      resizeFull: 'Resize Full',
      resizeHalf: 'Resize Half',
      resizeQuarter: 'Resize Quarter',
      floatLeft: 'Float Left',
      floatRight: 'Float Right',
      floatNone: 'Float None',
      shapeRounded: 'Shape: Rounded',
      shapeCircle: 'Shape: Circle',
      shapeThumbnail: 'Shape: Thumbnail',
      shapeNone: 'Shape: None',
      dragImageHere: 'Drag image or text here',
      dropImage: 'Drop image or Text',
      selectFromFiles: 'Select from files',
      maximumFileSize: 'Maximum file size',
      maximumFileSizeError: 'Maximum file size exceeded.',
      url: 'Image URL',
      remove: 'Remove Image',
      original: 'Original'
    },
    video: {
      video: 'Video',
      videoLink: 'Video Link',
      insert: 'Insert Video',
      url: 'Video URL',
      providers: '(YouTube, Vimeo, Vine, Instagram, DailyMotion or Youku)'
    },
    link: {
      link: 'Link',
      insert: 'Insert Link',
      unlink: 'Unlink',
      edit: 'Edit',
      textToDisplay: 'Text to display',
      url: 'To what URL should this link go?',
      openInNewWindow: 'Open in new window'
    },
    table: {
      table: 'Table',
      addRowAbove: 'Add row above',
      addRowBelow: 'Add row below',
      addColLeft: 'Add column left',
      addColRight: 'Add column right',
      delRow: 'Delete row',
      delCol: 'Delete column',
      delTable: 'Delete table'
    },
    hr: {
      insert: 'Insert Horizontal Rule'
    },
    style: {
      style: 'Style',
      p: 'Normal',
      blockquote: 'Quote',
      pre: 'Code',
      h1: 'Header 1',
      h2: 'Header 2',
      h3: 'Header 3',
      h4: 'Header 4',
      h5: 'Header 5',
      h6: 'Header 6'
    },
    lists: {
      unordered: 'Unordered list',
      ordered: 'Ordered list'
    },
    options: {
      help: 'Help',
      fullscreen: 'Full Screen',
      codeview: 'Code View'
    },
    paragraph: {
      paragraph: 'Paragraph',
      outdent: 'Outdent',
      indent: 'Indent',
      left: 'Align left',
      center: 'Align center',
      right: 'Align right',
      justify: 'Justify full'
    },
    color: {
      recent: 'Recent Color',
      more: 'More Color',
      background: 'Background Color',
      foreground: 'Foreground Color',
      transparent: 'Transparent',
      setTransparent: 'Set transparent',
      reset: 'Reset',
      resetToDefault: 'Reset to default',
      cpSelect: 'Select'
    },
    shortcut: {
      shortcuts: 'Keyboard shortcuts',
      close: 'Close',
      textFormatting: 'Text formatting',
      action: 'Action',
      paragraphFormatting: 'Paragraph formatting',
      documentStyle: 'Document Style',
      extraKeys: 'Extra keys'
    },
    help: {
      'insertParagraph': 'Insert Paragraph',
      'undo': 'Undoes the last command',
      'redo': 'Redoes the last command',
      'tab': 'Tab',
      'untab': 'Untab',
      'bold': 'Set a bold style',
      'italic': 'Set a italic style',
      'underline': 'Set a underline style',
      'strikethrough': 'Set a strikethrough style',
      'removeFormat': 'Clean a style',
      'justifyLeft': 'Set left align',
      'justifyCenter': 'Set center align',
      'justifyRight': 'Set right align',
      'justifyFull': 'Set full align',
      'insertUnorderedList': 'Toggle unordered list',
      'insertOrderedList': 'Toggle ordered list',
      'outdent': 'Outdent on current paragraph',
      'indent': 'Indent on current paragraph',
      'formatPara': 'Change current block\'s format as a paragraph(P tag)',
      'formatH1': 'Change current block\'s format as H1',
      'formatH2': 'Change current block\'s format as H2',
      'formatH3': 'Change current block\'s format as H3',
      'formatH4': 'Change current block\'s format as H4',
      'formatH5': 'Change current block\'s format as H5',
      'formatH6': 'Change current block\'s format as H6',
      'insertHorizontalRule': 'Insert horizontal rule',
      'linkDialog.show': 'Show Link Dialog'
    },
    history: {
      undo: 'Undo',
      redo: 'Redo'
    },
    specialChar: {
      specialChar: 'SPECIAL CHARACTERS',
      select: 'Select Special characters'
    }
  }
});
