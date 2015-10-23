require('shelljs/global');
var mysql = require('mysql');
var Random = require('random-js');

var YakRecorder = {};
YakRecorder.config = {
	table: 'yaks',
	create: "CREATE TABLE `yaks` ( \
		  `id` varchar(128) NOT NULL DEFAULT '', \
		  `message` varchar(255) DEFAULT NULL, \
		  `latitude` float DEFAULT '0', \
		  `longitude` float DEFAULT '0', \
		  `gmt` float DEFAULT '0', \
		  `numberOfLikes` int(11) DEFAULT '0', \
		  `comments` int(11) DEFAULT '0', \
		  `posterID` varchar(32) DEFAULT '0', \
		  `locationName` varchar(128) DEFAULT NULL, \
		  `score` float DEFAULT '0', \
		  `handle` varchar(128) DEFAULT NULL, \
		  PRIMARY KEY (`id`), \
		  KEY `posterID` (`posterID`) USING BTREE, \
		  KEY `locationName` (`locationName`) USING BTREE, \
		  KEY `coordinates` (`longitude`,`latitude`) USING BTREE \
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
	columns: [
		'id',
		'message',
		'latitude',
		'longitude',
		'gmt',
		'numberOfLikes',
		'comments',
		'posterID',
		'locationName',
		'score',
		'handle'
	]
}

YakRecorder.getData = function (callback) {
	exec('php yak.php', { silent: true }, function (status, output) {
		var data = JSON.parse(output);
		if (!data) {
			console.log('No data returned from the server.');
		} else {
			callback(data);
		}
	});
}

YakRecorder.record = function (data, connection, success) {

	// Prepare the params string.
	var rows = [];
	var replacements = [];

	// For each of the returned records...
	for (var index in data) {
		if (!data.hasOwnProperty(index)) continue;
		if (!data[index]['messageID']) continue; // Skip ones that don't have an ID.
		data[index]['id'] = data[index]['messageID']; // Re-map the message ID.
		rows.push('(' + 
			Array.apply(null, Array(YakRecorder.config.columns.length)).map(function(){ return '?' }).join(',') + 
			')');
		for (var columnIndex in YakRecorder.config.columns) {
			if (!YakRecorder.config.columns.hasOwnProperty(columnIndex)) continue;
			replacements.push(data[index][YakRecorder.config.columns[columnIndex]]);
		}
	}

	var joinedColumns = YakRecorder.config.columns.join(',');
	var updateParts = [];
	for (var columnIndex in YakRecorder.config.columns) {
		if (!YakRecorder.config.columns.hasOwnProperty(columnIndex)) continue;
		var columnName = YakRecorder.config.columns[columnIndex];
		updateParts.push(columnName + '=VALUES(' + columnName + ')');
	}
	var query = 'INSERT INTO yaks (' + joinedColumns + ') VALUES ' + rows.join(',') + 
		' ON DUPLICATE KEY UPDATE ' + updateParts.join(',') + ';';
	connection.query(query, replacements, function (error, rows, fields) {
			if (error) { console.log(error); return; }
			success();
		});

}

YakRecorder.ensureSchema = function (connection, success) {
	connection.query('SHOW TABLES', function (error, rows, fields) {
		if (error) { console.log(error); return; }
		var hasTable = false;
		for (var index in rows) {
			if (!rows.hasOwnProperty(index)) continue;
			for (var column in rows[index]) {
				if (!rows[index].hasOwnProperty(column)) continue;
				if (rows[index][column] == YakRecorder.config.table) {
					hasTable = true;
				}
			}
		}

		if (hasTable) {
			success();
		} else {
			connection.query(YakRecorder.config.create, function (error, rows, fields) {
				if (error) {
					console.log(error);
					return;
				}
				success();
			});
		}
	});
}

YakRecorder.connection = null;

YakRecorder.run = function () {

	if (YakRecorder.connection) {
		YakRecorder.connection.end();
		YakRecorder.connection = null;
	}

	console.log(' - Connecting to the database...');
	YakRecorder.connection = mysql.createConnection({
		host: 'database',
		user: env['MYSQL_USER'],
		password: env['MYSQL_PASSWORD'],
		database: env['MYSQL_DATABASE'],
		charset: 'utf8mb4'
	});
	YakRecorder.connection.connect();

	console.log(' - Ensuring the database schema is setup...');
	YakRecorder.ensureSchema(YakRecorder.connection, function () {

		console.log('Fetching Yaks');
		YakRecorder.getData(function (yaks) {
			YakRecorder.record(yaks, YakRecorder.connection, function () {
				console.log(' - Recorded data.');
			});
		});

	});

	var engine = Random.engines.mt19937().autoSeed();
	var min = 60 * 1000 * 10;
	var max = 60 * 1000 * 30;
	setTimeout(YakRecorder.run, Random.integer(min, max)(engine));

}

YakRecorder.run();
