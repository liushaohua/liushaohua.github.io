/**
 * Created by yataozhang on 14/11/6.
 */
;
(function (win) {
    win.Util = new function () {
        this.XHR = function () {
            for (var e = [function () {
                return new XMLHttpRequest
            }, function () {
                return new ActiveXObject("Msxml2.XMLHTTP")
            }, function () {
                return new ActiveXObject("Msxml3.XMLHTTP")
            }, function () {
                return new ActiveXObject("Microsoft.XMLHTTP")
            }], d = null, g = 0; g < e.length; g++) {
                try {
                    d = e[g]()
                } catch (b) {
                    continue
                }
                break
            }
            if (!d) {
                throw Error("XMLHttpRequest is not supported")
            }
            return d
        };
        this.JSONParse = function (str) {
            if (window.JSON) {
                return JSON.parse(str);
            } else {
                return eval("(" + str + ")");
            }
        };
        this.JSONStringify = function (obj) {
            if (window.JSON) {
                return JSON.stringify(obj);
            } else {
                var back = "{"
                for (var i in obj) {
                    back += i + ":" + (Object.prototype.toString.call(x[i]) == "[object Object]" ? arguments.callee(obj[i]) : obj[i] ) + ",";
                }
                back = back.slice(0, back.length - 1) + "}";
                return back;
            }
        };
        this.filter = function (array, func) {
            if ([].filter) {
                return array.filter(func);
            } else {
                var temp = [];
                for (var i = 0; i < array.length; i++) {
                    func(array[i], i, array) ? temp.push(array[i]) : void 0;
                }
                return temp;
            }
        };
    };
})(window);
