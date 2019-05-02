"use strict";
// Class definition
var KTNoUISliderDemos = function() {

    // Private functions

     
    var demo1 = function() {
        // init slider
        var slider = document.getElementById('kt_nouislider_1');

        noUiSlider.create(slider, {
            start: [ 0 ],
            step: 2,
            range: {
                'min': [ 0 ],
                'max': [ 10 ]
            },
            format: wNumb({
                decimals: 0 
            })
        });

        // init slider input
        var sliderInput = document.getElementById('kt_nouislider_1_input');

        slider.noUiSlider.on('update', function( values, handle ) {
            sliderInput.value = values[handle];
        });

        sliderInput.addEventListener('change', function(){
            slider.noUiSlider.set(this.value);
        });
    }

    var demo2 = function() {
        // init slider
        var slider = document.getElementById('kt_nouislider_2');

        noUiSlider.create(slider, {
            start: [ 20000 ],
            connect: [true, false],
            step: 1000,
            range: {
                'min': [ 20000 ],
                'max': [ 80000 ]
            },
            format: wNumb({
                decimals: 3,
                thousand: '.',
                postfix: ' (US $)',
            })
        });

        // init slider input
        var sliderInput = document.getElementById('kt_nouislider_2_input');

        slider.noUiSlider.on('update', function( values, handle ) {
            sliderInput.value = values[handle];
        });

        sliderInput.addEventListener('change', function(){
            slider.noUiSlider.set(this.value);
        });
    }

    var demo3 = function() {
        // init slider
        var slider = document.getElementById('kt_nouislider_3');

        noUiSlider.create(slider, {
            start: [20, 80],
            connect: true,
            direction: 'rtl',
            tooltips: [true, wNumb({ decimals: 1 })],
            range: {
                'min': [0],
                '10%': [10, 10],
                '50%': [80, 50],
                '80%': 150,
                'max': 200
            }
        });
       

        // init slider input
        var sliderInput0 = document.getElementById('kt_nouislider_3_input');
        var sliderInput1 = document.getElementById('kt_nouislider_3.1_input');
        var sliderInputs = [sliderInput1, sliderInput0];        

        slider.noUiSlider.on('update', function( values, handle ) {
            sliderInputs[handle].value = values[handle];
        });
    }

    var demo4 = function() {

       var slider = document.getElementById('kt_nouislider_input_select');

        // Append the option elements
        for ( var i = -20; i <= 40; i++ ){

            var option = document.createElement("option");
                option.text = i;
                option.value = i;

            slider.appendChild(option);
        }

        // init slider
        var html5Slider = document.getElementById('kt_nouislider_4');

        noUiSlider.create(html5Slider, {
            start: [ 10, 30 ],
            connect: true,
            range: {
                'min': -20,
                'max': 40
            }
        });

        // init slider input
        var inputNumber = document.getElementById('kt_nouislider_input_number');

        html5Slider.noUiSlider.on('update', function( values, handle ) {

            var value = values[handle];

            if ( handle ) {
                inputNumber.value = value;
            } else {
                slider.value = Math.round(value);
            }
        });

        slider.addEventListener('change', function(){
            html5Slider.noUiSlider.set([this.value, null]);
        });

        inputNumber.addEventListener('change', function(){
            html5Slider.noUiSlider.set([null, this.value]);
        });
    }
 
    var demo5 = function() {
        // init slider
        var slider = document.getElementById('kt_nouislider_5');

        noUiSlider.create(slider, {
            start: 20,
            range: {
                min: 0,
                max: 100
            },
            pips: {
                mode: 'values',
                values: [20, 80],
                density: 4
            }
        });

        var sliderInput = document.getElementById('kt_nouislider_5_input');

        slider.noUiSlider.on('update', function( values, handle ) {
            sliderInput.value = values[handle];
        });

        sliderInput.addEventListener('change', function(){
            slider.noUiSlider.set(this.value);
        });

        slider.noUiSlider.on('change', function ( values, handle ) {
            if ( values[handle] < 20 ) {
                slider.noUiSlider.set(20);
            } else if ( values[handle] > 80 ) {
                slider.noUiSlider.set(80);
            }
        });
    }

    var demo6 = function() {
        // init slider             

        var verticalSlider = document.getElementById('kt_nouislider_6');

        noUiSlider.create(verticalSlider, {
            start: 40,
            orientation: 'vertical',
            range: {
                'min': 0,
                'max': 100
            }
        }); 

        // init slider input
        var sliderInput = document.getElementById('kt_nouislider_6_input');

        verticalSlider.noUiSlider.on('update', function( values, handle ) {
            sliderInput.value = values[handle];
        });

        sliderInput.addEventListener('change', function(){
            verticalSlider.noUiSlider.set(this.value);
        });      
    }    

    // Modal demo

    var modaldemo1 = function() {
        var slider = document.getElementById('kt_nouislider_modal1');

        noUiSlider.create(slider, {
            start: [ 0 ],
            step: 2,
            range: {
                'min': [ 0 ],
                'max': [ 10 ]
            },
            format: wNumb({
                decimals: 0 
            })
        });

        // init slider input
        var sliderInput = document.getElementById('kt_nouislider_modal1_input');

        slider.noUiSlider.on('update', function( values, handle ) {
            sliderInput.value = values[handle];
        });

        sliderInput.addEventListener('change', function(){
            slider.noUiSlider.set(this.value);
        });
    }

    var modaldemo2 = function() {
        var slider = document.getElementById('kt_nouislider_modal2');

        noUiSlider.create(slider, {
            start: [ 20000 ],
            connect: [true, false],
            step: 1000,
            range: {
                'min': [ 20000 ],
                'max': [ 80000 ]
            },
            format: wNumb({
                decimals: 3,
                thousand: '.',
                postfix: ' (US $)',
            })
        });

        // init slider input
        var sliderInput = document.getElementById('kt_nouislider_modal2_input');

        slider.noUiSlider.on('update', function( values, handle ) {
            sliderInput.value = values[handle];
        });

        sliderInput.addEventListener('change', function(){
            slider.noUiSlider.set(this.value);
        });
    }

    var modaldemo3 = function() {
        var slider = document.getElementById('kt_nouislider_modal3');

        noUiSlider.create(slider, {
            start: [20, 80],
            connect: true,
            direction: 'rtl',
            tooltips: [true, wNumb({ decimals: 1 })],
            range: {
                'min': [0],
                '10%': [10, 10],
                '50%': [80, 50],
                '80%': 150,
                'max': 200
            }
        });
       

        // init slider input
        var sliderInput0 = document.getElementById('kt_nouislider_modal1.1_input');
        var sliderInput1 = document.getElementById('kt_nouislider_modal1.2_input');
        var sliderInputs = [sliderInput1, sliderInput0];        

        slider.noUiSlider.on('update', function( values, handle ) {
            sliderInputs[handle].value = values[handle];
        });
    }
    return {
        // public functions
        init: function() {
            demo1();
            demo2();
            demo3();  
            demo4(); 
            demo5();  
            demo6(); 
            modaldemo1();
            modaldemo2();
            modaldemo3();                           
        }
    };
}();

jQuery(document).ready(function() {
    KTNoUISliderDemos.init();
});