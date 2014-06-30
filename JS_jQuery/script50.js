$(function(){
    $("form").submit(function(){
        var input = $("input").val();
        showAlert(input);
    });
});

function showAlert(input) {
    if (input == "") {
        alert("入力してください");
    } else if ($.isNumeric(input)) {
        alert("数字です");
    } else {
        alert("数字以外です");
    }
}
