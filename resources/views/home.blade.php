@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-3 col-sm-6">
            <div class="panel widget bg-primary">
                <div class="row row-table">
                    <div class="col-xs-4 text-center bg-primary-dark pv-lg">
                        <em class="icon-screen-smartphone fa-3x"></em>
                    </div>
                    <div class="col-xs-8 pv-lg">
                        <div class="h2 mt0">{{ $users }}</div>
                        <div class="text-uppercase">Devices</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="panel widget bg-purple">
                <div class="row row-table">
                    <div class="col-xs-4 text-center bg-purple-dark pv-lg">
                        <em class="icon-globe fa-3x"></em>
                    </div>
                    <div class="col-xs-8 pv-lg">
                        <div class="h2 mt0">{{ $organizations }}</div>
                        <div class="text-uppercase">Circles</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="panel widget bg-success">
                <div class="row row-table">
                    <div class="col-xs-4 text-center bg-success-dark pv-lg">
                        <em class="icon-notebook fa-3x"></em>
                    </div>
                    <div class="col-xs-8 pv-lg">
                        <div class="h2 mt0">{{ $events }}</div>
                        <div class="text-uppercase">Events</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="panel widget bg-warning">
                <div class="row row-table">
                    <div class="col-xs-4 text-center bg-warning-dark pv-lg">
                        <em class="icon-globe-alt fa-3x"></em>
                    </div>
                    <div class="col-xs-8 pv-lg">
                        <div class="h2 mt0">{{ $locations }}</div>
                        <div class="text-uppercase">Locations</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
