;
(function (win, doc, $, undefined) {
    var conf = {
        'name': 'RongCloudWebSDK',
        'isPCBrowser': false,
        'keyboardHeight': 0,
        'winWidth': 0,
        'winHeight': 0,
        'statuHeight': 0,
        'RongIMVoice': false
    };
    conf.pc = {
        'MinWidth': 960,
        'FooterMinHeight': 60,
        'Mt': $(".left").css('margin-top'),
        'Mb': 20,
    };
    conf.scroll = {
        'cursorcolor': "#78d0f4",
        'cursoropacitymax': 1,
        'touchbehavior': true,
        'cursorwidth': "5px",
        'cursorborder': "0",
        'cursorborderradius': "5px",
    };
    var lib = {};
    lib.clone = function (obj) {
        var that = this;
        if (typeof(obj) != 'object') return obj;
        if (obj == null) return obj;
        var newObj = {};
        for (var i in obj) {
            newObj[i] = that.clone(obj[i]);
        }
        return newObj;
    };
    lib.delay = function (d) {
        for (var t = Date.now(); Date.now() - t <= d;);
    };

    var self = {};
    self.conf = conf;
    self.isPCBrowser = function () {
        conf.winWidth = $(window).width();
        return conf.winWidth > conf.pc.MinWidth;
    };

    self.setBoxHeight = function (heigh) {
        var winHeight = $(window).height();
        if (heigh && typeof(heigh) == "number") {
            winHeight = heigh;
        }
        ;

        var otherHeight = 0;
        if (self.isPCBrowser()) {
            otherHeight = conf.pc.FooterMinHeight + conf.pc.Mb + 25;  //上下空隙间距
            $(".bro_notice").show();
            $(".left").show();
            $(".right_box").show();
        } else {
            if ($(".right_box").is(":visible") == true) {
                $(".left").hide();
            }
        }
        var intBoxHeight = winHeight - otherHeight;
        var intBoxMinHeight = $(".left").css('min-height');
        intBoxMinHeight = parseInt(intBoxMinHeight);
        if (intBoxHeight > intBoxMinHeight) {
            $(".left").height(intBoxHeight);
            //$(".right_box").height(intBoxHeight);
        } else {
            $(".left").height(intBoxMinHeight);
            //$(".right_box").height(intBoxMinHeight);
        }
        //$(".right").height($(".right_box").height());
        $(".right").height($(".left").height());
        self.setListHeight();
        self.setDialogBoxHeight();
    };
    self.setListHeight = function () {
        var intListHeight = 0;
        var intHeaderHeight = $(".dialog_header").height();
        var intOperHeight = 0;
        var boxMb = 0;
        if ($(".listOperatorContent") && $(".listOperatorContent").is(":visible")) {
            intOperHeight = $(".listOperatorContent").height();
            boxMb = conf.pc.Mb;
        }
        var intBoxHeight = $(".left").height();
        intListHeight = intBoxHeight - intHeaderHeight - intOperHeight - boxMb;
        $(".list").height(intListHeight);
    };
    self.setDialogBoxHeight = function () {
        var intBoxHeight = $(".right").height();
        var intMsgBoxHeight = 0;
        if (self.isPCBrowser()) {
            intMsgBoxHeight = $(".msg_box").outerHeight();
        } else {
            intMsgBoxHeight = 44;
        }
        var otherHeight = intMsgBoxHeight + $(".dialog_box_header").outerHeight();
        if ($(".pagetion_list") && $(".pagetion_list").is(":visible") == true) {
            otherHeight = $(".pagetion_list").height() + conf.pc.Mb;
        }
        var msgBoxHeight = intBoxHeight - otherHeight;
        $(".dialog_box").css('height', msgBoxHeight);
    };
    /**
     * 定位单条未读消息数
     */
    self.locateNum = function (index, obj) {
        var padding = 3;
        var intWidth = $(obj).width();
        var val = $(obj).html();
        if (val && val > 0) {
            $(obj).css('display', 'inline-block');
            $(obj).css('padding', padding);
            $(obj).css('margin-left', -intWidth / 2 - 6);
        } else {
            $(obj).hide();
        }
    };
    self.locateMsgStatu = function (index, obj) {
        var prevHeight = $(obj).prev("div").height();
        var marTop = (prevHeight - $(obj).height()) / 2 + 1.5 * $(obj).height();
        $(obj).css("margin-top", -marTop);
    };

    self.back = function () {
        if ($(".right_box").is(":visible")) {
            $(".right_box").hide();
            $(".left").show();
            $(".listConversation").show();
            if (!$(".listAddr").is(":visible")) {
                $(".listConversation").hide();
                $(".listConversation").show();
            } else {
                $(".listAddr").hide();
                $(".listAddr").show();
            }
        } else {
            $(".listAddr").hide();
            $(".listConversation").show();
            $(".logOut").show();
            $(".addrBtnBack").hide();
        }
    };
    self.createOrientationChangeProxy = function (fn) {
        return function () {
            if ($(".RongIMexpressionWrap").is(":visible")) {
                $("#RongIMexpression").trigger('click');
            }
            ;
            $(".textarea").blur();
            clearTimeout(fn.orientationChangeTimer);
            var args = Array.prototype.slice.call(arguments, 0);
            fn.orientationChangeTimer = setTimeout(function () {
                var ori = window.orientation;
                if (ori != fn.lastOrientation) {
                    fn.apply(null, args);
                }
                fn.lastOrientation = ori;
            }, 800);
        };
    };
    self.changeView = function () {
        setTimeout(function () {
                var height = 0;
                $(".textarea").focus();
                window.scrollTo(0, 0);
                if (window.orientation == 180 || window.orientation == 0) {
                    height = self.winHeight - conf.statuHeight;
                    //$("body").append('<link href="//css/main.css" rel="stylesheet">');
                } else {
                    height = self.winWidth - conf.statuHeight;
                    //$("body").append('<link href="//css/main.css" rel="stylesheet">');
                }
                self.setBoxHeight()
            },
            500);
    };
    self.setStatuHeight = function () {
        var winBodyHeight = $(window).height();
        var height = 0;
        if (window.orientation == 180 || window.orientation == 0) {
            height = window.screen.height - winBodyHeight;
        } else {
            height = window.screen.width - winBodyHeight;
        }
        conf.statuHeight = height;
    };
    self.bind = function () {
        self.winHeight = window.screen.height;
        self.winWidth = window.screen.width;
        self.setStatuHeight();
        if ('onorientationchange' in window) {
            window.addEventListener("orientationchange", self.createOrientationChangeProxy(function () {
                if (window.orientation == 0 || window.orientation == 180 || window.orientation == 90 || window.orientation == -90) {
                    self.changeView();
                }
            }), false);
        } else {
            //$(window).bind("resize", self.setBoxHeight);
        }

        $(".conversation_msg_num").on('change', self.locateNum);
        $(".status").on('change', self.locateMsgStatu);
        $(".btnBack").on('click', self.back);
        $(".setting").bind('click', function () {
            $(".settingView").toggle();
        });
        $(".conversationBtn").click(function (event) {
            $(".phone_dialog_header>.logOut").show();
            $(".addrBtnBack").hide();
            $(".conversationBtn").addClass('selected');
            $(".addrBtn").removeClass('selected');
            $(".list").hide();
            $(".listConversation").show();
        });
        $(".addrBtn").click(function (event) {
            $(".phone_dialog_header>.logOut").hide();
            $(".addrBtnBack").show();
            $(".addrBtn").addClass('selected');
            $(".conversationBtn").removeClass('selected');
            $(".list").hide();
            $(".listAddr").show();
        });
        $("#RongIMexpression").bind('click', function () {
            var RongIMexpressionObj = $(".RongIMexpressionWrap");
            var intExpressHeight = RongIMexpressionObj.innerHeight();
            if (RongIMexpressionObj.is(":visible")) {
                $(".dialog_box").height($(".dialog_box").height() + intExpressHeight);
                RongIMexpressionObj.hide()
            } else {
                $(".dialog_box").height($(".dialog_box").height() - intExpressHeight);
                RongIMexpressionObj.show()
            }
           // RongIMexpressionObj.slideToggle();
        });
        $(".textarea").bind('focus', self.virtualKeyboardHeight);
//        $("body").bind('click', function() {$(this).trigger('mouseleave');alert(1111);});
        $(".list").delegate('li', 'click', function () {
            if (!self.isPCBrowser()) {
                $(".left").hide();
                $(".right_box").show();
            }
            ;
        });
        $(".dialog_box").bind('DOMNodeInserted', self.autoScroll);
        $(".dialog_box").delegate('img', 'click', function (event) {
            var url = $(this).attr('bigUrl');
            self.showImg({'img': url, 'oncomplete': self.showBigImg});
        });
        $(".dialog_box").delegate('.voice', 'click', function (event) {
            if (typeof(conf.RongIMVoice) == 'boolean' && conf.RongIMVoice) {
                RongIMClient.voice.play($(this).next().val());
            }
        });
        $("body").delegate('.light_notice_backlayer', 'click', function (event) {
            $('.light_notice_backlayer').remove();
            $(".frontlayer").remove();
        });
        $("body").delegate('.frontlayer', 'click', function (event) {
            $('.light_notice_backlayer').remove();
            $(".frontlayer").remove();
        });
        $("#mainContent").bind('keypress', function (event) {
            if (event.ctrlKey && event.keyCode == "13") {
                $('#send').trigger('click');
            }
        }).bind("click", function () {
            if ($(".RongIMexpressionWrap").is(":visible"))
                $("#RongIMexpression").trigger('click');
        });

    };
    self.showBigImg = function () {
        var html = '<div class="light_notice_backlayer"></div>';
        var frontLayer = '<div class="frontlayer"></div>';
        var winWidth = $(window).width();
        var winHeight = $(window).height();
        var style = '';
        var left = 0;
        var top = 0;
        var width = this.width;
        if (width > winWidth) {
            width = winWidth;
        } else {
            left = (winWidth - width) / 2;
        }
        if (this.height < winHeight) {
            top = (winHeight - this.height) / 2;
        }
        //style = 'left: ' + left + 'px; top: ' + top + 'px';
        this.obj.style.width = width + 'px';
        $("body").append($(html));
        var Layer = $(frontLayer);
        //var obj = this.obj.outerHTML;
        Layer.css('left', left + 'px');
        Layer.css('top', top + 'px');

        Layer.append(this.obj);
        $("body").append(Layer);
    };
    self.showImg = function (cfg) {
        var img = new Image();
        img.src = cfg.img;
        var callback = cfg.oncomplete;
        img.onload = function () {
            callback.call({"width": img.width, "height": img.height, "obj": img}, null);
        }
    };
    self.autoScroll = function () {
        var scrollHeight = $('.dialog_box')[0].scrollHeight;
        $('.dialog_box').scrollTop(scrollHeight);
    };
    self.drawExpressionWrap = function () {
        var RongIMexpressionObj = $(".RongIMexpressionWrap");
        if (win.RongIMClient) {
            var arrImgList = RongIMClient.Expression.getAllExpression(60, 0);
            if (arrImgList && arrImgList.length > 0) {
                for (var objArr in arrImgList) {
                    var imgObj = arrImgList[objArr].img;
                    imgObj.setAttribute("alt", arrImgList[objArr].chineseName);
                    imgObj.setAttribute("title", arrImgList[objArr].chineseName);
//                    imgObj.alt = arrImgList[objArr].chineseName;
                    var newSpan = $('<span class="RongIMexpression_' + arrImgList[objArr].englishName + '"></span>');
                    newSpan.append(imgObj);
                    RongIMexpressionObj.append(newSpan);
                }
            }
            $(".RongIMexpressionWrap>span").bind('click', function (event) {
                $(".textarea")[0].value+="["+$(this).children(".RC_Expression").attr("alt")+"]";
//                $(".textarea").append($(this).clone());
            });
        }
        ;
    };
    self.virtualKeyboardHeight = function () {
        //var sx = $(window).scrollLeft(), sy = $(window).scrollTop();
        //var naturalHeight = window.innerHeight;
        //window.scrollTo(sx, document.body.scrollHeight);
        //var keyboardHeight = naturalHeight - window.innerHeight;
        //window.scrollTo(sx, sy);
        //conf.keyboardHeight = keyboardHeight;
        //return keyboardHeight;
    };
    self.getWinHeight = function (event) {
        if (event && event.type == 'orientationchange') {
            return conf.winWidth;
        } else {
            return $(window).height();
        }
    }
    self.init = function () {
        conf.winWidth = $(window).width();
        conf.winHeight = $(window).height();
        self.setBoxHeight();
        self.bind();

        self.drawExpressionWrap();

        var newConf = conf.scroll;
        newConf = lib.clone(newConf);
        var listAddr = lib.clone(conf.scroll);
        var newConf1 = lib.clone(conf.scroll);
        $(".listConversation").niceScroll(conf.scroll);
        $(".listAddr").niceScroll(listAddr);
        $('.dialog_box').niceScroll(newConf);
        $(".RongIMexpressionWrap").niceScroll(newConf1);
        $(".conversation_msg_num").each(self.locateNum);
        $(".status").each(self.locateMsgStatu);
        self.autoScroll();
        if (typeof(conf.RongIMVoice) == 'boolean' && !conf.RongIMVoice) {
            conf.RongIMVoice = RongIMClient.voice.init();
        }
    };
    win[conf.name] = self;
})(window, document, window.jQuery);


$().ready(function ($) {
    $(document).delegate(".user_img img,.owner_image img","error",function () {
        console.log(1111);
        this.setAttribute("src", "/images/user.png");
    });
    if (window.RongCloudWebSDK) {
        RongCloudWebSDK.init();
    }
    ;
    function stopScrolling(touchEvent) {
        touchEvent.preventDefault();
    }

    $("body").bind("touchmove", stopScrolling);
    var docElm = document.documentElement;
    if (docElm.requestFullscreen) {
        docElm.requestFullscreen();
    }
    if (docElm.fullscreenEnabled) {
        docElem.exitFullscreen();
    }

});
