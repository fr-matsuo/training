$(function() {
    $("#more").on("click", loadJson);
    loadJson();

    function loadJson() {
        $.ajax({
            url:"data.json",
            dataType:"json"
        }).done(function(data, status, xhr){
            addData(data);
        }).fail(function(xhr, status, error){
            alert("error");
        });
    }

    function addData(data) {
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
    }
});
