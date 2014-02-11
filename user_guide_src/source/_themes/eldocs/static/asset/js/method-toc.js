/* modified from http://stackoverflow.com/a/12459801 */
$(function (){
    var createList = function(selector){

        var ul = $('<ul>');
        var selected = $(selector);

        if (selected.length === 0){
            return;
        }

        selected.clone().each(function (i,e){

            var p = $(e).children('.descclassname');
            var n = $(e).children('.descname');
            var l = $(e).children('.headerlink');

            var a = $('<a>');
            a.attr('href',l.attr('href')).attr('title', 'jump to ' + n + '()');

            a.append(p).append(n);

            var entry = $('<li>').append(a);
            ul.append(entry);
        });
        return ul;
    }

    var c = $('<div>');

    var ul0 = c.clone().append($('.submodule-index'))

    customIndex = $('.custom-index');
    customIndex.empty();
    customIndex.append(ul0);

    var l = createList('dl.method > dt, dl.function > dt');
    if (l) {
        var ul = c.clone()
            .append('<h3>Methods / Functions</h3>')
            .append(l);
    }
    customIndex.append(ul);
});