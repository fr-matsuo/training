$(function setHeaderSize() {
    var header = $("#header");
    header.css("font-size", "30px");
});

$(function addP() {
    var sample = "<p class='sample'>サンプル2です。</p>";
    $('#msg').after(sample);
});

$(function changeCopyright() {
    var after = "Copyright 2013";
    $('#footer').text(after);
});
