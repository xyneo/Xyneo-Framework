var xyneo_panel_active = 0;

$(document).ready(function(){
    $("#xyneo_panel_logo").mouseenter(function(){
        xyneo_panel_active=1;
        var document_root = $("#xyneo_jsurl").val();
        $("#the_xyneo_admin_logo").attr("src",document_root+"xyneo/xyneo_panel/public/img/admin_logo_hover.png")
        $("#xyneo_panel_open_info").show();
        $("#xyneo_panel_link").animate({width:'120px'},200,function(){
            $(this).find("a").fadeIn(200);
        });
    });
    
    $("#xyneo_panel_logo").mouseleave(function(){
        xyneo_panel_active=0;
        setTimeout("closeXyneoPanelLink()",1000);
    });
    
    $("#xyneo_panel_open_info").find('div').mouseenter(function(){
        xyneo_panel_active=1;
    });
    $("#xyneo_panel_open_info").find('div').mouseleave(function(){
        xyneo_panel_active=0;
        setTimeout("closeXyneoPanelLink()",600);
    });
    
});

function closeXyneoPanelLink(){
    if(xyneo_panel_active==0){
        var document_root = $("#xyneo_jsurl").val();
        $("#the_xyneo_admin_logo").attr("src",document_root+"xyneo/xyneo_panel/public/img/admin_logo.png");
        $("#xyneo_panel_link").find("a").hide();
        $("#xyneo_panel_link").animate({width:'0px'},200,function(){
            $("#xyneo_panel_open_info").hide();
        });
    }
}
