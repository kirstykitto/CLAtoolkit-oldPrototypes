/*show/hide scrapers
------------------------------*/

$("#tab1").change(function(){
            $( "select option:selected").each(function(){
                if($(this).attr("value")=="facebook"){
                    $(".facebook-set").siblings().hide();
                    $(".facebook-set").show();
                }
                if($(this).attr("value")=="twitter"){
                    $(".twitter-set").siblings().hide();
                    $(".twitter-set").show();
                }
                if($(this).attr("value")=="google"){
                    $(".google-set").siblings().hide();
                    $(".google-set").show();
                }
                if($(this).attr("value")=="linkedin"){
                    $(".linkedin-set").siblings().hide();
                    $(".linkedin-set").show();
                }
                if($(this).attr("value")=="googledrive"){
                    $(".google-drive-set").siblings().hide();
                    $(".google-drive-set").show();
                }
                if($(this).attr("value")=="youtube"){
                    $(".youtube-set").siblings().hide();
                    $(".youtube-set").show();
                }
            });
        });





