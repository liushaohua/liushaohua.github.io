$(function() {
	//私有变量
	var currentConversationTargetId = 0,
		conver, _historyMessagesCache = {},
		token = _html = "",
		namelist = {
			"group001": "融云群一",
			"group002": "融云群二",
			"group003": "融云群三",
			"kefu114": "客服"
		},
		audio = document.getElementsByTagName("audio")[0],
		//是否开启声音 
		hasSound = true,
		//登陆人员信息默认值
		owner = {
			id: "",
			portrait: "static/images/user_img.jpg",
			name: "张亚涛",
			fid: "",
			fface: "",
			token: ""
		},/**/
		//初始化登陆人员信息
		list = location.search.slice(1).split('&'),
		$scope = {};
		console.log(list);
	var conversationStr = '<li targetType="{0}" targetId="{1}" targetName="{2}" targetFace="{3}"><span class="user_img"><img src={3} onerror="this.src=\'static/images/personPhoto.png\'"/><font class="conversation_msg_num {4}">{5}</font></span><span class="conversationInfo"><p style="margin-top: 10px"><font class="user_name">{6}</font><font class="date" >{7}</font></p></span></li>';
	var historyStr = '<div class="xiaoxiti {0} user"><div class="user_img"><img onerror="this.src=\'static/images/personPhoto.png\'" src="{1}"/></div><span>{2}</span><div class="msg"><div class="msgArrow"><img src="static/images/{3}"> </div><span></span>{4}</div><div messageId="{5}" class="status"></div></div><div class="slice"></div>';
	var friendListStr = '<li targetType="4" targetId="{0}" targetName="{1}"><span class="user_img"><img src="static/images/personPhoto.png"/></span> <span class="user_name">{1}</span></li>';

	if (list.length == 6) {
		$.each(list, function(i, item) {
			var val = item.split("=");
			owner[val[0]] = decodeURIComponent(val[1]);
		});
		$("img[RCTarget='owner.portrait']").attr("src", owner.portrait);
		$('span[RCTarget="owner.name"]').html(owner.name);
		console.log(owner);
		token = owner.token + '==';
		face = owner.fface;
		fid = owner.fid;
	} else {
		location.href = "login.html";
		return;
	}
	currentConversationTargetId = fid,
	//未读消息数
	$scope.totalunreadcount = 0;
	//绘画列表
	$scope.ConversationList = [];
	//好友列表
	$scope.friendsList = [];
	//会话标题
	$scope.conversationTitle = "";
	//历史消息容器
	$scope.historyMessages = $scope.historyMessages || [];
	//开启关闭声音
	$("#closeVoice").click(function() {
		hasSound = !hasSound;
		this.innerHTML = hasSound ? "开启声音" : "关闭声音";
	});
	//退出
	$(".logOut>a,#close").click(function() {
		$.get("/logout?t=" + Date.now()).done(function(data) {
			if (RongIMClient.getInstance) RongIMClient.getInstance().disconnect();
		}).always(function(){
			location.href = "login.html";
		})
	});
	/*$.get("/friends?t=" + Date.now(), function(data) {
		if (data.code == 200) {
			$scope.friendsList = data.result;
			$scope.friendsList.forEach(function(item) {
				_html += String.stringFormat(friendListStr, item.id, item.username)
			});
			$("#friendsList").html(_html)
		}
	}, "json");*/
	var friendsList =  [{"id":"6751","username":"\u674e\u6dfc","portrait":""},{"id":"6752","username":"vee","portrait":"http:\/\/www.gravatar.com\/avatar\/97d271900631dc9ea9810a1784b4407b?s=82"},{"id":"6754","username":"Ariel@iPhone","portrait":"http:\/\/www.gravatar.com\/avatar\/3f56d1043edd4b9657c465ac7a507067?s=82"},{"id":"6755","username":"Ariel@MX4","portrait":"http:\/\/www.gravatar.com\/avatar\/daf372fb788d682a987d8755377aa0f6?s=82"},{"id":"6758","username":"Z","portrait":"http:\/\/www.gravatar.com\/avatar\/977de4c12fcf7c01b0c0a4daa29cdcd5?s=82"},{"id":"6759","username":"ypx","portrait":"http:\/\/www.gravatar.com\/avatar\/62c95e14a963d0d8399f68412beb7474?s=82"},{"id":"6761","username":"osworker","portrait":"http:\/\/www.gravatar.com\/avatar\/2e880f2735c247cfa2c2ae6b484852ae?s=82"},{"id":"6762","username":"Shasta","portrait":"http:\/\/www.gravatar.com\/avatar\/8a7a7572c98e6c1c6eeb60bb90716c60?s=82"},{"id":"6764","username":"123","portrait":"http:\/\/www.gravatar.com\/avatar\/2b3779b9472cddf89283cb1a0c4b33c1?s=82"},{"id":"6766","username":"ioo","portrait":"http:\/\/www.gravatar.com\/avatar\/b81487917c4061a856f00216d74ab551?s=82&d=wavatar"},{"id":"6768","username":"fspinach","portrait":"http:\/\/www.gravatar.com\/avatar\/866c63a48518fa611f9331f5220d4f8d?s=82"},{"id":"6769","username":"xk","portrait":"http:\/\/www.gravatar.com\/avatar\/55d2142db5ed590c6fbdc78a5854884a?s=82"},{"id":"6770","username":"\u55f7\u513f","portrait":"http:\/\/www.gravatar.com\/avatar\/c5a3b616ee7fb304a8595de25fc00e67?s=82"},{"id":"6771","username":"\u7a7f\u8863\u7a7f\u8863","portrait":"http:\/\/www.gravatar.com\/avatar\/b2fb387ef252129ec6a127d673b2d3b8?s=82"},{"id":"6772","username":"eiuqohoagh","portrait":"http:\/\/www.gravatar.com\/avatar\/aaf41c0fc91216b3b383716c1b0feb51?s=82"},{"id":"6773","username":"Ivan","portrait":"http:\/\/www.gravatar.com\/avatar\/9bf4b6064c992f9981c0d873d24363c2?s=82"},{"id":"6775","username":"yangyonghui","portrait":"http:\/\/www.gravatar.com\/avatar\/bd120cd684f5b27348aeaf8c4937960c?s=82"},{"id":"6778","username":"seefar","portrait":"http:\/\/www.gravatar.com\/avatar\/ab53807ac5a59750f22ba4af3cd710f4?s=82"},{"id":"6780","username":"\u64e6\u9664","portrait":"http:\/\/www.gravatar.com\/avatar\/a25472e1ac85cf9b6a0c805957b2d572?s=82"},{"id":"6781","username":"dfdfd","portrait":"http:\/\/www.gravatar.com\/avatar\/4f50967a711fa8476aeaa43cfc9abfa7?s=82"},{"id":"6783","username":"qwe","portrait":"http:\/\/www.gravatar.com\/avatar\/0202716f34cdff5f9264f5131005aa11?s=82"},{"id":"6785","username":"123456","portrait":"http:\/\/www.gravatar.com\/avatar\/0b8fa3a759be7baceaece1dce6b6d250?s=82"},{"id":"6786","username":"chai","portrait":"http:\/\/www.gravatar.com\/avatar\/b67bc798d6d47be3b99c99dbed7e8e92?s=82"},{"id":"6787","username":"\u7d2b\u9f99","portrait":"http:\/\/www.gravatar.com\/avatar\/cd2d00c50d700a95dc1a285635b66528?s=82"},{"id":"6788","username":"\u5c0f\u9093","portrait":"http:\/\/www.gravatar.com\/avatar\/dee62ee6a394945a919f16ff772db5d4?s=82"},{"id":"6789","username":"278129","portrait":"http:\/\/www.gravatar.com\/avatar\/3d660b8557af39cd2c7b6a9a2f3818bd?s=82"},{"id":"6790","username":"hb","portrait":"http:\/\/www.gravatar.com\/avatar\/f91864a3769ed8d87a4b9f7fcba05e94?s=82"},{"id":"6791","username":"\u5f20\u4e09","portrait":"http:\/\/www.gravatar.com\/avatar\/178746ca5f3a215d054fedc8755434f9?s=82"},{"id":"6792","username":"\u5f20\u4e8c","portrait":"http:\/\/www.gravatar.com\/avatar\/6f57ef9b1a11237685451dbc4ff677d8?s=82"},{"id":"6793","username":"\u5f20\u4e00","portrait":"http:\/\/www.gravatar.com\/avatar\/96ca112479051682519f0cc0463baf5c?s=82"},{"id":"6794","username":"\u5f20\u4e8c","portrait":"http:\/\/www.gravatar.com\/avatar\/62c0e91e9045b75c847123b5b5c87e6c?s=82"},{"id":"6795","username":"\u64a9\u64a9\u64a9\u64a9","portrait":"http:\/\/www.gravatar.com\/avatar\/e0de9c3aa1463e1a2d45aa53ea106826?s=82"},{"id":"6796","username":"\u60a8","portrait":"http:\/\/www.gravatar.com\/avatar\/2968dbb4ec6d3f1539b154570bd2807b?s=82"},{"id":"6797","username":"\u5f20\u4e94","portrait":"http:\/\/www.gravatar.com\/avatar\/617c56dbce5497a9c3aa8d53c07a3f90?s=82"},{"id":"6798","username":"\u54ea\u91cc","portrait":"http:\/\/www.gravatar.com\/avatar\/8a25a2ab6019c31b5e2401b7ef3e0048?s=82"},{"id":"6799","username":"\u79bb\u5f00\u4e86","portrait":"http:\/\/www.gravatar.com\/avatar\/aa2ee100ae74fa2f031ffafc1c9da0a7?s=82"},{"id":"6801","username":"DragonJ","portrait":"http:\/\/www.gravatar.com\/avatar\/d6a2342c6a074d443bcafed35a70a5b4?s=82"},{"id":"6802","username":"test-a","portrait":"http:\/\/www.gravatar.com\/avatar\/5a1041f491a798a117a57126fffa741e?s=82"},{"id":"6803","username":"jimmy509","portrait":"http:\/\/www.gravatar.com\/avatar\/d72e3ebb85fdcb209d5d0be00c750931?s=82"},{"id":"6804","username":"jimmy509","portrait":"http:\/\/www.gravatar.com\/avatar\/effa885b75cd086447d9545e51a34d4f?s=82"},{"id":"6805","username":"e-lcq","portrait":"http:\/\/www.gravatar.com\/avatar\/ee3be8d01e780714a084fb9916250f00?s=82&d=wavatar"},{"id":"6806","username":"\u8bb0\u5f55","portrait":"http:\/\/www.gravatar.com\/avatar\/dc61d3a1033bc530a58b07a65ee7afaf?s=82"},{"id":"6810","username":"123456","portrait":"http:\/\/www.gravatar.com\/avatar\/389ba50c68306b82a4c4491c9e9282d8?s=82"},{"id":"6812","username":"Miao","portrait":"http:\/\/www.gravatar.com\/avatar\/78f12005068409eb4133d77860b1fb10?s=82"},{"id":"6813","username":"\u5434\u9e4f\u8f89","portrait":"http:\/\/www.gravatar.com\/avatar\/31b3b8d631d2f0fb9fcb0293521025ae?s=82"},{"id":"6814","username":"wgy","portrait":"http:\/\/www.gravatar.com\/avatar\/20902afd7d84181bdc2642696e309821?s=82&d=wavatar"},{"id":"6815","username":"qq123","portrait":"http:\/\/www.gravatar.com\/avatar\/2351d86237db01dd48236fbd3f066ef4?s=82"},{"id":"6816","username":"123456","portrait":"http:\/\/www.gravatar.com\/avatar\/25d5ab25791bdc4e9fd72ad43da92a6c?s=82"},{"id":"6817","username":"\u848b\u4f1f","portrait":"http:\/\/www.gravatar.com\/avatar\/098abf4e6bd7fbbca76bf1e186e00e6a?s=82"},{"id":"6818","username":"qwe1","portrait":"http:\/\/www.gravatar.com\/avatar\/d8c881b8e7309202536f41ea9026c8ca?s=82"}];
	friendsList.forEach(function(item) {
		_html += String.stringFormat(friendListStr, item.id, item.username)
	});
	$("#friendsList").html(_html)

	$("#friendsList>li,#conversationlist>li").live("click", function() {
		if(this.parentNode.id=="conversationlist"){
			$("font.conversation_msg_num",this).hide().html("");
		}
		getHistory(this.getAttribute("targetId"), this.getAttribute("targetName"), this.getAttribute("targetType"));
		face = this.getAttribute("targetFace");
	});
	$("div.listAddr li:lt(4)").click(function() {
		getHistory(this.getAttribute("targetId"), this.getAttribute("targetName"), this.getAttribute("targetType"));	
		face = this.getAttribute("targetFace");
	});
	$("#send").click(function() {
		if (!conver && !currentConversationTargetId) {
			alert("请选中需要聊天的人");
			return;
		}
		var con = $("#mainContent").val().replace(/\[.+?\]/g, function(x) {
			return RongIMClient.Expression.getEmojiObjByEnglishNameOrChineseName(x.slice(1, x.length - 1)).tag || x;
		});
		if (con == "") {
			alert("不允许发送空内容");
			return;
		}
		//发送消息
		var content = new RongIMClient.MessageContent(RongIMClient.TextMessage.obtain(myUtil.replaceSymbol(con)));
		RongIMClient.getInstance().sendMessage(4, currentConversationTargetId,content , null, {
			onSuccess: function() {
				console.log("send successfully");
			},
			onError: function(x) {
				$(".dialog_box div[messageId='" + content.getMessage().getMessageId() + "']").addClass("status_error");
				console.log(x.getValue(), x.getMessage())
			}
		});
		addhistoryMessages(content.getMessage());
		initConversationList();
		$("#mainContent").val("");
	});
	//初始化SDK
	RongIMClient.init("pwe86ga5e1ej6"); //e0x9wycfx7flq z3v5yqkbv8v30

	/*$.ajax({
		type: "get",
		url: "/token?t=" + Date.now(),
		dataType: "json"
	}).done(function(data) {
		if (data.code == 200) {
			token = data.result;
			//链接融云
			RongIMClient.connect(token.token, {
				onSuccess: function(x) {
					console.log("connected，userid＝" + x);
				},
				onError: function(c) {
					console.log("失败:" + c.getMessage())
				}
			});
		} else {
			alert("获取token失败,请重新登录");
			location.href = "login.html";
		}
	}).fail(function() {
		alert("获取token失败");
		location.href = "login.html";
	});*/
    RongIMClient.connect(token, {
        onSuccess: function(x) {
            console.log("connected，userid＝" + x);
        },
        onError: function(c) {
            console.log("失败:" + c.getMessage())
        }
    });

	//链接状态监听器
	RongIMClient.setConnectionStatusListener({
		onChanged: function(status) {
			console.log(status.getValue(), status.getMessage());
			if (status.getValue() == 0) {
				$scope.ConversationList = RongIMClient.getInstance().getConversationList();
				initConversationList();
			} else if (status.getValue() == 4) {
				location.href = "/WebIMDemo/login.html";
			}
		}
	});
	//接收消息监听器
	RongIMClient.getInstance().setOnReceiveMessageListener({
		onReceived: function(data) {
			if (hasSound) {
				audio.play();
			}
			$scope.totalunreadcount = RongIMClient.getInstance().getTotalUnreadCount();
			$("#totalunreadcount").show().html($scope.totalunreadcount);
			if (currentConversationTargetId != data.getTargetId()) {
				if (document.title != "[新消息]融云 Demo - Web SDK") document.title = "[新消息]融云 Demo - Web SDK";
				var person = $scope.friendsList.filter(function(item) {
					return item.id == data.getTargetId();
				})[0];
				var tempval = RongIMClient.getInstance().getConversation(data.getConversationType(), data.getTargetId());
				if (person) {
					tempval.setConversationTitle(person.username);
				} else {
					if (data.getTargetId() in namelist) {
						tempval.setConversationTitle(namelist[data.getTargetId()]);
					} else {
						RongIMClient.getInstance().getUserInfo(data.getTargetId(), {
							onSuccess: function(x) {
								tempval.setConversationTitle(x.getUserName());
							},
							onError: function() {
								tempval.setConversationTitle("陌生人Id：" + data.getTargetId());
							}
						});
					}
				}
				if (!_historyMessagesCache[data.getConversationType() + "_" + data.getTargetId()]) _historyMessagesCache[data.getConversationType() + "_" + data.getTargetId()] = [data];
				else _historyMessagesCache[data.getConversationType() + "_" + data.getTargetId()].push(data);
			} else {
				addhistoryMessages(data);
			}
			initConversationList(data);
		}
	});

	function addhistoryMessages(item) {
		$scope.historyMessages.push(item);
		$(".dialog_box:first").append(String.stringFormat(historyStr, item.getMessageDirection() == 0 ? "other_user" : "self", item.getMessageDirection() == 1 ? owner.portrait : face, "", item.getMessageDirection() == 0 ? 'white_arrow.png' : 'blue_arrow.png', myUtil.msgType(item.getDetail()), item.getMessageId()));
	}

	function initConversationList() {
		_html = "",friend_name = "",friend_face = "";
		$scope.ConversationList.forEach(function(item) {	
		//	RongIMClient.getInstance().getUserInfo(item.getTargetId(), {
		//		onSuccess: function(x) {
		//			console.log(x);
		//			friend_name = x.getUserName();
		//			friend_face = x.getPortraitUri();
		//		},
		//		onError: function() {
		//			
		//		}
		//	});	
			_html += String.stringFormat(conversationStr, item.getConversationType(), item.getTargetId(), item.getConversationTitle(), "static/images/personPhoto.png", item.getUnreadMessageCount() == 0 ? "hidden" : "", item.getUnreadMessageCount(), item.getConversationTitle(), new Date(+item.getLatestTime()).toString().split(" ")[4]);

		});
		$("#conversationlist").html(_html);
	};

	//加载历史记录
	function getHistory(id, name, type) {
		if (!window.Modules) //检测websdk是否已经加载完毕
			return;
		currentConversationTargetId = id;
		$scope.conversationTitle = name;
		conver = RongIMClient.getInstance().createConversation(RongIMClient.ConversationType.setValue(type), currentConversationTargetId, name);
		if (!_historyMessagesCache[type + "_" + currentConversationTargetId]) _historyMessagesCache[type + "_" + currentConversationTargetId] = [];
		$scope.historyMessages = _historyMessagesCache[type + "_" + currentConversationTargetId];
		var tempval = $scope.ConversationList.filter(function(item) {
			return item.getTargetId() == currentConversationTargetId;
		})[0];
		if (tempval) {
			tempval.unread = 0;
			RongIMClient.getInstance().clearMessagesUnreadStatus(RongIMClient.ConversationType.setValue(type), currentConversationTargetId);
			$scope.totalunreadcount = RongIMClient.getInstance().getTotalUnreadCount();
			if ($scope.totalunreadcount <= 0) {
				document.title = "融云 Demo - Web SDK";
			}
		}
		$("#conversationTitle").html($scope.conversationTitle);
		_html = "";
		$scope.historyMessages.forEach(function(item, i) {
			_html += String.stringFormat(historyStr, item.getMessageDirection() == 0 ? "other_user" : "self", item.getMessageDirection() == 1 ? owner.portrait : face, "", item.getMessageDirection() == 0 ? 'white_arrow.png' : 'blue_arrow.png', myUtil.msgType(item.getDetail()), item.getMessageId());
		});
		$(".dialog_box:first").html(_html);
		$("#totalunreadcount").html($scope.totalunreadcount);
		if ($scope.totalunreadcount == 0) {
			$("#totalunreadcount").hide();
		}
	}
});

String.stringFormat = function(str) {
	for (var i = 1; i < arguments.length; i++) {
		str = str.replace(new RegExp("\\{" + (i - 1) + "\\}", "g"), arguments[i] || "");
	}
	return str;
}
var myUtil = {
	msgType: function(type) {
		if ("imageUri" in type) {
			return String.stringFormat('<div class="msgBody">{0}</div>', "<img class='imgThumbnail' src='data:image/jpg;base64," + type.content + "' bigUrl='" + type.imageUri + "'/>");
		} else if ("duration" in type) {
			return String.stringFormat('<div class="msgBody voice">{0}</div><input type="hidden" value="'+type.content+'">', "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" + type.duration);
		} else if ("poi" in type) {
			return String.stringFormat('<div class="msgBody">{0}</div>{1}', "[位置消息]" + type.poi, "<img src='data:image/png;base64," + type.content + "'/>");
		} else {
			return String.stringFormat('<div class="msgBody">{0}</div>', this.initEmotion(this.symbolReplace(type.content)));
		}
	},
	initEmotion: function(str) {
		var a = document.createElement("span")
		return RongIMClient.Expression.retrievalEmoji(str, function(img) {
			a.appendChild(img.img);
			var str = '<span class="RongIMexpression_' + img.englishName + '">' + a.innerHTML + '</span>';
			a.innerHTML = "";
			return str;
		});
	},
	symbolReplace: function(str) {
		if (!str) return '';
		str = str.replace(/&/g, '&amp;');
		str = str.replace(/</g, '&lt;');
		str = str.replace(/>/g, '&gt;');
		str = str.replace(/"/g, '&quot;');
		str = str.replace(/'/g, '&#039;');
		return str;
	},
	replaceSymbol: function(str) {
		if (!str) return '';
		str = str.replace(/&amp;/g, '&');
		str = str.replace(/&lt;/g, '<');
		str = str.replace(/&gt;/g, '>');
		str = str.replace(/&quot;/g, '"');
		str = str.replace(/&#039;/g, "'");
		str = str.replace(/&nbsp;/g, " ");
		return str;
	}
};
