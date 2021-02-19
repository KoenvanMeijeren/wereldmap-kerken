<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Wereldmap kerken</title>

    <meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=no">

    <link href='https://api.mapbox.com/mapbox-gl-js/v2.1.1/mapbox-gl.css' rel='stylesheet' />
    <style>
        body {
            margin: 0;
            padding: 0;
        }

        #map {
            position: absolute;
            top: 0;
            bottom: 0;
            width: 100%;
        }

        .mapboxgl-popup {
            max-width: 400px;
            font: 12px/20px 'Helvetica Neue', Arial, Helvetica, sans-serif;
        }
    </style>
</head>
<body>
<div id='map' style='width: 100%; height: 500px;'></div>
</body>
<script src='https://api.mapbox.com/mapbox-gl-js/v2.1.1/mapbox-gl.js'></script>
<script>
    mapboxgl.accessToken = 'pk.eyJ1Ijoia29lbnZhbm1laWplcmVuIiwiYSI6ImNrbGNmcmEzdzF0M24yd25wbGl5cXR6NWYifQ.Ghg_Sxd7GcuQUVaka0OcNg';
    const map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/mapbox/streets-v11',
        center: [52.092876, 5.104480],
        zoom: 1
    });

    map.on('load', function () {
        map.loadImage(
            'https://docs.mapbox.com/mapbox-gl-js/assets/custom_marker.png',
            // Add an image to use as a custom marker
            function (error, image) {
                if (error) throw error;
                map.addImage('custom-marker', image);

                map.addSource('places', {
                    'type': 'geojson',
                    'data': {
                        'type': 'FeatureCollection',
                        'features': [
                            {
                                'type': 'Feature',
                                'properties': {
                                    'description':
                                        '<strong>Kerk A</strong><br><a href="www.google.com">Kerk</a>'
                                },
                                'geometry': {
                                    'type': 'Point',
                                    'coordinates': [-77.038659, 38.931567]
                                }
                            },
                            {
                                'type': 'Feature',
                                'properties': {
                                    'description':
                                        '<strong>Kerk A</strong><br><a href="www.google.com">Kerk</a>'
                                },
                                'geometry': {
                                    'type': 'Point',
                                    'coordinates': [-77.003168, 38.894651]
                                }
                            },
                            {
                                'type': 'Feature',
                                'properties': {
                                    'description':
                                        '<strong>Kerk A</strong><br><a href="www.google.com">Kerk</a>'
                                },
                                'geometry': {
                                    'type': 'Point',
                                    'coordinates': [-77.090372, 38.881189]
                                }
                            },
                            {
                                'type': 'Feature',
                                'properties': {
                                    'description':
                                        '<strong>Kerk A</strong><br><a href="www.google.com">Kerk</a>'
                                },
                                'geometry': {
                                    'type': 'Point',
                                    'coordinates': [-77.111561, 38.882342]
                                }
                            },
                            {
                                'type': 'Feature',
                                'properties': {
                                    'description':
                                        '<strong>Kerk A</strong><br><a href="www.google.com">Kerk</a>'
                                },
                                'geometry': {
                                    'type': 'Point',
                                    'coordinates': [-77.052477, 38.943951]
                                }
                            },
                            {
                                'type': 'Feature',
                                'properties': {
                                    'description':
                                        '<strong>Kerk A</strong><br><a href="www.google.com">Kerk</a>'
                                },
                                'geometry': {
                                    'type': 'Point',
                                    'coordinates': [-77.043444, 38.909664]
                                }
                            },
                            {
                                'type': 'Feature',
                                'properties': {
                                    'description':
                                        '<strong>Kerk A</strong><br><a href="www.google.com">Kerk</a>'
                                },
                                'geometry': {
                                    'type': 'Point',
                                    'coordinates': [-77.031706, 38.914581]
                                }
                            },
                            {
                                'type': 'Feature',
                                'properties': {
                                    'description':
                                        '<strong>Kerk A</strong><br><a href="www.google.com">Kerk</a>'
                                },
                                'geometry': {
                                    'type': 'Point',
                                    'coordinates': [-77.020945, 38.878241]
                                }
                            },
                            {
                                'type': 'Feature',
                                'properties': {
                                    'description':
                                        '<strong>Kerk A</strong><br><a href="www.google.com">Kerk</a>'
                                },
                                'geometry': {
                                    'type': 'Point',
                                    'coordinates': [-77.007481, 38.876516]
                                }
                            }
                        ]
                    }
                });

                // Add a layer showing the places.
                map.addLayer({
                    'id': 'places',
                    'type': 'symbol',
                    'source': 'places',
                    'layout': {
                        'icon-image': 'custom-marker',
                        'icon-allow-overlap': true
                    }
                });
            }
        );

        // Create a popup, but don't add it to the map yet.
        const popup = new mapboxgl.Popup({
            closeButton: false,
            closeOnClick: false
        });

        map.on('mouseenter', 'places', function (e) {
            // Change the cursor style as a UI indicator.
            map.getCanvas().style.cursor = 'pointer';

            const coordinates = e.features[0].geometry.coordinates.slice();
            const description = e.features[0].properties.description;

            // Ensure that if the map is zoomed out such that multiple
            // copies of the feature are visible, the popup appears
            // over the copy being pointed to.
            while (Math.abs(e.lngLat.lng - coordinates[0]) > 180) {
                coordinates[0] += e.lngLat.lng > coordinates[0] ? 360 : -360;
            }

            // Populate the popup and set its coordinates
            // based on the feature found.
            popup.setLngLat(coordinates).setHTML(description).addTo(map);
        });

        map.on('mouseleave', 'places', function () {
            map.getCanvas().style.cursor = '';
            popup.remove();
        });
    });
</script>
</html>