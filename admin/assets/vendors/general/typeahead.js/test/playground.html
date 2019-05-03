<!DOCTYPE html>
<html>
  <head>
    <script src="../bower_components/jquery/jquery.js"></script>
    <script src="../dist/typeahead.bundle.js"></script>

    <style>
      .container {
        width: 800px;
        margin: 50px auto;
      }

      .typeahead-wrapper {
        display: block;
        margin: 50px 0;
      }

      .tt-dropdown-menu {
        background-color: #fff;
        border: 1px solid #000;
      }

      .tt-suggestion.tt-cursor {
        background-color: #ccc;
      }

      .triggered-events {
        float: right;
        width: 500px;
        height: 300px;
      }
    </style>
  </head>

  <body>
    <div class="container">
      <textarea class="triggered-events"></textarea>
      <form action="/where" method="GET">
        <div class="typeahead-wrapper">
          <input class="states" name="states" type="text" placeholder="states" value="Michigan">
          <input type="submit">
        </div>
      </form>
      <div class="typeahead-wrapper">
        <input class="bad-tokens" type="text" placeholder="bad tokens">
      </div>
      <div class="typeahead-wrapper">
        <input class="regex-symbols" type="text" placeholder="regex symbols">
      </div>
      <div class="typeahead-wrapper">
        <input class="header-footer" type="text" placeholder="header footer">
      </div>
      <div class="typeahead-wrapper">
        <input class="ltr" type="text" placeholder="ltr">
      </div>
      <div class="typeahead-wrapper">
        <input class="rtl" type="text" placeholder="rtl">
      </div>
      <div class="typeahead-wrapper">
        <input class="mixed" type="text" placeholder="mixed">
      </div>
    </div>
    </div>

    <script>
      var states = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.whitespace,
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        local: [
          'Alabama',
          'Alaska',
          'Arizona',
          'Arkansas',
          'California',
          'Colorado',
          'Connecticut',
          'Delaware',
          'Florida',
          'Georgia',
          'Hawaii',
          'Idaho',
          'Illinois',
          'Indiana',
          'Iowa',
          'Kansas',
          'Kentucky',
          'Louisiana',
          'Maine',
          'Maryland',
          'Massachusetts',
          'Michigan',
          'Minnesota',
          'Mississippi',
          'Missouri',
          'Montana',
          'Nebraska',
          'Nevada',
          'New Hampshire',
          'New Jersey',
          'New Mexico',
          'New York',
          'North Carolina',
          'North Dakota',
          'Ohio',
          'Oklahoma',
          'Oregon',
          'Pennsylvania',
          'Rhode Island',
          'South Carolina',
          'South Dakota',
          'Tennessee',
          'Texas',
          'Utah',
          'Vermont',
          'Virginia',
          'Washington',
          'West Virginia',
          'Wisconsin',
          'Wyoming'
        ]
      });

      states.initialize();

      $('.states').typeahead({
        highlight: true
      },
      {
        source: states
      });


      var badTokens = new Bloodhound({
        datumTokenizer: function(d) { return d.tokens; },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        local: [
          {
            value1: 'all bad',
            jake: '111',
            tokens: ['  ', ' ', null, undefined, false, 'all', 'bad']
          },
          {
            value1: 'whitespace',
            jake: '112',
            tokens: ['  ', ' ', '\t', '\n', 'whitespace']
          },
          {
            value1: 'undefined',
            jake: '113',
            tokens: [undefined, 'undefined']
          },
          {
            value1: 'null',
            jake: '114',
            tokens: [null, 'null']
          },
          {
            value1: 'false',
            jake: '115',
            tokens: [false, 'false']
          }
        ]
      });

      badTokens.initialize();

      $('.bad-tokens').typeahead(null, {
        displayKey: 'value1',
        source: badTokens
      });

      var regexSymbols = new Bloodhound({
        datumTokenizer: function(d) { 
          return Bloodhound.tokenizers.whitespace(d.val); 
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        local: [
          { val: '*.js' },
          { val: '[Tt]ypeahead.js' },
          { val: '^typeahead.js$' },
          { val: 'typeahead.js(0.8.2)' },
          { val: 'typeahead.js(@\\d.\\d.\\d)' },
          { val: 'typeahead.js@0.8.2' }
        ]
      });

      regexSymbols.initialize();

      $('.regex-symbols').typeahead(null, {
        displayKey: 'val',
        source: regexSymbols
      });

      var abc = new Bloodhound({
        datumTokenizer: function(d) { 
          return Bloodhound.tokenizers.whitespace(d.val); 
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        local: [
          { val: 'a' }, 
          { val: 'ab' }, 
          { val: 'abc' }, 
          { val: 'abcd' }, 
          { val: 'abcde' }
        ]
      });

      abc.initialize();

      $('.header-footer').typeahead(null, {
        displayKey: 'val',
        source: abc,
        templates: {
          header: '<h3>Header</h3>',
          footer: '<h3>Footer</h3>'
        }
      },
      {
        displayKey: 'val',
        source: abc,
        templates: {
          header: '<h3>start</h3>',
          footer: '<h3>end</h3>',
          empty: '<h3>empty</h3>'
        }
      });

      var ltr = new Bloodhound({
        datumTokenizer: function(d) { 
          return Bloodhound.tokenizers.whitespace(d.val); 
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        local: [
          { val: "one" },
          { val: "two three" },
          { val: "four" },
          { val: "five six" },
          { val: "seven" }
        ]
      });

      ltr.initialize();

      $('.ltr').typeahead({
        highlight: true
      },
      {
        displayKey: 'val',
        source: ltr
      });

      var rtl = new Bloodhound({
        datumTokenizer: function(d) { 
          return Bloodhound.tokenizers.whitespace(d.val); 
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        local: [
          { val: "שלום" },
          { val: "ערב טוב" },
          { val: "מה שלומך" },
          { val: "רב תודות" },
          { val: "אין דבר" }
        ]
      });

      rtl.initialize();

      $('.rtl').typeahead({
        highlight: true
      },
      {
        displayKey: 'val',
        source: rtl
      });

      var mixed = new Bloodhound({
        datumTokenizer: function(d) { 
          return Bloodhound.tokenizers.whitespace(d.val); 
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        local: [
          { val: "שלום" },
          { val: "ערב טוב" },
          { val: "מה שלומך" },
          { val: "one" },
          { val: "two three" }
        ]
      });

      mixed.initialize();

      $('.mixed').typeahead({
        highlight: true
      },
      {
        displayKey: 'val',
        source: mixed
      });


      $('input').on([
        'typeahead:active',
        'typeahead:idle',
        'typeahead:open',
        'typeahead:close',
        'typeahead:change',
        'typeahead:render',
        'typeahead:select',
        'typeahead:autocomplete',
        'typeahead:cursorchange',
      ].join(' '), logTypeaheadEvent);

      $('form').on('submit', logSubmitEvent);
      
      function logSubmitEvent($e) {
        var text; 

        $e && $e.preventDefault(); 

        text = JSON.stringify($(this).serializeArray()); 
        writeToTextarea('submit', text);
      }

      function logTypeaheadEvent($e) {
        var args, type, text;

        args = [].slice.call(arguments, 1);
        type = $e.type;
        text = window.JSON ? JSON.stringify(args) : '';

        writeToTextarea(type, text);
      }

      function writeToTextarea(/* lines */) {
        var $textarea, val, text;

        $textarea = $('.triggered-events');
        val = $textarea.val();
        text = [].join.call(arguments, '\n');

        $textarea.val([val, text, '\n'].join('\n'));
        $textarea[0].scrollTop = $textarea[0].scrollHeight;
      }
    </script>
  </body>
</html>
