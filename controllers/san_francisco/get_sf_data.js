function vue_cards() {
    var ajax = new OrionAjax('/controllers/san_francisco/get_sf_data.php', 'get');
    ajax.getResponse(function (resp) {
        var data = JSON.parse(resp);

        var card = new Vue({
            el: '#vue-card',
            data: {
                zip_codes: data
            }
        })



        map();

        function map() {
            // turn json into object
            var zip_data = {};

            var length = data.length;
            for (var i = 0; i < length; i++) {
                zip_data[ data[i].zip ] = {};
                zip_data[ data[i].zip ].ap = data[i].ap;
                zip_data[ data[i].zip ].ncc = data[i].ncc;
                zip_data[ data[i].zip ].ccc = data[i].ccc;
                zip_data[ data[i].zip ].date = data[i].date;
            }



            var zip_ajax = new OrionAjax('https://raw.githubusercontent.com/visgl/deck.gl/master/examples/layer-browser/data/sf.zip.geo.json', 'get');
            zip_ajax.getResponse(function (response) {

                var map = L.map("map", {
                    dragging: true,
                    doubleClickZoom: false,
                    scrollWheelZoom: true,
                    touchZoom: true,
                    boxZoom: true,
                    keyboard: true,
                    zoomControl: true,
                    maxZoom: 18,
                    minZoom: 12,
                    // map boundaries and bouncing
                    maxBoundsViscosity: 1.0,
                    maxBounds: [[37.601966, -122.222218], [37.922070, -122.642793]]
                }).setView([37.7550622, -122.4414047, 17], 13);
                //L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                //L.tileLayer("https://api.maptiler.com/maps/streets/{z}/{x}/{y}.png?key=ZkvuhtBcdzwryiprePcx", {
                L.tileLayer("https://api.mapbox.com/styles/v1/mapbox/streets-v11/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibmdjNTM5OCIsImEiOiJja2tkaWJzMHEwMzBuMnZvMXk0ODg5MmkxIn0.kH41r5NUMPm-1_KuIuphXw", {
                    // keep street text visible
                    tileSize: 512,
                    zoomOffset: -1,
                    // maptiler logo
                    attribution: '&copy; <a href="https://www.mapbox.com/map-feedback/">MapBox</a>'
                }).addTo(map);
//https://api.mapbox.com/styles/v1/mapbox/streets-v11/tiles/%7Bz%7D/%7Bx%7D/%7By%7D?access_token=pk.eyJ1IjoibmdjNTM5OCIsImEiOiJja2tkaWJzMHEwMzBuMnZvMXk0ODg5MmkxIn0.kH41r5NUMPm-1_KuIuphXw
                var mapStyle = {
                    //"color": "#403b37",
                    "color": "#413d3d", // polygon border color
                    "weight": 1,        // polygon border weight
                    "opacity": 1,       // polygon border opacity
                    "fillOpacity": 0,   // polygon area color
                };


                L.geoJSON(JSON.parse(response).features, {
                    style: mapStyle,
                    onEachFeature: function (feature, layer) {
                        // display zip code
                        var label = L.marker(layer.getBounds().getCenter(), {
                            icon: L.divIcon({
                                className: 'label',
                                html: '<span style="font-size: 18px; background-color: #aba6a6">' + feature.properties.ZIP_CODE + '</span>',
                                iconSize: [0, 0]
                            })
                        }).addTo(map);

                        label.on('mouseclick', function() {
                            console.log('click');
                        });

                        var ncc = ccc = ap = null;
                        if (zip_data[feature.properties.ZIP_CODE]) {
                            ncc = zip_data[feature.properties.ZIP_CODE].ncc;
                            ccc = zip_data[feature.properties.ZIP_CODE].ccc;
                            ap = zip_data[feature.properties.ZIP_CODE].ap;
                        }
                        else {
                            ncc = 'N/A';
                            ccc = 'N/A';
                            ap = 'N/A';
                        }


                        var tooltip = layer.bindPopup(
                            '<div style="">' +
                            'Zip Code ' + feature.properties.ZIP_CODE + '<br>' +
                            'new cases: ' + ncc + '<br>' +
                            'total cases: ' + ccc + '<br>' +
                            'population: ' + ap + '<br>' +
                            '</div>',
                            {className: 'tooltip-no-event-flicker'});

                        tooltip.isOpen = false; // add new object property indicating tooltip opened or closed

                        layer.on('mouseover', function (e) {
                            tooltip.openPopup();
                        })
                        layer.on('mousemove', function(e) {
                            tooltip.openPopup(e.latlng);
                        })
                        layer.on('mouseout', function () {
                            tooltip.closePopup();
                        })
                        layer.on('mouseleave', function () {
                            tooltip.closePopup();
                        })
                        layer.on('click', function (e) {
                            if (tooltip.isOpen) {
                                tooltip.closePopup();
                                tooltip.isOpen = false;
                            }
                            else {
                                tooltip.openPopup(e.latlng);
                                tooltip.isOpen = true;
                            }
                        })
                    }
                }).addTo(map);
            })
        } // end of map()

    }) // end of initial ajax call
}



