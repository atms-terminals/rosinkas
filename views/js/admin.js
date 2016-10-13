/* global L */
$(document).ready(function() {
	'use strict';

	$('#mapid').height($(window).height() - 60);

	var mymap = L.map('mapid').setView([54.9747100, 73.3881400], 13);

	L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpandmbXliNDBjZWd2M2x6bDk3c2ZtOTkifQ._QA7i5Mpkd_m30IGElHziw', {
		maxZoom: 18,
		id: 'mapbox.streets'
	}).addTo(mymap);

});