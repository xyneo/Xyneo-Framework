$(document).ready(function(){
    
    var url = $("#jsurl").attr("name");
    
    
    $.getJSON("xyneo_project.json",function(data){
        $("#p_name").val(data.name);
        $("#p_company").val(data.company_name);
        $("#p_country").val(data.country);
        $("#p_description").val(data.description);
        $("#p_email").val(data.email);
    });
    
    
    $("#top_tabs").find("li").mouseenter(function(){
        $(this).css({"color":"#0d5581"}).stop().animate({"backgroundPosition":"0px -31px"},400);
    });
    $("#top_tabs").find("li").mouseleave(function(){
        $(this).css({"color":"#737373"}).stop().animate({"backgroundPosition":"0px 0px"},400);
    });
    
    $("#ajax_form").submit(function(){
         
        var url     =   $(this).attr("action");
        var data    =   $(this).serialize();
        
        $.post(url, data, function(a){
            alert(a);
        });
        return false;
    });
    
    $("#controller_ajax_form").submit(function(){
         
        var url     =   $(this).attr("action");
        var data    =   $(this).serialize();
        
        $.post(url, data, function(a){
            alert(a);
            $("#controller_name").val("");
            $("#controller_description").val("");
        });
        return false;
    });
    
    $("#layout_ajax_form").submit(function(){
         
        var url     =   $(this).attr("action");
        var data    =   $(this).serialize();
        
        $.post(url, data, function(a){
            alert(a);
            $("#layout_name").val("");
            $("#layout_description").val("");
        });
        return false;
    });
    
    $("#helper_ajax_form").submit(function(){
         
        var url     =   $(this).attr("action");
        var data    =   $(this).serialize();
        
        $.post(url, data, function(a){
            alert(a);
            $("#helper_name").val("");
            $("#helper_description").val("");
        });
        return false;
    });
    
    //---------- MENU CONTROLS ---------------------------------------------------------------
    
    $("#pd").css('background','white');
    
    $("#top_tabs").find("li").click(function(){
        refreshLayouts();
        $(".panel_page").hide();
        var selected_page = $(this).attr("id")+"_page";
        $("#top_tabs").find("li").css({"background":"url("+url+"xyneo/xyneo_panel/public/img/xyneo_panel_menu_bg2.png)"});
        $(this).css({"background":"white"});
        $("#"+selected_page).show();
    });
    
    $("#controller_view").change(function(){
        if($("#controller_view").prop("checked"))
            $("#controller_layout").fadeIn(200);
        else
            $("#controller_layout").fadeOut(200);
        
    });
    
    $("#c").click(function(){
        window.close();
    });
    
    //---------- FUNCTIONS ---------------------------------------------------------------
    
    function refreshLayouts(){
        $.get('xyneopanel/refreshlayouts',function(result){
            $("#controller_layout").html(result);
        });
    }
    
});

