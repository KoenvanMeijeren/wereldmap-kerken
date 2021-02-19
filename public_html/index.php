<!DOCTYPE html>
<html>
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
            width: 100%;
        }

        #buttons {
            text-align: center;
        }

        .button {
            display: inline-block;
            cursor: pointer;
            padding: 8px;
            border-radius: 3px;
            margin-top: 10px;
            font-size: 12px;
            text-align: center;
            color: #fff;
            background: #ee8a65;
            font-family: sans-serif;
            font-weight: bold;
        }

        .filter-ctrl {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1;
        }

        .filter-ctrl input[type='text'] {
            font: 12px/20px 'Helvetica Neue', Arial, Helvetica, sans-serif;
            width: 100%;
            border: 0;
            background-color: #fff;
            margin: 0;
            color: rgba(0, 0, 0, 0.5);
            padding: 10px;
            box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.1);
            border-radius: 3px;
            width: 180px;
        }
    </style>
</head>
<body>
<ul id="buttons">
    <li id="button-fr" class="button">French</li>
    <li id="button-ru" class="button">Russian</li>
    <li id="button-de" class="button">German</li>
    <li id="button-es" class="button">Spanish</li>
    <li id="button-en" class="button">English</li>
</ul>

<div id='map' style='width: 100%; height: 500px;'></div>
<div class="filter-ctrl">
    <input id="filter-input" type="text" name="filter" placeholder="Filter by name">
</div>

</body>
<script src='https://api.mapbox.com/mapbox-gl-js/v2.1.1/mapbox-gl.js'></script>
<script>
    mapboxgl.accessToken = 'pk.eyJ1Ijoia29lbnZhbm1laWplcmVuIiwiYSI6ImNrbGNmcmEzdzF0M24yd25wbGl5cXR6NWYifQ.Ghg_Sxd7GcuQUVaka0OcNg';
    let layerIDs = []; // Will contain a list used to filter against.
    const filterInput = document.getElementById('filter-input');
    const map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/mapbox/streets-v11',
        center: [52.092876, 5.104480],
        minZoom: 1,
        zoom: 1
    });

    // Add zoom and rotation controls to the map.
    map.addControl(new mapboxgl.NavigationControl());

    map.on('load', function () {
        // Add a new source from our GeoJSON data and
        // set the 'cluster' option to true. GL-JS will
        // add the point_count property to your source data.
        map.addSource('kerken', {
            'type': 'geojson',
            'data': 'churches.json',
            cluster: true,
            clusterMaxZoom: 14, // Max zoom to cluster points on
            clusterRadius: 50 // Radius of each cluster when clustering points (defaults to 50)
        });

        map.addLayer({
            id: 'clusters',
            type: 'circle',
            source: 'kerken',
            filter: ['has', 'point_count'],
            paint: {
                // Use step expressions (https://docs.mapbox.com/mapbox-gl-js/style-spec/#expressions-step)
                // with three steps to implement three types of circles:
                //   * Blue, 20px circles when point count is less than 100
                //   * Yellow, 30px circles when point count is between 100 and 750
                //   * Pink, 40px circles when point count is greater than or equal to 750
                'circle-color': [
                    'step',
                    ['get', 'point_count'],
                    '#51bbd6',
                    100,
                    '#f1f075',
                    750,
                    '#f28cb1'
                ],
                'circle-radius': [
                    'step',
                    ['get', 'point_count'],
                    20,
                    100,
                    30,
                    750,
                    40
                ]
            }
        });

        map.addLayer({
            id: 'cluster-count',
            type: 'symbol',
            source: 'kerken',
            filter: ['has', 'point_count'],
            layout: {
                'text-field': '{point_count_abbreviated}',
                'text-font': ['DIN Offc Pro Medium', 'Arial Unicode MS Bold'],
                'text-size': 12
            }
        });

        map.addLayer({
            id: 'unclustered-point',
            type: 'circle',
            source: 'kerken',
            filter: ['!', ['has', 'point_count']],
            paint: {
                'circle-color': '#11b4da',
                'circle-radius': 10,
                'circle-stroke-width': 3,
                'circle-stroke-color': '#fff'
            }
        });

        // inspect a cluster on click
        map.on('click', 'clusters', function (e) {
            const features = map.queryRenderedFeatures(e.point, {
                layers: ['clusters']
            });
            const clusterId = features[0].properties.cluster_id;
            map.getSource('kerken').getClusterExpansionZoom(
                clusterId,
                function (err, zoom) {
                    if (err) return;

                    map.easeTo({
                        center: features[0].geometry.coordinates,
                        zoom: zoom
                    });
                }
            );
        });

        // When a click event occurs on a feature in
        // the unclustered-point layer, open a popup at
        // the location of the feature, with
        // description HTML from its properties.
        map.on('click', 'unclustered-point', function (e) {
            const coordinates = e.features[0].geometry.coordinates.slice();
            const title = e.features[0].properties.title;
            const link = e.features[0].properties.link;

            // Ensure that if the map is zoomed out such that
            // multiple copies of the feature are visible, the
            // popup appears over the copy being pointed to.
            while (Math.abs(e.lngLat.lng - coordinates[0]) > 180) {
                coordinates[0] += e.lngLat.lng > coordinates[0] ? 360 : -360;
            }

            new mapboxgl.Popup()
                .setLngLat(coordinates)
                .setHTML(
                    'Title: ' + title + '<br><a href="' + link + '">' + title + '</a>'
                )
                .addTo(map);
        });

        map.on('mouseenter', 'clusters', function () {
            map.getCanvas().style.cursor = 'pointer';
        });

        map.on('mouseleave', 'clusters', function () {
            map.getCanvas().style.cursor = '';
        });
    });

    document.getElementById('buttons').addEventListener('click', function (event) {
        const language = event.target.id.substr('button-'.length);
        // Use setLayoutProperty to set the value of a layout property in a style layer.
        // The three arguments are the id of the layer, the name of the layout property,
        // and the new property value.
        map.setLayoutProperty('country-label', 'text-field', [
            'get',
            'name_' + language
        ]);
    });

    filterInput.addEventListener('keyup', function (e) {
        // If the input value matches a layerID set
        // it's visibility to 'visible' or else hide it.
        const value = e.target.value.trim().toLowerCase();
        layerIDs.forEach(function (layerID) {
            map.setLayoutProperty(
                layerID,
                'visibility',
                layerID.indexOf(value) > -1 ? 'visible' : 'none'
            );
        });
    });
</script>
</html>