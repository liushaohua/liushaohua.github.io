;(function($){
    $.fn.HYQTabs = function(params){
    var s = this;$s =$(this);
    var aTab = $s.find('.active a');
    //显示当前活动的TAB
    $(aTab.attr('href').toString()).fadeIn(300);

    $s.find(".hyq-tab").click(function(event){
        //防止锚点跳转
        event.preventDefault();
        var t = $(event.target||event.srcElement); 
         
         if(t.parent().hasClass('.active')) return;
        //先隐藏当前的TAB内容
        var atv = t.parent().parent().find(".active a");

        if(atv.length>0){
            $(atv.attr("href").toString()).fadeOut(300);
        }
        

        //移除TAB样式
        t.parent().parent().find(".hyq-tab").removeClass("active");
        //加入Active样式，显示活动的PANE
        t.parent().addClass("active");
        console.log(t.prop("href"))
        $(t.attr("href").toString()).fadeIn(300);

        event.stopPropagation();
    });


     


      return  $.extend($(this),params);
    };
    $(window).load(function(){
        var tabControls = $(".hyq-tabs");
        if(tabControls.length>0){
            $.each(tabControls,function(i,e){
                $(e).HYQTabs();
            });
        }
    });
}(jQuery));