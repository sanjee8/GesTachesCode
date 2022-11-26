$(document).ready(function() {


    $('#task_table').DataTable({
        order: [[1, 'desc']]
    });

    $('#task_description').summernote();

    $('#update_task_description').summernote();

    let collabs = [];




    $('#collabFinder').on('input', function() {


        let data = [];
        let search_input = $('#collabFinder').val();
        if(search_input === "") {
            data = [];
        } else {
            $.get( "/api/users?page=1&email="+search_input, function( data_get ) {

                data = data_get["hydra:member"];



                search(data);


            });

            return;
        }


        


        search(data);
        

    });


    $(".purposeCollabs").on('click', '.collabSelect',function(e) {
        e.preventDefault();
        $('#collabFinder').dropdown('toggle');
        
        if(collabs.includes($(this).attr("data-id"))) {
            return;
        };

        collabs.push($(this).attr("data-id"));

        $('#task_collabs_input').attr("value", collabs.toString());

        $('#collabFinder').val("");
        search([]);

        $("#collabHelp").append("<button data-id='"+$(this).attr("data-id")+"' class='collabSelected removable' >" + $(this).val()+ " <i class='fa-sharp fa-solid fa-xmark'></i></button>");
    });


    $("#collabHelp").on('click', '.removable', function(e) {
        e.preventDefault();

        

        const index = collabs.indexOf($(this).attr("data-id"));

        if (index > -1) { 
          collabs.splice(index, 1);
        }

        $(this).remove();

        $('#task_collabs_input').attr("value", collabs.toString());
        console.log(collabs)
    });


});

function search(data) {

    $('.purposeCollabs').empty();


    if(data.length > 0) {
        $('.noCollab').hide();

    } else {
        $('.noCollab').show();
        $(".purposeCollabs").append("<a class=\"dropdown-item disabled noCollab\">Aucun collaborateur trouvé.</a>");
    }

    data.forEach(element => {
        $('.purposeCollabs').append("<input data-id='"+element.id+"' class='dropdown-item collabSelect' value='"+element.firstname + " " + element.lastname +"'>")

    });

}