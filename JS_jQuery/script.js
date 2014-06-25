$(function () {
    $("#header").css("font-size", "30px");

    var sample = "<p class='sample'>サンプル2です。</p>";
    $('#msg').after(sample);

    var after = "Copyright 2013";
    $('#footer').text(after);
})(jQuery);
