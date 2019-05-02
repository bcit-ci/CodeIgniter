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
    <!-- @include repeater.html -->
    <h2>Nested</h2>
    <!-- @include nested-repeater.html -->
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
