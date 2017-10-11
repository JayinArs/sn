@extends('layouts.app')

@section('top')
    <a href="{{ ($is_system ? route('event.system.list') : route('event.index')) }}" class="btn btn-danger">Cancel</a>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body pb0">
                    {!! Form::open(['id' => 'event-update-form']) !!}
                    @if($is_system)
                        <fieldset>
                            <div class="form-group">
                                <label class="col-md-2 col-sm-2 text-right control-label">Category:</label>
                                <div class="col-md-10 col-sm-10">
                                    {{ Form::select('category', $categories, $event->category_id, ['class' => 'form-control']) }}
                                </div>
                            </div>
                        </fieldset>
                    @endif
                    <fieldset>
                        <div class="form-group">
                            <label class="col-md-2 col-sm-2 text-right control-label">Title:</label>
                            <div class="col-md-10 col-sm-10">
                                {{ Form::text('title', $event->title, ['class' => 'form-control']) }}
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <div class="form-group">
                            <label class="col-md-2 col-sm-2 text-right control-label">Hijri Date:</label>
                            <div class="col-md-10 col-sm-10">
                                {!! Hijri::get_field($event->hijri_date, false, false, false, false) !!}
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <div class="form-group">
                            <label class="col-md-2 col-sm-2 text-right control-label">Recurring Event:</label>
                            <div class="col-md-10 col-sm-10">
                                <label class="switch">
                                    {!! Form::checkbox( 'is_recurring', true, $event->is_recurring, [ 'id' => 'cb_is_recurring' ] ) !!}
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset id="recurring-type" style="display: {!! $event->is_recurring ? "block" : "none" !!};">
                        <div class="form-group">
                            <label class="col-md-2 col-sm-2 text-right control-label">Recurring Type:</label>
                            <div class="col-md-10 col-sm-10">
                                {!! Form::select( 'recurring_type', $recurring_types, (isset($event->meta_data['recurring_type']) ? $event->meta_data['recurring_type'] : false), [ 'class' => 'form-control' ] ) !!}
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
        $main_form = $('#event-update-form');
        $main_form.submit(function () {

            $.notify(window.custom.messages.processing);

            $.ajax({
                url: '{{ route('event.system.update', $event->id) }}',
                type: 'PUT',
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

        $("#cb_is_recurring").on("change", function () {
            if ($(this).is(":checked"))
                $("fieldset#recurring-type").slideDown();
            else
                $("fieldset#recurring-type").slideUp();
        });
    });
</script>
@endpush