'use strict';

importScripts('include/sw-precache/workbox/workbox-sw.js');

workbox.setConfig({
	modulePathPrefix: 'include/sw-precache/workbox/',
	debug: false
});
self.addEventListener('message', (event) => {
	if (event.data && event.data.type === 'SKIP_WAITING') {
		self.skipWaiting();
	}
});

if (workbox) {
	workbox.precaching.precacheAndRoute([]);
} else {
	console.log(`Workbox didn't load`);
}