var socket = io.connect('http://playerrecog.nodejitsu.com/');

socket.on('updateloc', function (path) {
	console.log(path.url);
	window.location.assign(path.url)
});