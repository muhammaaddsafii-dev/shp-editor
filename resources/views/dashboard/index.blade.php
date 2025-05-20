@extends('dashboard.layouts.master')

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
                        <div class="map">
                            <span>Alternative content for the map</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
