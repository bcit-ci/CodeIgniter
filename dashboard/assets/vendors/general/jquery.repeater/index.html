<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Repeater</title>
    <title>jquery.repeater</title>
    <style>
    html, body {
        font-family: Helvetica, Arial, sans-serif;
        color: rgb(80, 80, 80);
    }
    </style>
</head>
<body>
    <h2>Repeater</h2>
    <form action="echo.php" class="repeater" enctype="multipart/form-data">
      <div data-repeater-list="group-a">
        <div data-repeater-item>
          <input name="untyped-input" value="A"/>
    
          <input type="text" name="text-input" value="A"/>
    
          <input type="date" name="date-input"/>
    
          <textarea name="textarea-input">A</textarea>
    
          <input type="radio" name="radio-input" value="A" checked/>
          <input type="radio" name="radio-input" value="B"/>
    
          <input type="checkbox" name="checkbox-input" value="A" checked/>
          <input type="checkbox" name="checkbox-input" value="B"/>
    
          <select name="select-input">
            <option value="A" selected>A</option>
            <option value="B">B</option>
          </select>
    
          <select name="multiple-select-input" multiple>
            <option value="A" selected>A</option>
            <option value="B" selected>B</option>
          </select>
    
          <input data-repeater-delete type="button" value="Delete"/>
        </div>
        <div data-repeater-item>
          <input name="untyped-input" value="A"/>
    
          <input type="text" name="text-input" value="B"/>
    
          <input type="date" name="date-input"/>
    
          <textarea name="textarea-input">B</textarea>
    
          <input type="radio" name="radio-input" value="A" />
          <input type="radio" name="radio-input" value="B" checked/>
    
          <input type="checkbox" name="checkbox-input" value="A"/>
          <input type="checkbox" name="checkbox-input" value="B" checked/>
    
          <select name="select-input">
            <option value="A">A</option>
            <option value="B" selected>B</option>
          </select>
    
          <select name="multiple-select-input" multiple>
            <option value="A" selected>A</option>
            <option value="B" selected>B</option>
          </select>
    
          <input data-repeater-delete type="button" value="Delete"/>
        </div>
      </div>
      <input data-repeater-create type="button" value="Add"/>
    </form>
    
    <h2>Nested</h2>
    <form action="echo.php" class="outer-repeater">
      <div data-repeater-list="outer-group" class="outer">
        <div data-repeater-item class="outer">
          <input type="text" name="text-input" value="A" class="outer"/>
          <input data-repeater-delete type="button" value="Delete" class="outer"/>
          <div class="inner-repeater">
            <div data-repeater-list="inner-group" class="inner">
              <div data-repeater-item class="inner">
                <input type="text" name="inner-text-input" value="B" class="inner"/>
                <input data-repeater-delete type="button" value="Delete" class="inner"/>
              </div>
            </div>
            <input data-repeater-create type="button" value="Add" class="inner"/>
          </div>
        </div>
      </div>
      <input data-repeater-create type="button" value="Add" class="outer"/>
    </form>
    
    <script src="jquery-1.11.1.js"></script>
    <script src="jquery.repeater.js"></script>
    <script>
    $(document).ready(function () {
        'use strict';

        $('.repeater').repeater({
            defaultValues: {
                'textarea-input': 'foo',
                'text-input': 'bar',
                'select-input': 'B',
                'checkbox-input': ['A', 'B'],
                'radio-input': 'B'
            },
            show: function () {
                $(this).slideDown();
            },
            hide: function (deleteElement) {
                if(confirm('Are you sure you want to delete this element?')) {
                    $(this).slideUp(deleteElement);
                }
            },
            ready: function (setIndexes) {

            }
        });

        window.outerRepeater = $('.outer-repeater').repeater({
            isFirstItemUndeletable: true,
            defaultValues: { 'text-input': 'outer-default' },
            show: function () {
                console.log('outer show');
                $(this).slideDown();
            },
            hide: function (deleteElement) {
                console.log('outer delete');
                $(this).slideUp(deleteElement);
            },
            repeaters: [{
                isFirstItemUndeletable: true,
                selector: '.inner-repeater',
                defaultValues: { 'inner-text-input': 'inner-default' },
                show: function () {
                    console.log('inner show');
                    $(this).slideDown();
                },
                hide: function (deleteElement) {
                    console.log('inner delete');
                    $(this).slideUp(deleteElement);
                }
            }]
        });
    });
    </script>
</body>
</html>
