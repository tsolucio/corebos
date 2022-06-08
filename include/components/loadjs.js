const loadJS = src => new Promise((resolve, reject) => {
	if (document.querySelector(`head > script[src="${src}"]`) !== null) {
		return resolve();
	}
	const script = document.createElement('script');
	script.src = src;
	script.async = true;
	document.head.appendChild(script);
	script.onload = resolve;
	script.onerror = reject;
});