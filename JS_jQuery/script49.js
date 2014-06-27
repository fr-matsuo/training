$(function() {
    //課題2
    $("#header").css("font-size", "30px");

    $('#msg').after("<p class='sample'>サンプル2です。</p>");

    $('.sample').hover(
        function() { $('.sample').css("background-color", "gray");  },
        function() { $('.sample').css("background-color", "white"); }
    );

    $('#footer').text("Copyright 2013");
    
    //課題3
    $('#msg').on("click", function() {
        $('#msg').text("テストです。");
    });
});
