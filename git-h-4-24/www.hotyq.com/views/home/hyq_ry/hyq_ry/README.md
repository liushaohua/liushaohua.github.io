demo-web-sdk
============

Demostration of Rong Web SDK.

## 融云 web SDK 如何使用

[文档参考](http://docs.rongcloud.cn/api/js/index.html " SDK 文档")


使用融云 `web SDK` 发消息之前必须利用申请的`appkey`进行初始化，只有在初始化之后才能使用RongIMClient.getInstance()方法得到实例.如只想知晓如何使用 web SDK 请参考 `SDK_Demo.html`

## 指定版本号引用
`http://res.websdk.rong.io/RongIMClient{-版本号}-min.js` 如不添加则默认为最新版本SDK，添加版本号则加载指定版本SDK

### 初始化web sdk ，此项必须设置
```js
RongIMClient.init("appkey");
```
### 设置链接状态监听器，此项必须设置
```js
RongIMClient.setConnectionStatusListener({  
     onChanged: function (status) {  
         window.console.log(status.getValue(), status.getMessage(), new Date()) 
     }  
}); 
```
### 链接融云服务器，此项必须设置

此方法为异步方法，请确定链接成功之后再执行其他操作。成功返回登录人员id失败则返回失败枚举对象
```js
RongIMClient.connect("token", {
     onSuccess: function (userid) {
         window.console.log("connected，userid＝" + userid)
     },
     onError: function (x) {
         window.console.log(x.getMessage())
     }
});
```
### 设置消息监听器，此项必须设置

所有接收的消息都通过此监听器进行处理，可以通过message.getMessageType()和RongIMClient.MessageType枚举对象来判断消息类型
```js
RongIMClient.getInstance().setOnReceiveMessageListener({
     onReceived: function (message) {
         //message为RongIMMessage子类实例
         console.log(message.getContent());
     }
});
```
### 得到RongIMClient实例对象,只有执行init()之后才能使用getInstance()方法
```js
var ins = RongIMClient.getInstance();
```
### 设置私人会话类型
```js
var contype = RongIMClient.ConversationType.PRIVATE;
```
### 例如注册某个元素点击事件(举例)
```js
element.onclick = function () {
//调用实例的发送消息方法
     ins.sendMessage(contype, "targetId", RongIMClient.TextMessage.obtain("发送消息内容"), null, {
           onSuccess: function () {
                //发送成功逻辑处理
           },
           onError: function (data) {
                //发送失败逻辑处理
                console.log(data.getValue(),data.getMessage())
           }
       });
};
```
### 使用指定链接通道链接服务器 
web SDK 通道才用层层降级的方式进行兼容处理。连接通道首先默认使用websocket，如环境不支持websocket则自动降级至flash socket，不支持flash则自定降级至xhr-polling，以此来达到全兼容的目的。
<br/>
如果想强制使用长链接连接服务器则必须设置`window.WEB_XHR_POLLING = true;`
#### 通道选项设置[使用此项必须为0.9.6版本,使用前请确定SDK版本号为0.9.6及以上版本]
```js
     //强制使用长链接进行通讯 设置此项，并保证此项优先级最高并且最先被执行，否则设置无效
     window.WEB_XHR_POLLING = true;
  ```
  ```js
     //强制使用flash进行通讯 设置此项，并保证此项优先级最高并且最先被执行，否则设置无效
     window.WEB_SOCKET_FORCE_FLASH = true;
```
##通道选项优先级比较
`window.WEB_SOCKET_FORCE_FLASH > window.WEB_XHR_POLLING`

### 注意:
`web SDK` 是全异步的，所以发送消息之前确保链接成功。
本demo仅做演示使用，页面不做兼容性考虑。
本`web SDK`为强兼容性，demo的弱兼容性与SDK无关。
使用本示例的页面在商业上使用而引发的处理不当与本人以及本人所属组织无关。
本示例仅做演示，仅仅只做演示。未考虑低版本及部分版本浏览器兼容性。
