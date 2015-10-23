var uuid = require('node-uuid');
var md5 = require('blueimp-md5').md5;
var CryptoJS = require('crypto-js');
var http = require('http');
var extend = require('util')._extend;
var restler = require('restler');
var querystring = require('querystring');
var btoa = require('btoa');
var Yak = {};

// This class is a port of http://pastebin.com/Bjzph6BQ
// to Javascript.

// Configuration variables.
Yak.config = {

	// Choose hostname based on location:
	// - us-east-api.yikyakapi.net
	// - us-central-api.yikyakapi.net
	// - us-west-api.yikyakapi.net
	// - asia-api.yikyakapi.net
	// - europe-api.yikyakapi.net
	host: 'https://us-central-api.yikyakapi.net',

	base: '/api/',
	version: '2.2.1',
	key: 'F7CAFA2F-FE67-4E03-A090-AC7FFF010729',
	headers: {
		'Proxy-Connection': 'keep-alive',
		'Accept-Language': 'en;q=1, zh-Hans;q=0.9',
		'Connection': 'keep-alive',
		'User-Agent': 'Yik Yak/2.2.1 (iPhone; iOS 8.1.2; Scale/2.00)'
	}

};

Yak.Yak = function (latitude, longitude, altitude, speed, course) {

	// Initialize
	var _user = false;
	var _latitude = latitude;
	var _longitude = longitude;
	var _altitude = altitude;
	var _speed = -1.0000;
	var _course = -1.0000;
	var _horizontal_accuracy = 65.0000;
	var _vertical_accuracy = 10.0000;
	if (typeof('speed') !== undefined) {
		_speed = speed;
	}
	if (typeof('cource') !== undefined) {
		_course = course;
	}

	this.buildURI = function (resource, params) {
		var uri = Yak.config.base + resource;
		if (typeof(params) !== 'undefined') {
			uri += '?' + querystring.stringify(params);
		}
		return uri;
	}

	this.getUser = function () {
		return this._user;
	}

	this.setUser = function (user) {
		this._user = user;
		return this;
	}

	this.getLatitude = function () {
		return this._latitude;
	}

	this.setLatitude = function (latitude) {
		this._latitude = latitude;
		return this;
	}

	this.getLongitude = function () {
		return this._longitude;
	}

	this.setLongitude = function (longitude) {
		this._longitude = longitude;
		return this;
	}

	this.getAltitude = function () {
		return this._altitude;
	}

	this.setAltitude = function (altitude) {
		this._altitude = altitude;
		return this;
	}

	this.getSpeed = function () {
		return this._speed;
	}

	this.setSpeed = function (speed) {
		this._speed = speed;
		return this;
	}

	this.getCourse = function () {
		return this._course;
	}

	this.setCourse = function (course) {
		this._course = course;
		return this;
	}

	this.getHorizontalAccuracy = function () {
		return this._horizontal_accuracy;
	}

	this.setHorizontalAccuracy = function (horizontal_accuracy) {
		this._horizontal_accuracy = horizontal_accuracy;
		return this;
	}

	this.getVerticalAccuracy = function () {
		return this._vertical_accuracy;
	}

	this.setVerticalAccuracy = function (vertical_accuracy) {
		this._vertical_accuracy = vertical_accuracy;
		return this;
	}

	this.generateID = function () {
		return uuid.v4().toUpperCase();
	}

	this.generateID2 = function () {
		var uid = md5(this.generateID());
		return uid.substr(0, 6) + uid.substr(5, uid.length - 1);
	}

	this.hash = function (data, salt) {
		var hash = CryptoJS.HmacSHA1(data + salt.toString(), Yak.config.key);
		return hash.toString(CryptoJS.enc.Base64);
	}

	this.get = function (resource, _params, success, failure, cookies) {

		var cookies = cookies | [];
		var params = params | [];
		var salt = Math.floor(Date.now() / 1000);
		var hash = this.hash(this.buildURI(resource, params), salt);
		if (typeof(_params) != typeof({})) {
			_params = {};
		}
		var params = extend(_params, { 'salt': salt, 'hash': hash });

		var cookieString = '';
		var prefix = '';
		for (var cookieName in cookies) {
			if (!cookie.hasOwnProperty(cookieName)) continue;
			var cookie = cookies[cookieName];
			cookieString += prefix + cookieName + ':' + cookie;
			prefix = '; ';
		}

		var path = Yak.config.host + this.buildURI(resource, params);
		console.log('GET %s', path);
		restler.get(path, {
			headers: extend(Yak.config.headers, {
				'Cookie': cookieString
			})
		}).on('success', success).on('fail', function (data, response) {
			console.log('Request failed with status %s', response.statusCode);
			console.log(data);
			failure();
		}).on('error', function (error, response) {
			console.log('Request failed with error.');
			console.log(error);
			failure();
		});

	}

	this.getMessages = function (latitude, longitude, success, error) {
		var __latitude = latitude | this._latitude;
		var __longitude = longitude | this._longitude;
		this.get('getMessages', {
			'userID': this._user,
			'lat': __latitude,
			'long': __longitude,
			'userLat': this._latitude,
			'userLong': this._longitude
		}, success, error);
	}

	this.registerUser = function (success) {
		var id = this.generateID();
		var self = this;
		this.get('registerUser', {
			'userID': id,
			'lat': _latitude,
			'long': _longitude,
			'version': Yak.config.version
		}, function (data, response) {
			self.setUser(id);
			success(self);
		}, function () {
			console.log('Error registering the user.');
		});
	}

}

module.exports = Yak;
