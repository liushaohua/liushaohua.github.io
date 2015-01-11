function myAddEvent(a, b, c) {
    if (a.attachEvent) {
        a.attachEvent('on' + b, c)
    }
	else {
        a.addEventListener(b, c, false)
    }
}
myAddEvent(window, "load", 
function() {
    var e = document.getElementById("scroll");
    var f = document.getElementById("sImg");
    var g = f.getElementsByTagName("li");
    var h = document.getElementById("sLeft");
    var k = document.getElementById("sRight");
    var m = document.getElementById("scrollWidth");
    var n = document.getElementById("scrollBtn");
    var i = 0;
    for (i = 0; i < g.length; i++) {
        g[i].style.left = 220 * i + "px"
    }
    f.style.width = g[0].offsetWidth * g.length + "px";
    var o = 0;
    var p = 0;
    n.onmousedown = function(a) {
        var b = a || event;
        o = b.clientX;
        p = n.offsetLeft;
        if (n.setCapture) {
            n.onmousemove = doDrag;
            n.onmouseup = stopDrag;
            n.setCapture()
        } else {
            document.addEventListener("mousemove", doDrag, true);
            document.addEventListener("mouseup", stopDrag, true)
        }
    };
    function doDrag(a) {
        var b = a || event;
        var l = b.clientX - o + p;
        if (l < 0) {
            l = 0
        } else if (l > m.offsetWidth - n.offsetWidth) {
            l = m.offsetWidth - n.offsetWidth
        }
        var c = l / (m.offsetWidth - n.offsetWidth);
        moveSlide(c);
        fixPos()
    };
    function stopDrag() {
        if (n.releaseCapture) {
            n.onmousemove = null;
            n.onmouseup = null;
            n.releaseCapture()
        } else {
            document.removeEventListener("mousemove", doDrag, true);
            document.removeEventListener("mouseup", stopDrag, true)
        }
    };
    h.onclick = function() {
        var a = document.getElementById("sImg");
        var b = a.getElementsByTagName("img");
        moveSlide(sPercent - 1 / b.length);
        fixPos()
    };
    k.onclick = function() {
        var a = document.getElementById("sImg");
        var b = a.getElementsByTagName("img");
        moveSlide(sPercent + 1 / b.length);
        fixPos()
    };
    function fixPos() {
        var a = 0;
        var b = -1;
        var l = f.offsetLeft;
        for (i = 0; i < g.length; i++) {
            var c = Math.abs((l + g[i].offsetLeft + g[i].offsetWidth / 2) - e.offsetWidth / 2);
            var d = 1 - c / 300;
            if (d < 0) {
                d = 0
            }
            d += 1;
            if (a < d) {
                a = d;
                b = i
            }
            g[i].getElementsByTagName("img")[0].style.width = 200 * d + "px";
            g[i].style.marginLeft = -(200 * d - 200) / 2 + "px";
            g[i].style.marginTop = -(300 * d - 300) / 2 + "px";
            g[i].style.filter = "alpha(opacity:" + d / 1.5 * 100 + ")";
            g[i].style.opacity = d / 1.5
        }
        var j = g.length;
        for (i = b; i >= 0; i--) {
            g[i].style.zIndex = j--
        }
        j = g.length;
        for (i = b; i < g.length; i++) {
            g[i].style.zIndex = j--
        }
    };
    fixPos()
});
var sPercent = 0;
function moveSlide(a) {
    var b = document.getElementById("scroll");
    var c = b.getElementsByTagName("ul")[0];
    var d = document.getElementById("scrollWidth");
    var e = document.getElementById("scrollBtn");
    if (a < 0) {
        a = 0
    } else if (a > 1) {
        a = 1
    }
    e.style.left = a * (d.offsetWidth - e.offsetWidth) + "px";
    c.style.left = -(c.offsetWidth - b.offsetWidth) * a + "px";
    sPercent = a
};