export function jslog(connection, level, message) {
	if (!jslog.active) {
		return;
	}
	if (level=='fatal' || level=='trace' || level=='error' || level=='warn' || level=='debug' || level=='info') {
		sendJSLogToWebservice(connection, level, message);
	}
	if (jslog.jslog2console) {
		try {
			message = JSON.parse(message);
		} catch (e) {
			//message = message;
		}
		switch (level) {
		case 'table':
			console.table(message);
			break;
		case 'error':
			console.error(message);
			break;
		case 'warn':
			console.warn(message);
			break;
		case 'trace':
			console.trace(message);
			break;
		case 'info':
			console.info(message);
			break;
		default:
			console.log(message);
		}
	}
}

jslog.jslog2console = false;
jslog.active = true;

jslog.fatal = function (connection, message) {
	jslog(connection, 'fatal', message);
};
jslog.trace = function (connection, message) {
	jslog(connection, 'trace', message);
};
jslog.error = function (connection, message) {
	jslog(connection, 'error', message);
};
jslog.warn = function (connection, message) {
	jslog(connection, 'warn', message);
};
jslog.debug = function (connection, message) {
	jslog(connection, 'debug', message);
};
jslog.info = function (connection, message) {
	jslog(connection, 'info', message);
};

function sendJSLogToWebservice(connection, level, message) {
	connection.doInvoke('jsLog', {'level': level, 'message': message}, 'POST');
}
