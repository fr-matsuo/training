$(function(){
   var json_data = undefined;

    $.getJSON("data.JSON", function(data){
        json_data = data;
        addData();
    });

    $("#more").on("click", addData);

    function addData() {
        var length = json_data.language.length;
        var mat    = $("#languageMat");
        
        for (var i = 0; i < length; i++) {
            var mat_col =
                "<tr><td>"
                    + json_data.language[i].id   +
                "</td><td>"
                    + json_data.language[i].name + 
                "</td><td>"
                    + json_data.language[i].kana + 
                "</td></tr>";
            mat.append(mat_col);
        }
    }
});
