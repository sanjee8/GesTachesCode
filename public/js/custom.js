$(document).ready(function() {


    $('#task_table').DataTable({
        order: [[1, 'desc']]
    });

    $('#user_table').DataTable({
        order: [[3, 'desc']]
    });

    $('#task_description').summernote();

    $('#update_task_description').summernote();

    let collabs = [];

    let account_selected = null;


    $(".account-tab-btn").on('click', function () {



        let block_waiting = $("#account-waiting");
        let block_password = $("#password-change");
        let block_infos = $("#informations-change");
        let btn_type = $(this).attr('data-tab');

       if(account_selected === btn_type) {
           block_waiting.show();
           account_selected = null;
           block_password.hide();
           block_infos.hide();


       } else {

           block_waiting.hide();
           block_password.hide();
           block_infos.hide();
           account_selected = btn_type;

           if(btn_type === "password") {

               block_password.removeAttr("hidden");
               block_password.show();
           } else if(btn_type === "informations") {

               block_infos.removeAttr("hidden");
               block_infos.show();
           }


       }



        if(account_selected !== null) {

            $(".action-btn").removeClass("action-active");
            $(this).addClass("action-active");

        } else {
            $(this).removeClass("action-active");
        }

    });



    $(".deleteUserProfile").on('click', function () {

        let user_id = $(this).attr("data-id");
        let user_name = $(this).attr("data-name");
        let user_avatar = $(this).attr("data-avatar");

        let user_isadmin = $(this).attr("data-isadmin");

        if(user_isadmin === "true") {
            $("#deleteDeniedModal").modal("show");
            return;
        }

        let link_modele = $("#profile-data-model-link").val();
        let final_link = link_modele.replace('0', user_id);

        $("#profile-data-avatar").attr('src', user_avatar);
        $("#profile-data-name").text(user_name);
        $("#profile-data-link").attr('href', final_link);

        $("#deleteUserModal").modal('show');


    });

    $(".adminUserProfile").on('click', function () {

        let user_id = $(this).attr("data-id");
        let user_name = $(this).attr("data-name");
        let user_avatar = $(this).attr("data-avatar");

        let user_isadmin = $(this).attr("data-isadmin");

        if(user_isadmin === "true") {
            let link_modele = $("#profile-data-model-link-admin-remove").val();
            let final_link = link_modele.replace('0', user_id);

            $("#profile-data-avatar-admin-remove").attr('src', user_avatar);
            $("#profile-data-name-admin-remove").text(user_name);
            $("#profile-data-link-admin-remove").attr('href', final_link);

            $("#removeAdminModal").modal("show");
            return;
        }

        let link_modele = $("#profile-data-model-link-admin").val();
        let final_link = link_modele.replace('0', user_id);

        $("#profile-data-avatar-admin").attr('src', user_avatar);
        $("#profile-data-name-admin").text(user_name);
        $("#profile-data-link-admin").attr('href', final_link);

        $("#adminUserModal").modal('show');



    });


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