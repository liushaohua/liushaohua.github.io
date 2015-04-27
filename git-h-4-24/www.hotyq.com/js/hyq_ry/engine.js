$(function() {
	//私有变量
	var currentConversationTargetId = 0,
		token = "",
		//登陆人员信息默认值
		owner = {
			id: "",
			portrait: "static/images/user_img.jpg",
			name: "刘少翔",
			fid: "",
			fface: "",
			token: ""
		},/**/
		//初始化登陆人员信息
		list = 'id=45646&name=钟摆运动&portrait=http://img.hotyq.com/user/46/456/45646/10055279471edaa5.jpeg&token=G7fP+K+FulDWMISsVkU1NPop+SLpL1tvozs6G9bXr68KiO36FHvaTl+5MyyZbRbeaLF0aUdb7wjXzupSu2bWCg&fid=46919&fface=http://img.hotyq.com/user/19/469/46919/725528c20d82b09.jpg'.split('&'),
		$scope = {};
	

	if (list.length == 6) {
		$.each(list, function(i, item) {
			var val = item.split("=");
			owner[val[0]] = decodeURIComponent(val[1]);
		});
		console.log(owner);
		token = owner.token + '==';
		face = owner.fface;
		fid = owner.fid;
	}
	//未读消息数
	$scope.totalunreadcount = 0;

	
	//初始化SDK
	RongIMClient.init("pwe86ga5e1ej6"); //e0x9wycfx7flq z3v5yqkbv8v30

    RongIMClient.connect(token, {
        onSuccess: function(x) {
            console.log("connected，userid＝" + x);
        },
        onError: function(c) {
            console.log("失败:" + c.getMessage())
        }
    });
	
	//接收消息监听器
	RongIMClient.getInstance().setOnReceiveMessageListener({
		onReceived: function(data) {
			$scope.totalunreadcount = RongIMClient.getInstance().getTotalUnreadCount();
			if (currentConversationTargetId != data.getTargetId()) {
				//接收消息
				alert($scope.totalunreadcount);
			
			}
		}
	});
});
