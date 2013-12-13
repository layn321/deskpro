Modernizr.addTest('osmac', function() {
	if (!navigator || !navigator.appVersion) {
		return false;
	}

	return (navigator.appVersion.indexOf("Mac")!=-1);
});

Modernizr.addTest('browser-ie', function() {
	if (!navigator || !navigator.appVersion) {
		return false;
	}

	return (navigator.appVersion.toLowerCase().indexOf("msie")!=-1);
});

Modernizr.addTest('ipad', function () {
  return !!navigator.userAgent.match(/iPad/i);
});

Modernizr.addTest('iphone', function () {
  return !!navigator.userAgent.match(/iPhone/i);
});

Modernizr.addTest('ipod', function () {
  return !!navigator.userAgent.match(/iPod/i);
});

Modernizr.addTest('appleios', function () {
  return (Modernizr.ipad || Modernizr.ipod || Modernizr.iphone);
});