var http = require('http'),
    url = require('url'),
    mime = require('mime'),
    path = require('path'),
    fs = require('fs');

http.createServer(function (req, res) {
    if (req.url == '/favicon.icon') {
        res.end();
        return;
    }

    var reqPath = path.normalize(req.url),
        filePath = path.join(__dirname, reqPath);

    fs.exists(filePath, function (exists) {
        if (exists) {
            if (fs.statSync(filePath).isDirectory()) {
                var str = '';
                fs.readdir(filePath, function(err,files) {
                    res.writeHead('200, {"Content-Type": "text/html;charset=utf-8"}');
                    err && ~function () {
                        res.write(err);
                        return;
                    } ();
                    files.forEach(function (file) {
                        str += '<li><a href="' + path.join(reqPath,file) + '">' + file + ' </a></li>';
                    });
                    res.end(str);
                });
            } else {
                res.writeHead(200, {'Content-Type': mime.lookup(path.basename(filePath)) + ';charset=utf-8'});
                fs.readFile(filePath, {flag: "r"}, function (err, data) {
                    if (err) {
                        res.end(err);
                    } else {
                        res.end(data);
                    }
                });
            }
        } else {
            res.writeHead(404, {"Content-Type": "text/html"});
            res.write('not defined');
            res.end();
        }

    });
}).listen(8080);