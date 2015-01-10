function myAddEvent(a, b, c) {
    if (a.attachEvent) {
        a.attachEvent('on' + b, c)
    }
	else {
        a.addEventListener(b, c, false)
    }
}
myAddEvent(window, 'load', 
function() {
    var k = document.getElementById("center");
    var m = k.getElementsByTagName("dl");
    var n = k.getElementsByTagName("dt");
    var o = k.getElementsByTagName("span");
    var p = {};
    var q = {};
    var i = 0;
    for (i = 0; i < o.length; i++) {
        startDrag(o[i])
    }
    function startDrag(d) {
        d.onmousedown = function(a) {
            var b = a || event;
            p.x = b.clientX;
            p.y = b.clientY;
            q.x = d.parentNode.parentNode.offsetLeft;
            q.y = d.parentNode.parentNode.offsetTop;
            d.parentNode.parentNode.style.zIndex = "9999";
            if (d.setCapture) {
                d.onmousemove = doDrag;
                d.onmouseup = stopDrag;
                d.setCapture()
            } else {
                document.addEventListener("mousemove", doDrag, true);
                document.addEventListener("mouseup", stopDrag, true)
            }
            clearInterval(d.parentNode.parentNode.timer)
        };
        function doDrag(a) {
            var b = a || event;
            var l = b.clientX - p.x + q.x;
            var t = b.clientY - p.y + q.y;
            d.parentNode.parentNode.style.left = l + "px";
            d.parentNode.parentNode.style.top = t + "px";
            pengzhuang(d.parentNode.parentNode)
        };
        function stopDrag() {
            var a = null;
            var b = d.parentNode.parentNode;
            if (d.releaseCapture) {
                d.onmousemove = null;
                d.onmouseup = null;
                d.releaseCapture()
            } else {
                document.removeEventListener("mousemove", doDrag, true);
                document.removeEventListener("mouseup", stopDrag, true)
            }
            b.style.zIndex = "1";
            a = pengzhuang(b);
            if (a) {
                startMove(b, r[a.index].x, r[a.index].y);
                startMove(a, r[b.index].x, r[b.index].y);
                var c = a.index;
                a.index = b.index;
                b.index = c
            } else {
                startMove(b, r[b.index].x, r[b.index].y)
            }
        }
    };
    var r = [];
    for (i = 0; i < m.length; i++) {
        r[i] = {
            x: m[i].offsetLeft,
            y: m[i].offsetTop
        }
    }
    for (i = 0; i < m.length; i++) {
        m[i].index = i;
        m[i].style.left = r[i].x + "px";
        m[i].style.top = r[i].y + "px";
        m[i].style.margin = '0';
        m[i].style.position = "absolute"
    }
    function closeText(a, b) {
        var c = a.offsetTop;
        var d = a.offsetTop + a.offsetHeight;
        var e = a.offsetLeft;
        var f = a.offsetLeft + a.offsetWidth;
        var g = b.offsetTop;
        var h = b.offsetTop + b.offsetHeight;
        var i = b.offsetLeft;
        var j = b.offsetLeft + b.offsetWidth;
        if (c > h || d < g || e > j || f < i) {
            return false
        } else {
            return true
        }
    };
    function pengzhuang(a) {
        var i = 0;
        var b = 99999;
        var c = null;
        for (i = 0; i < m.length; i++) {
            if (a == m[i]) {
                continue
            }
            if (closeText(a, m[i])) {
                var d = Math.sqrt(Math.pow(m[i].offsetLeft - a.offsetLeft, 2) + Math.pow(m[i].offsetTop - a.offsetTop, 2));
                if (d < b) {
                    b = d;
                    c = m[i]
                }
            }
        }
        return c
    };
    function startMove(a, b, c) {
        if (a.timer) {
            clearInterval(a.timer)
        }
        if (!a.speedX) a.speedX = 0;
        if (!a.speedY) a.speedY = 0;
        a.timer = setInterval(function() {
            doMove(a, b, c)
        },
        30)
    };
    function doMove(a, b, c) {
        if (Math.abs(a.offsetLeft - b) < 1 && Math.abs(a.speedX) < 1 && Math.abs(a.offsetTop - c) < 1 && Math.abs(a.speedY) < 1) {
            clearInterval(a.timer);
            a.timer = null
        } else {
            a.speedX += (b - a.offsetLeft) / 6;
            a.speedX *= 0.75;
            a.style.left = a.offsetLeft + a.speedX + 'px';
            a.speedY += (c - a.offsetTop) / 6;
            a.speedY *= 0.75;
            a.style.top = a.offsetTop + a.speedY + 'px'
        }
    }
});