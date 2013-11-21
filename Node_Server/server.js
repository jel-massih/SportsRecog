var app = require('express')(),
	server = require('http').createServer(app),
	port = 8080,
	url = 'http://playerrecog.nodejitsu.com' + port + '/',
	io = require('socket.io').listen(server, {log: false});

app.configure(function () {
	app.use(require('express').bodyParser());
	app.use(app.router);
});

server.listen(port);
console.log("Express Server Listening On Port " + port);
console.log(url);

app.post('/callbacks/event/:eventType', function(req, res) {
	console.log("PUT /callbacks/event/" + req.query.url);
	io.sockets.emit('updateloc', { url: req.query.url});
	res.send('OK');
});

app.get('/', function(req, res) {
	console.log("Connected");
});

io.sockets.on('connection', function (socket) {
    console.log("COnnection!")
});