@extends('layouts.app')

@section('top')
    <a href="{{ $is_system ? route('event.system.list') : route('event.index') }}" class="btn btn-danger">Cancel</a>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body pb0">
                    {!! Form::open(['id' => 'event-creation-form']) !!}
                    @if($is_system)
                        <fieldset>
                            <div class="form-group">
                                <label class="col-md-2 col-sm-2 text-right control-label">Category:</label>
                                <div class="col-md-10 col-sm-10">
                                    {{ Form::select('category', $categories, false, ['class' => 'form-control']) }}
                                </div>
                            </div>
                        </fieldset>
                    @endif
                    <fieldset>
                        <div class="form-group">
                            <label class="col-md-2 col-sm-2 text-right control-label">Title:</label>
                            <div class="col-md-10 col-sm-10">
                                {{ Form::text('title', false, ['class' => 'form-control']) }}
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <div class="form-group">
                            <label class="col-md-2 col-sm-2 text-right control-label">Hijri Date:</label>
                            <div class="col-md-10 col-sm-10">
                                {!! Hijri::get_field(false, false, false, false, false) !!}
                            </div>
                        </div>
                    </fieldset>
                    @if(!$is_system)
                        <fieldset>
                            <div class="form-group">
                                <label class="col-md-2 col-sm-2 text-right control-label">Organization:</label>
                                <div class="col-md-10 col-sm-10">
                                    {!! Form::select( 'organization_id', $organizations, false, [ 'id' => 'select-organization', 'class' => 'form-control' ] ) !!}
                                </div>
                            </div>
                        </fieldset>
                        <fieldset>
                            <div class="form-group">
                                <label class="col-md-2 col-sm-2 text-right control-label">Organization Location:</label>
                                <div class="col-md-10 col-sm-10">
                                    {!! Form::select( 'organization_location_id', [], false, [ 'id' => 'select-organization-location', 'class' => 'form-control', 'disabled' => 'disabled' ] ) !!}
                                </div>
                            </div>
                        </fieldset>
                        <fieldset>
                            <div class="form-group">
                                <label class="col-md-2 col-sm-2 text-right control-label">Follow Hijri Date:</label>
                                <div class="col-md-10 col-sm-10">
                                    <label class="switch">
                                        {!! Form::checkbox( 'is_hijri_date', true, true, [ 'id' => 'cb_is_hijri_date' ] ) !!}
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset id="english-date" style="display: none;">
                            <div class="form-group">
                                <label class="col-md-2 col-sm-2 text-right control-label">English Date:</label>
                                <div class="col-md-10 col-sm-10">
                                    <div class="row">
                                        <div class="col-md-4">
                                            {!! Form::text( 'date[year]', false, ['class' => 'form-control', 'placeholder' => 'Year'] ) !!}
                                        </div>
                                        <div class="col-md-4">
                                            {!! Form::select( 'date[month]', $months, false, [ 'class' => 'form-control' ] ) !!}
                                        </div>
                                        <div class="col-md-4">
                                            {!! Form::select( 'date[day]', $days, false, [ 'class' => 'form-control' ] ) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset>
                            <div class="form-group">
                                <label class="col-md-2 col-sm-2 text-right control-label">Time:</label>
                                <div class="col-md-10 col-sm-10">
                                    {!! Form::text( 'time', false, ['class' => 'form-control', 'placeholder' => '00:00'] ) !!}
                                </div>
                            </div>
                        </fieldset>
                        <fieldset>
                            <div class="form-group">
                                <label class="col-md-2 col-sm-2 text-right control-label">Venue:</label>
                                <div class="col-md-10 col-sm-10">
                                    {!! Form::text( 'venue', false, ['class' => 'form-control', 'placeholder' => 'Place name'] ) !!}
                                </div>
                            </div>
                        </fieldset>
                    @endif
                    <fieldset>
                        <div class="form-group">
                            <label class="col-md-2 col-sm-2 text-right control-label">Recurring Event:</label>
                            <div class="col-md-10 col-sm-10">
                                <label class="switch">
                                    {!! Form::checkbox( 'is_recurring', true, false, [ 'id' => 'cb_is_recurring' ] ) !!}
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset id="recurring-type" style="display: none;">
                        <div class="form-group">
                            <label class="col-md-2 col-sm-2 text-right control-label">Recurring Type:</label>
                            <div class="col-md-10 col-sm-10">
                                {!! Form::select( 'recurring_type', $recurring_types, false, [ 'class' => 'form-control' ] ) !!}
                            </div>
                        </div>
                    </fieldset>
                    <div class="form-group text-right">
                        <div class="col-md-12">
                            <input type="submit" class="btn btn-success" value="Publish"/>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    {!! Form::close() !!}
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    jQuery(function ($) {
        $main_form = $('#event-creation-form');
        $main_form.submit(function () {

            $.notify(window.custom.messages.processing);

            $.ajax({
                url: '{{ ($is_system ? route('event.system.store') : route('event.store')) }}',
                type: 'POST',
                dataType: 'JSON',
                data: $main_form.serializeArray(),
                success: function (data) {
                    $.notify.closeAll();
                    $.notify(data);
                },
                error: function (e) {
                    $.notify.closeAll();
                    $.notify(window.custom.messages.internal_error);
                }
            });

            return false;
        });

        $("#cb_is_hijri_date").on("change", function () {
            if ($(this).is(":checked"))
                $("fieldset#english-date").slideUp();
            else
                $("fieldset#english-date").slideDown();
        });

        $("#cb_is_recurring").on("change", function () {
            if ($(this).is(":checked"))
                $("fieldset#recurring-type").slideDown();
            else
                $("fieldset#recurring-type").slideUp();
        });

        $("#select-organization").on("change", function () {
            var $organization_id = $(this).val();
            var $selector = $("#select-organization-location");

            $selector.attr("disabled", "disabled");
            $selector.empty();
            $selector.append('<option value="">Loading...</option>');

            $.ajax({
                url: '{{ route("organization.locations", "%%") }}'.replace('%%', $organization_id),
                type: 'GET',
                dataType: 'JSON',
                success: function (data) {
                    $selector.empty();

                    data.map(function (location) {
                        $selector.append('<option value="' + location.id + '">' + location.city + ', ' + location.country + '</option>');
                    });

                    $selector.removeAttr("disabled");
                },
                error: function (e) {
                    console.log(e);
                    $selector.attr("disabled", "disabled");
                }
            });
        });
    });
</script>
@endpush