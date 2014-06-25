$(function() {
    $("#header").css("font-size", "30px");

    $('#msg').after("<p class='sample'>サンプル2です。</p>");

    $('.sample').hover(
        function() { $('.sample').css("background-color", "gray");  },
        function() { $('.sample').css("background-color", "white"); }
    );

    $('#footer').text("Copyright 2013");
});
