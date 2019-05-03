"use strict";
// Class definition
var KTFlotchartsDemo = function() {

    // Private functions

    var demo1 = function() {
        var data = [];
        var totalPoints = 250;

        // random data generator for plot charts

        function getRandomData() {
            if (data.length > 0) data = data.slice(1);
            // do a random walk
            while (data.length < totalPoints) {
                var prev = data.length > 0 ? data[data.length - 1] : 50;
                var y = prev + Math.random() * 10 - 5;
                if (y < 0) y = 0;
                if (y > 100) y = 100;
                data.push(y);
            }
            // zip the generated y values with the x values
            var res = [];
            for (var i = 0; i < data.length; ++i) {
                res.push([i, data[i]]);
            }

            return res;
        }

        var d1 = [];
        for (var i = 0; i < Math.PI * 2; i += 0.25)
            d1.push([i, Math.sin(i)]);

        var d2 = [];
        for (var i = 0; i < Math.PI * 2; i += 0.25)
            d2.push([i, Math.cos(i)]);

        var d3 = [];
        for (var i = 0; i < Math.PI * 2; i += 0.1)
            d3.push([i, Math.tan(i)]);

        $.plot($("#kt_flotcharts_1"), [{
            label: "sin(x)",
            data: d1,
            lines: {
                lineWidth: 1,
            },
            shadowSize: 0,
            color: '#f6aa33'
        }, {
            label: "cos(x)",
            data: d2,
            lines: {
                lineWidth: 1,
            },
            shadowSize: 0,
            color: '#6e4ff5'
        }, {
            label: "tan(x)",
            data: d3,
            lines: {
                lineWidth: 1,
            },
            shadowSize: 0,
            color: '#fe3995'
        }], {
            series: {
                lines: {
                    show: true,
                },
                points: {
                    show: true,
                    fill: true,
                    radius: 3,
                    lineWidth: 1
                }
            },
            xaxis: {
                tickColor: "#eee",
                ticks: [0, [Math.PI / 2, "\u03c0/2"],
                    [Math.PI, "\u03c0"],
                    [Math.PI * 3 / 2, "3\u03c0/2"],
                    [Math.PI * 2, "2\u03c0"]
                ]
            },
            yaxis: {
                tickColor: "#eee",
                ticks: 10,
                min: -2,
                max: 2
            },
            grid: {
                borderColor: "#eee",
                borderWidth: 1
            }
        });
    }

    var demo2 = function() {
        function randValue() {
            return (Math.floor(Math.random() * (1 + 40 - 20))) + 20;
        }
        var pageviews = [
            [1, randValue()],
            [2, randValue()],
            [3, 2 + randValue()],
            [4, 3 + randValue()],
            [5, 5 + randValue()],
            [6, 10 + randValue()],
            [7, 15 + randValue()],
            [8, 20 + randValue()],
            [9, 25 + randValue()],
            [10, 30 + randValue()],
            [11, 35 + randValue()],
            [12, 25 + randValue()],
            [13, 15 + randValue()],
            [14, 20 + randValue()],
            [15, 45 + randValue()],
            [16, 50 + randValue()],
            [17, 65 + randValue()],
            [18, 70 + randValue()],
            [19, 85 + randValue()],
            [20, 80 + randValue()],
            [21, 75 + randValue()],
            [22, 80 + randValue()],
            [23, 75 + randValue()],
            [24, 70 + randValue()],
            [25, 65 + randValue()],
            [26, 75 + randValue()],
            [27, 80 + randValue()],
            [28, 85 + randValue()],
            [29, 90 + randValue()],
            [30, 95 + randValue()]
        ];
        var visitors = [
            [1, randValue() - 5],
            [2, randValue() - 5],
            [3, randValue() - 5],
            [4, 6 + randValue()],
            [5, 5 + randValue()],
            [6, 20 + randValue()],
            [7, 25 + randValue()],
            [8, 36 + randValue()],
            [9, 26 + randValue()],
            [10, 38 + randValue()],
            [11, 39 + randValue()],
            [12, 50 + randValue()],
            [13, 51 + randValue()],
            [14, 12 + randValue()],
            [15, 13 + randValue()],
            [16, 14 + randValue()],
            [17, 15 + randValue()],
            [18, 15 + randValue()],
            [19, 16 + randValue()],
            [20, 17 + randValue()],
            [21, 18 + randValue()],
            [22, 19 + randValue()],
            [23, 20 + randValue()],
            [24, 21 + randValue()],
            [25, 14 + randValue()],
            [26, 24 + randValue()],
            [27, 25 + randValue()],
            [28, 26 + randValue()],
            [29, 27 + randValue()],
            [30, 31 + randValue()]
        ];

        var plot = $.plot($("#kt_flotcharts_2"), [{
            data: pageviews,
            label: "Unique Visits",
            lines: {
                lineWidth: 1,
            },
            shadowSize: 0,
            color: '#fe3995'

        }, {
            data: visitors,
            label: "Page Views",
            lines: {
                lineWidth: 1,
            },
            shadowSize: 0,
            color: '#6e4ff5'
        }], {
            series: {
                lines: {
                    show: true,
                    lineWidth: 2,
                    fill: true,
                    fillColor: {
                        colors: [{
                            opacity: 0.05
                        }, {
                            opacity: 0.01
                        }]
                    }
                },
                points: {
                    show: true,
                    radius: 3,
                    lineWidth: 1
                },
                shadowSize: 2
            },
            grid: {
                hoverable: true,
                clickable: true,
                tickColor: "#eee",
                borderColor: "#eee",
                borderWidth: 1
            },
            colors: ["#d12610", "#37b7f3", "#52e136"],
            xaxis: {
                ticks: 11,
                tickDecimals: 0,
                tickColor: "#eee",
            },
            yaxis: {
                ticks: 11,
                tickDecimals: 0,
                tickColor: "#eee",
            }
        });

        function showTooltip(x, y, contents) {
            $('<div id="tooltip">' + contents + '</div>').css({
                position: 'absolute',
                display: 'none',
                top: y + 5,
                left: x + 15,
                border: '1px solid #333',
                padding: '4px',
                color: '#fff',
                'border-radius': '3px',
                'background-color': '#333',
                opacity: 0.80
            }).appendTo("body").fadeIn(200);
        }

        var previousPoint = null;
        $("#chart_2").bind("plothover", function(event, pos, item) {
            $("#x").text(pos.x.toFixed(2));
            $("#y").text(pos.y.toFixed(2));

            if (item) {
                if (previousPoint != item.dataIndex) {
                    previousPoint = item.dataIndex;

                    $("#tooltip").remove();
                    var x = item.datapoint[0].toFixed(2),
                        y = item.datapoint[1].toFixed(2);

                    showTooltip(item.pageX, item.pageY, item.series.label + " of " + x + " = " + y);
                }
            } else {
                $("#tooltip").remove();
                previousPoint = null;
            }
        });
    }

    var demo3 = function() {
        var sin = [],
            cos = [];
        for (var i = 0; i < 14; i += 0.1) {
            sin.push([i, Math.sin(i)]);
            cos.push([i, Math.cos(i)]);
        }

        var plot = $.plot($("#kt_flotcharts_3"), [{
            data: sin,
            label: "sin(x) = -0.00",
            lines: {
                lineWidth: 1,
            },
            shadowSize: 0,
            color: '#f6aa33'
        }, {
            data: cos,
            label: "cos(x) = -0.00",
            lines: {
                lineWidth: 1,
            },
            shadowSize: 0,
            color: '#6e4ff5'
        }], {
            series: {
                lines: {
                    show: true
                }
            },
            crosshair: {
                mode: "x"
            },
            grid: {
                hoverable: true,
                autoHighlight: false,
                tickColor: "#eee",
                borderColor: "#eee",
                borderWidth: 1
            },
            yaxis: {
                min: -1.2,
                max: 1.2
            }
        });

        var legends = $("#kt_flotcharts_3 .legendLabel");
        legends.each(function() {
            // fix the widths so they don't jump around
            $(this).css('width', $(this).width());
        });

        var updateLegendTimeout = null;
        var latestPosition = null;

        function updateLegend() {
            updateLegendTimeout = null;

            var pos = latestPosition;

            var axes = plot.getAxes();
            if (pos.x < axes.xaxis.min || pos.x > axes.xaxis.max || pos.y < axes.yaxis.min || pos.y > axes.yaxis.max) return;

            var i, j, dataset = plot.getData();
            for (var i = 0; i < dataset.length; ++i) {
                var series = dataset[i];

                // find the nearest points, x-wise
                for (j = 0; j < series.data.length; ++j)
                    if (series.data[j][0] > pos.x) break;

                // now interpolate
                var y, p1 = series.data[j - 1],
                    p2 = series.data[j];

                if (p1 == null) y = p2[1];
                else if (p2 == null) y = p1[1];
                else y = p1[1] + (p2[1] - p1[1]) * (pos.x - p1[0]) / (p2[0] - p1[0]);

                legends.eq(i).text(series.label.replace(/=.*/, "= " + y.toFixed(2)));
            }
        }

        $("#kt_flotcharts_3").bind("plothover", function(event, pos, item) {
            latestPosition = pos;
            if (!updateLegendTimeout) updateLegendTimeout = setTimeout(updateLegend, 50);
        });
    }

    var demo4 = function() {

        var data = [];
        var totalPoints = 250;

        // random data generator for plot charts

        function getRandomData() {
            if (data.length > 0) data = data.slice(1);
            // do a random walk
            while (data.length < totalPoints) {
                var prev = data.length > 0 ? data[data.length - 1] : 50;
                var y = prev + Math.random() * 10 - 5;
                if (y < 0) y = 0;
                if (y > 100) y = 100;
                data.push(y);
            }
            // zip the generated y values with the x values
            var res = [];
            for (var i = 0; i < data.length; ++i) {
                res.push([i, data[i]]);
            }

            return res;
        }

        //server load
        var options = {
            series: {
                shadowSize: 1
            },
            lines: {
                show: true,
                lineWidth: 0.5,
                fill: true,
                fillColor: {
                    colors: [{
                        opacity: 0.1
                    }, {
                        opacity: 1
                    }]
                }
            },
            yaxis: {
                min: 0,
                max: 100,
                tickColor: "#eee",
                tickFormatter: function(v) {
                    return v + "%";
                }
            },
            xaxis: {
                show: false,
            },
            colors: ["#6e4ff5"],
            grid: {
                tickColor: "#eee",
                borderWidth: 0,
            }
        };

        var updateInterval = 30;
        var plot = $.plot($("#kt_flotcharts_4"), [getRandomData()], options);

        function update() {
            plot.setData([getRandomData()]);
            plot.draw();
            setTimeout(update, updateInterval);
        }

        update();
    }

    var demo5 = function() {
        var d1 = [];
        for (var i = 0; i <= 10; i += 1)
            d1.push([i, parseInt(Math.random() * 30)]);

        var d2 = [];
        for (var i = 0; i <= 10; i += 1)
            d2.push([i, parseInt(Math.random() * 30)]);

        var d3 = [];
        for (var i = 0; i <= 10; i += 1)
            d3.push([i, parseInt(Math.random() * 30)]);

        var stack = 0,
            bars = true,
            lines = false,
            steps = false;

        function plotWithOptions() {
            $.plot($("#kt_flotcharts_5"),

                [{
                    label: "sales",
                    data: d1,
                    lines: {
                        lineWidth: 1,
                    },
                    shadowSize: 0,
                    color: '#2abe81'
                }, {
                    label: "tax",
                    data: d2,
                    lines: {
                        lineWidth: 1,
                    },
                    shadowSize: 0,
                    color: '#6e4ff5'
                }, {
                    label: "profit",
                    data: d3,
                    lines: {
                        lineWidth: 1,
                    },
                    shadowSize: 0,
                    color: '#fe3995'
                }]

                , {
                    series: {
                        stack: stack,
                        lines: {
                            show: lines,
                            fill: true,
                            steps: steps,
                            lineWidth: 0, // in pixels
                        },
                        bars: {
                            show: bars,
                            barWidth: 0.5,
                            lineWidth: 0, // in pixels
                            shadowSize: 0,
                            align: 'center',
                            fill: 0.5
                        }
                    },
                    grid: {
                        tickColor: "#eee",
                        borderColor: "#eee",
                        borderWidth: 1
                    }
                }
            );
        }

        $(".stackControls input").click(function(e) {
            e.preventDefault();
            stack = $(this).val() == "With stacking" ? true : null;
            plotWithOptions();
        });

        $(".graphControls input").click(function(e) {
            e.preventDefault();
            bars = $(this).val().indexOf("Bars") != -1;
            lines = $(this).val().indexOf("Lines") != -1;
            steps = $(this).val().indexOf("steps") != -1;
            plotWithOptions();
        });

        plotWithOptions();
    }

    var demo6 = function() {
        // bar chart:
        var data = GenerateSeries(0);

        function GenerateSeries(added) {
            var data = [];
            var start = 100 + added;
            var end = 200 + added;

            for (var i = 1; i <= 20; i++) {
                var d = Math.floor(Math.random() * (end - start + 1) + start);
                data.push([i, d]);
                start++;
                end++;
            }

            return data;
        }

        var options = {
            series: {
                bars: {
                    show: true
                }
            },
            bars: {
                barWidth: 0.8,
                lineWidth: 0, // in pixels
                shadowSize: 0,
                align: 'left',
                fill: 1
            },

            grid: {
                tickColor: "#eee",
                borderColor: "#eee",
                borderWidth: 1
            }
        };

        $.plot($("#kt_flotcharts_6"), [{
            data: data,
            lines: {
                lineWidth: 1,
            },
            shadowSize: 0,
            color: '#6e4ff5'
        }], options);
    }

    var demo7 = function() {
        // horizontal bar chart:

        var data1 = [
            [10, 10],
            [20, 20],
            [30, 30],
            [40, 40],
            [50, 50]
        ];

        var options = {
            series: {
                bars: {
                    show: true
                }
            },
            bars: {
                horizontal: true,
                barWidth: 6,
                lineWidth: 0, // in pixels
                shadowSize: 0,
                align: 'left',
                fill: 1
            },
            grid: {
                tickColor: "#eee",
                borderColor: "#eee",
                borderWidth: 1
            }
        };

        $.plot($("#kt_flotcharts_7"), [{
            data: data1,
            color: '#fe3995'
        }], options);
    }

    var demo8 = function() {
        var data = [];
            var series = Math.floor(Math.random() * 10) + 1;
            series = series < 5 ? 5 : series;

            for (var i = 0; i < series; i++) {
                data[i] = {
                    label: "Series" + (i + 1),
                    data: Math.floor(Math.random() * 100) + 1
                };
            }

            $.plot($("#kt_flotcharts_8"), data, {
                    series: {
                        pie: {
                            show: true
                        }
                    },
                    colors: ['#fe3995', '#f6aa33', '#6e4ff5', '#2abe81', '#c7d2e7']
                });
    }

    var demo9 = function() {
         var data = [];
            var series = Math.floor(Math.random() * 10) + 1;
            series = series < 5 ? 5 : series;

            for (var i = 0; i < series; i++) {
                data[i] = {
                    label: "Series" + (i + 1),
                    data: Math.floor(Math.random() * 100) + 1
                };
            }

            $.plot($("#kt_flotcharts_9"), data, {
                    series: {
                        pie: {
                            show: true
                        }
                    },
                    legend: {
                        show: false
                    },
                    colors: ['#fe3995', '#f6aa33', '#6e4ff5', '#2abe81', '#c7d2e7']
                });
    }

    var demo10 = function() {
         var data = [];
            var series = Math.floor(Math.random() * 10) + 1;
            series = series < 5 ? 5 : series;

            for (var i = 0; i < series; i++) {
                data[i] = {
                    label: "Series" + (i + 1),
                    data: Math.floor(Math.random() * 100) + 1
                };
            }

             $.plot($("#kt_flotcharts_10"), data, {
                    series: {
                        pie: {
                            show: true,
                            radius: 1,
                            label: {
                                show: true,
                                radius: 1,
                                formatter: function(label, series) {
                                    return '<div style="font-size:8pt;text-align:center;padding:2px;color:white;">' + label + '<br/>' + Math.round(series.percent) + '%</div>';
                                },
                                background: {
                                    opacity: 0.8
                                }
                            }
                        }
                    },
                    legend: {
                        show: false
                    },
                    colors: ['#fe3995', '#f6aa33', '#6e4ff5', '#2abe81', '#c7d2e7']
                });
    }

    var demo11 = function() {
         var data = [];
            var series = Math.floor(Math.random() * 10) + 1;
            series = series < 5 ? 5 : series;

            for (var i = 0; i < series; i++) {
                data[i] = {
                    label: "Series" + (i + 1),
                    data: Math.floor(Math.random() * 100) + 1
                };
            }

             $.plot($("#kt_flotcharts_11"), data, {
                    series: {
                        pie: {
                            show: true,
                            radius: 1,
                            label: {
                                show: true,
                                radius: 1,
                                formatter: function(label, series) {
                                    return '<div style="font-size:8pt;text-align:center;padding:2px;color:white;">' + label + '<br/>' + Math.round(series.percent) + '%</div>';
                                },
                                background: {
                                    opacity: 0.8
                                }
                            }
                        }
                    },
                    legend: {
                        show: false
                    },
                    colors: ['#fe3995', '#f6aa33', '#6e4ff5', '#2abe81', '#c7d2e7']
                });
    }


    return {
        // public functions
        init: function() {
            // default charts
            demo1();
            demo2();
            demo3();
            demo4();
            demo5();
            demo6();
            demo7();

            // pie charts
            demo8();
            demo9();
            demo10();
            demo11();
        }
    };
}();

jQuery(document).ready(function() {
    KTFlotchartsDemo.init();
});