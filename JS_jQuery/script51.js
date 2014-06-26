$(function(){
    $.getJSON("data.JSON", function(data){
        var length = data.language.length;
        var mat    = $("#languageMat");
        
        for (var i = 0; i < length; i++) {
            var mat_col =
                "<tr><td>"
                    + data.language[i].id   +
                "</td><td>"
                    + data.language[i].name + 
                "</td><td>"
                    + data.language[i].kana + 
                "</td></tr>";
            mat.append(mat_col);
        }
        
    });
});
