(function () {
'use strict';
	var two = 2;
	var funcTwo = function () {
		var two = 22;
		console.log(two);
	};
	funcTwo();
	console.log(two);
})();
