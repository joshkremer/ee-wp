(function($) {

    $("#rebuild_json_file").on('click', function(){



        function build_json_file(){
            console.log("Starting JSON feed creation process...");
            var data_file_path = $("#rebuild_json_file").attr("data_php_path");

            $.ajax({
                type: "POST",
                url: "/wp-admin/admin-ajax.php",
                data: {
                    action: 'build_master_json_file',
                    message_id: $('#rebuild_json_file').val()
                },

                beforeSend: function() {
                    $("#message").html("<p>Recreating JSON feeds ...</p>");
                    $('#message').show();
                    $('#loading').show();
                    $("#loading").html('<img src="/wp-content/plugins/klaviyo-wp-data-feed/spinner.gif" height="30" width="30" alt="Wait" />');
                },

                complete: function(){
                    $("#message").html('<p>Feed recreation complete. Please <a href="javascript:window.location.href=window.location.href">refresh the page</a> to view recent changes</p>');
                    $('#loading').hide();
                },
                success: function(output){
                    console.log(output);
                }
            })
        };

        build_json_file();


    });

})(jQuery);