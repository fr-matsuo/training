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
        var mat      = $("#languageMat");
        var add_data = "";
    
        $.each (data.language, function(index, elm){
            add_data +=
                "<tr><td>"
                    + elm.id   +
                "</td><td>"
                    + elm.name + 
                "</td><td>"
                    + elm.kana + 
                "</td></tr>";
        });
        mat.append(add_data);
    }
});
