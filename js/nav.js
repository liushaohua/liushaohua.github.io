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
    var a = document.getElementById("header");
    var b = document.getElementById("nav");
    var c = b.getElementsByTagName("li");
    var d = document.getElementById("bg");
    var i = 0;
    for (i = 1; i < c.length; i++) {
        c[i].onmouseover = function() {
            beginMove(d, this.offsetLeft)
        }
		c[i].onmouseout = function() {
            beginMove(d, c[1].offsetLeft)
        }
    }
});
function beginMove(a, b) {
    if (a.timer) {
        clearInterval(a.timer)
    }
    a.timer = setInterval(function() {
        moveIng(a, b)
    },
    30)
};
var speed = 0;
function moveIng(a, b) {
    if (Math.abs(speed) < 1 && Math.abs(b - a.offsetLeft) < 1) {
        clearInterval(a.timer);
        a.timer = null
    } else {
        speed += (b - a.offsetLeft) / 5;
        speed *= 0.7;
        a.style.left = a.offsetLeft + speed + "px"
    }
};
