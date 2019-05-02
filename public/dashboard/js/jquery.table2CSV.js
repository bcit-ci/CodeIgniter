/*
   Based on the jQuery plugin found at http://www.kunalbabre.com/projects/table2CSV.php
   Re-worked by ZachWick for LectureTools Inc. Sept. 2011
   Copyright (c) 2011 LectureTools Inc.

   Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/
jQuery.fn.table2CSV = function(options) {
    var options = jQuery.extend({
        separator: ',',
        header: [],
        columns: [],
        delivery: 'popup' /* popup, value, download */
    },
    options);

    var csvData = [];
    var headerArr = [];
    var el = this;
    var basic = options.columns.length == 0 ? true : false;
    var columnNumbers = [];
    var columnCounter = 0;

    //header
    var numCols = options.header.length; 
    var tmpRow = []; // construct header avalible array

    if (numCols > 0) {
       if (basic) {
          for (var i = 0; i < numCols; i++) {
             tmpRow[tmpRow.length] = formatData(options.header[i]);
          }
       } else if (!basic) {
          for (var o = 0; o < numCols; o++) {
             for (var i = 0; i < options.columns.length; i++) {
                if (options.columns[i] == options.header[o]) {
                   tmpRow[tmpRow.length] = formatData(options.header[o]);
		   columnNumbers[columnCounter] = o;
		   columnCounter++;
                }
             }
          }       
       }
    } else {
       jQuery(el).filter(':visible').find('th').each(function() {
          if (jQuery(this).css('display') != 'none') tmpRow[tmpRow.length] = formatData(jQuery(this).html());
       });
    }

    row2CSV(tmpRow);

    // actual data
    if (basic) {
       jQuery(el).find('tr').each(function() {
           var tmpRow = [];
           jQuery(this).filter(':visible').find('td').each(function() {
              if (jQuery(this).css('display') != 'none') {
                 tmpRow[tmpRow.length] = jQuery.trim(formatData(jQuery(this).html()));
              }
           });
           row2CSV(tmpRow);
       });
    } else {
       jQuery(el).find('tr').each(function() {
          var tmpRow = [];
          var columnCounter = 0;
          jQuery(this).filter(':visible').find('td').each(function() {
             if ((jQuery(this).css('display') != 'none') && (columnCounter in columnNumbers)) {
                tmpRow[tmpRow.length] = jQuery.trim(formatData(jQuery(this).html()));
             }
             columnCounter++;
          });
          row2CSV(tmpRow);
       });
    }
    if ((options.delivery == 'popup')||(options.delivery == 'download')) {
        var mydata = csvData.join('\n');
        return popup(mydata);
    } else {
        var mydata = csvData.join('\n');
        return mydata;
    }

    function row2CSV(tmpRow) {
        var tmp = tmpRow.join('') // to remove any blank rows
        // alert(tmp);
        if (tmpRow.length > 0 && tmp != '') {
            var mystr = tmpRow.join(options.separator);
            csvData[csvData.length] = jQuery.trim(mystr);
        }
    }
    function formatData(input) {
        // replace " with “
        var regexp = new RegExp(/["]/g);
        var output = input.replace(regexp, "“");
        //HTML
        var regexp = new RegExp(/\<[^\<]+\>/g);
        var output = output.replace(regexp, "");
        if (output == "") return '';
        return '' + output + '';
    }
    function popup(data) {
	if (options.delivery == 'download') {
           window.location='data:text/csv;charset=utf8,' + encodeURIComponent(data);
           return true;
	} else {
           var generator = window.open('', 'csv', 'height=400,width=600');
           generator.document.write('<html><head><title>CSV</title>');
           generator.document.write('</head><body >');
           generator.document.write('<textArea cols=70 rows=15 wrap="off" >');
           generator.document.write(data);
           generator.document.write('</textArea>');
           generator.document.write('</body></html>');
           generator.document.close();
           return true;
	}
    }
};
