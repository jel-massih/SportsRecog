var socket = io.connect('http://playerrecog.nodejitsu.com/');

socket.on('updateSeats', function (path) {
	console.log("Recieved Seat Update!");
});