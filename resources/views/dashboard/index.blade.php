@extends('dashboard.layouts.master')

@section('css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css">

    <style>
        #map {
            height: calc(100vh - 56px);
            width: 100%;
            margin: 0;
        }
    </style>
@endsection

@section('content')
    <h4 class="page-title">Dashboard</h4>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">World Map</h4>
                    <p class="card-category">
                        Map of the distribution of users around the world</p>
                </div>
                <div class="card-body">
                    <div class="mapcontainer">
                        <div id="map"></div>

                        <!-- Modal Create Point -->
                        <div class="modal fade" id="PointModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="PointModalLabel">Create Point</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('store-point') }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Name</label>
                                                <input type="text" class="form-control" id="name" name="name"
                                                    placeholder="Fill Point Name">
                                            </div>
                                            <div class="mb-3">
                                                <label for="description" class="form-label">Description</label>
                                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label for="geom" class="form-label">Geometry</label>
                                                <textarea class="form-control" id="geom_point" name="geom" rows="3" readonly></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label for="image" class="form-label">Image</label>
                                                <input type="file" class="form-control"
                                                    onchange="document.getElementById('preview-image-point').src= window.URL.createObjectURL(this.files[0])"
                                                    id="image_point" name="image">
                                            </div>
                                            <div class="mb3">
                                                <img src="" alt="Preview" id="preview-image-point"
                                                    class="img-thumbnail" width="400">
                                            </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="Submit" class="btn btn-primary">Save</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Create Polyline-->
                        <div class="modal fade" id="PolylineModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="PolylineModalLabel">Create Polyline </h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('store-polyline') }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Name</label>
                                                <input type="text" class="form-control" id="name" name="name"
                                                    placeholder="Fill Point Name">
                                            </div>
                                            <div class="mb-3">
                                                <label for="description" class="form-label">Description</label>
                                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label for="geom" class="form-label">Geometry</label>
                                                <textarea class="form-control" id="geom_polyline" name="geom" rows="3" readonly></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label for="image" class="form-label">Image</label>
                                                <input type="file" class="form-control"
                                                    onchange="document.getElementById('preview-image-polyline').src= window.URL.createObjectURL(this.files[0])"
                                                    id="image_polyline" name="image">
                                            </div>
                                            <div class="mb3">
                                                <img src="" alt="Preview" id="preview-image-polyline"
                                                    class="img-thumbnail" width="400">
                                            </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="Submit" class="btn btn-primary">Save</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Modal Create Polygon-->
                        <div class="modal fade" id="PolygonModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="PolygonModalLabel">Create Polygon </h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('store-polygon') }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Name</label>
                                                <input type="text" class="form-control" id="name" name="name"
                                                    placeholder="Fill Point Name">
                                            </div>
                                            <div class="mb-3">
                                                <label for="description" class="form-label">Description</label>
                                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label for="geom" class="form-label">Geometry</label>
                                                <textarea class="form-control" id="geom_polygon" name="geom" rows="3" readonly></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label for="image" class="form-label">Image</label>
                                                <input type="file" class="form-control"
                                                    onchange="document.getElementById('preview-image-polygon').src= window.URL.createObjectURL(this.files[0])"
                                                    id="image_polygon" name="image">
                                            </div>
                                            <div class="mb3">
                                                <img src="" alt="Preview" id="preview-image-polygon"
                                                    class="img-thumbnail" width="400">
                                            </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="Submit" class="btn btn-primary">Save</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
    <script src="https://unpkg.com/terraformer@1.0.7/terraformer.js"></script>
    <script src="https://unpkg.com/terraformer-wkt-parser@1.1.2/terraformer-wkt-parser.js"></script>

    <script>
        var map = L.map('map').setView([2.360558923242346, 120.82740596049541], 5);

        //Basemap
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        /* Digitize Function */
        var drawnItems = new L.FeatureGroup();
        map.addLayer(drawnItems);

        var drawControl = new L.Control.Draw({
            draw: {
                position: 'topleft',
                polyline: true,
                polygon: true,
                rectangle: true,
                circle: false,
                marker: true,
                circlemarker: false
            },
            edit: {
                featureGroup: drawnItems,
                remove: true
            }
        });

        map.addControl(drawControl);

        map.on('draw:created', function(e) {
            var type = e.layerType,
                layer = e.layer;

            console.log(type);

            var drawnJSONObject = layer.toGeoJSON();
            var objectGeometry = Terraformer.WKT.convert(drawnJSONObject.geometry);

            console.log(drawnJSONObject);
            console.log(objectGeometry);

            if (type === 'polyline') {
                // set value geometry to input geom
                $("#geom_polyline").val(objectGeometry);

                // show modal
                $("#PolylineModal").modal('show');
            } else if (type === 'polygon' || type === 'rectangle') {
                // set value geometry to input geom
                $("#geom_polygon").val(objectGeometry);

                // show modal
                $("#PolygonModal").modal('show');
            } else if (type === 'marker') {
                // set value geometry to input geom
                $("#geom_point").val(objectGeometry);

                // show modal
                $("#PointModal").modal('show');
            } else {
                console.log('_undefined_');
            }

            drawnItems.addLayer(layer);
        });

        /* GeoJSON Point */
        var point = L.geoJson(null, {
            onEachFeature: function(feature, layer) {
                var popupContent = "Nama: " + feature.properties.name + "<br>" +
                    "Deskripsi: " + feature.properties.description + "<br>" +
                    "Foto: <img src="+feature.properties.image+" class='img-thumbnail' alt=''>" +
                    "<br>" +
                    "<div class='d-flex flex-row mt-3'>" +
                    "<a href='{{ url('edit-point') }}/" + feature.properties.id +
                    "' class='btn btn-sm btn-warning me-2'><i class='fa-solid fa-edit'>EDIT</i></a>" +

                    "<form action='{{ url('delete-point') }}/" + feature.properties.id + "' method='POST'>" +
                    '{{ csrf_field() }}' +
                    '{{ method_field('DELETE') }}' +
                    "<button type='submit' class='btn btn-danger ' onclick='confirm(`Yakin Menghapus Data Ini?`)'><i class='fa-solid fa-trash'>DELETE</i></button>" +
                    "</form>" +
                    "</div>";

                layer.on({
                    click: function(e) {
                        point.bindPopup(popupContent);
                    },
                    mouseover: function(e) {
                        point.bindTooltip(feature.properties.name);
                    },
                });
            },
        });
        $.getJSON("{{ route('api.points') }}", function(data) {
            point.addData(data);
            map.addLayer(point);
        });

        /* GeoJSON Polygons */
        var polygons = L.geoJson(null, {
            onEachFeature: function(feature, layer) {
                var popupContent = "Nama: " + feature.properties.name + "<br>" +
                    "Deskripsi: " + feature.properties.description + "<br>" +
                    "Foto: <img src="+feature.properties.image+" class='img-thumbnail' alt='...'>" + "<br />" +
                    "<div class='d-flex flex-row mt-3'>" +
                    "<a href='{{ url('edit-polygon') }}/" + feature.properties.id +
                    "' class='btn btn-sm btn-warning me-2'><i class='fa-solid fa-edit'>EDIT</i></a>" +
                    +
                    "<form action='{{ url('delete-polygon') }}/" + feature.properties.id + "' method='POST'>" +
                    '{{ csrf_field() }}' +
                    '{{ method_field('DELETE') }}' +
                    "<button type='submit' class='btn btn-danger' onclick='confirm(`Yakin Menghapus Data Ini?`)'><i class='fa-solid fa-trash'>DELETE</i></button>" +
                    "</form>" +
                    "</div>";;;
                layer.on({
                    click: function(e) {
                        polygon.bindPopup(popupContent);
                    },
                    mouseover: function(e) {
                        polygon.bindTooltip(feature.properties.name);
                    },
                });
            },
        });
        $.getJSON("{{ route('api.polygons') }}", function(data) {
            point.addData(data);
            map.addLayer(polygon);
        });

        /* GeoJSON Polyline */
        var polyline = L.geoJson(null, {
            onEachFeature: function(feature, layer) {
                var popupContent = "Nama: " + feature.properties.name + "<br>" +
                    "Deskripsi: " + feature.properties.description + "<br>" +
                    "Foto: <img src="+feature.properties.image+" class='img-thumbnail' alt='...'>" + "<br>" +
                    "<div class='d-flex flex-row mt-3'>" +
                    "<a href='{{ url('edit-polyline') }}/" + feature.properties.id +
                    "' class='btn btn-sm btn-warning me-2'><i class='fa-solid fa-edit'>EDIT</i></a>" +

                    "<form action='{{ url('delete-polyline') }}/" + feature.properties.id +
                    "' method='POST'>" +
                    '{{ csrf_field() }}' +
                    '{{ method_field('DELETE') }}' +
                    "<button type='submit' class='btn btn-danger' onclick='confirm(`Yakin Menghapus Data Ini?`)'><i class='fa-solid fa-trash'>DELETE</i></button>" +
                    "</div>" +
                    "</form>";;
                layer.on({
                    click: function(e) {
                        polyline.bindPopup(popupContent);
                    },
                    mouseover: function(e) {
                        polyline.bindTooltip(feature.properties.name);
                    },
                });
            },
        });
        $.getJSON("{{ route('api.polylines') }}", function(data) {
            polyline.addData(data);
            map.addLayer(polyline);
        });
    </script>
@endpush
