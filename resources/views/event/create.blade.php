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
                                <label class="col-md-1 col-sm-2 control-label">Category:</label>
                                <div class="col-md-11 col-sm-10">
                                    {{ Form::select('category', $categories, false, ['class' => 'form-control']) }}
                                </div>
                            </div>
                        </fieldset>
                    @endif
                    <fieldset>
                        <div class="form-group">
                            <label class="col-md-1 col-sm-2 control-label">Title:</label>
                            <div class="col-md-11 col-sm-10">
                                {{ Form::text('title', false, ['class' => 'form-control']) }}
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <div class="form-group">
                            <label class="col-md-1 col-sm-2 control-label">Hijri Date:</label>
                            <div class="col-md-11 col-sm-10">
                                {!! Hijri::get_field() !!}
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
    });
</script>
@endpush