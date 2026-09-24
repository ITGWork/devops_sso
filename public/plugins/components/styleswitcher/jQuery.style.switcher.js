// Theme color settings
var url_root = $('#theme').data('url');

function store(name, val) {

    $.get(url_root + '/user/savetheme/' + val, function(data) {
        //console.log('success');
    });

    if (typeof(Storage) !== "undefined") {
        localStorage.setItem(name, val);
    } else {
        window.alert('Please use a modern browser to properly view this template!');
    }
}
$(document).ready(function() {

    $('body').addClass($('ul.layouts li.active a').data('layout'));

    $("*[data-theme]").click(function(e) {
        e.preventDefault();
        var currentStyle = $(this).attr('data-theme');
        store('theme', currentStyle);
        $('#theme').attr("href", url_root + "/css/colors/" + currentStyle + ".css");

        $('#themecolors li a').removeClass('working');
        $(this).addClass('working');

    });

});
