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
        require_once "sidebar.php";
    ?>

    <div id="main">

        <div id="error" >

        </div>

        <h1> Dodajte kategoriju </h1>

        <input type="text" id="categoryName" placeholder="Ime kategorije"/><button id="addCategory">Dodaj</button>

        <div id="categories">
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

        deleteCategory($(this).val())

    });

    function showCategories(categories) {

        $("#categories").empty();

        for(var i = 0; i < categories.length; i++){

            var p = $("<p>" + categories[i].category + "</p>");
            var button = $("<button class='deleteCategory'>Obriši</button>");
            button.val(categories[i].id);
            p.append(button);
            $("#categories").append(p);

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