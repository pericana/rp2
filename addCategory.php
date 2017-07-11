<?php
    require_once 'user_session.php';
    if(isUserLogined() === -1){
        header("Location: login.php");
    }

    if(isUserLogined() != 1){
        header("Location: index.php");
    }

    require_once 'classes/Category.php';
    require_once 'database_connect.php';
    require_once 'database_category.php';

?>

<?php
    $title = "Dodajte kategoriju";
    require_once "header.php";
?>

<div id="center">

    <?php
        require_once "user_info.php";
    ?>

    <div id="main">

        <div id="error" >

        </div>

        <div id="categoryAndUseMain">

            <h3> Dodajte kategoriju </h3>

            <input type="text" id="categoryName" placeholder="Ime kategorije" style="width: 300px; margin-right: 20px; margin-bottom: 20px"/><button id="addCategory" >Dodaj</button>

            <h3> Postojeće kategorije </h3>

            <table id="categories">
            </table>

        </div>

    </div>

</div>

<script>

    getCategories();

    $("#addCategory").on("click", function () {

        addCategory();

    });
    
    function addCategory() {

        var categoryName = $("#categoryName").val();
        if(categoryName.length === 0){
            alert("Upisite ime kategorije");
            return;
        }

        $.ajax({
            url : "script_category.php",

            data :
                {
                    action : "add",
                    name : categoryName
                },

            type: "POST",

            dataType : "json",

            success : function(data)
            {
                $("#categoryName").val("");
                showCategories(data);
            },
            error : function (xhr, status, errorThrown){
                alert("greska");
            }

        });
    }

    $("body").on("click", "button.deleteCategory", function () {

        var success = confirm("Želite li zaista obrisati kategoriju?");

        if(success){
            deleteCategory($(this).val());
        }


    });

    function showCategories(categories) {

        $("#categories").empty();

        for(var i = 0; i < categories.length; i++){
            var tr = $("<tr></tr>");
            var td = $("<td>" + categories[i].category + "</td>");
            td.css("width", "296px");
            var button = $("<button class='deleteCategory'>Obriši</button>");
            button.val(categories[i].id);
            button.css("margin-left", "17px");
            var td2 = $("<td></td>");
            td2.append(button);
            tr.append(td);
            tr.append(td2);
            $("#categories").append(tr);

        }

    }

    function deleteCategory(id){

        $.ajax({
            url : "script_category.php",

            data :
                {
                    action : "delete",
                    id : id
                },

            type: "POST",

            dataType : "json",

            success : function(data)
            {
                showCategories(data);
            },
            error : function (xhr, status, errorThrown){
                alert("greska");
            }

        });

    }

    function getCategories(){

        $.ajax({
            url : "script_category.php",

            data :
                {
                    action : "get"
                },

            type: "GET",

            dataType : "json",

            success : function(data)
            {
                showCategories(data);
            },
            error : function (xhr, status, errorThrown){
                alert("greska");
            }

        });

    }
    
</script>