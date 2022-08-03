// JavaScript Document

// Start jQuery code
$(function() {

/**
 * Create Google Map with maplace-js jQuery Plugin
 * https://maplacejs.com
 * Define map using the following properties
 * Map will automatically be built in #gmap
 */
new Maplace({
    locations: [{
        animation: 2, // BOUNCE: 1, DROP: 2
        html: "<h4>NWTC - Green Bay WI</h4><span>College of Business</span><ul><li>Thursday, December 4, 2021</li><li>4:30pm</li></ul>",
        icon: "../js/images/gmap-marker.png", // path is from the contact/index.html page
        lat: 44.526376,
        lon: -88.107691,
        zoom: 16
    }]
}).Load(); // End Maplace

}); // End jQuery


