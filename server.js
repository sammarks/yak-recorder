var Yak = require('./yak');

// For testing purposes, hard-code the lat and long.
var lat = 38.0297;
var lon = -84.4947;
var alt = 296.739; // And some random altitude.

var API = new Yak.Yak(lat, lon, 296.739);
API.registerUser(function (instance) {
	instance.getMessages(lat, lon, function (data) {
		console.log(data);
	}, function () {
		console.log('Error!');
	});
});
