@extends('layouts.app')

@section('top')
    <a href="{{ route('org.index') }}" class="btn btn-danger">Cancel</a>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body pb0">
                    {!! Form::open(['id' => 'org-creation-form']) !!}
                    <fieldset>
                        <div class="form-group">
                            <label class="col-md-1 col-sm-2 control-label">Associated Account:</label>
                            <div class="col-md-11 col-sm-10">
                                {{ Form::select('account_id', $accounts, false, ['class' => 'form-control chosen-select']) }}
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <div class="form-group">
                            <label class="col-md-1 col-sm-2 control-label">Name:</label>
                            <div class="col-md-11 col-sm-10">
                                {{ Form::text('name', false, ['class' => 'form-control']) }}
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <div class="form-group">
                            <label class="col-md-1 col-sm-2 control-label">Official:</label>
                            <div class="col-md-11 col-sm-10">
                                <label class="switch">
                                    <input name="is_official" type="checkbox"/>
                                    <span></span>
                                </label>
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
        $main_form = $('#org-creation-form');
        $main_form.submit(function () {

            $.notify(window.custom.messages.processing);

            $.ajax({
                url: '{{ route('org.store') }}',
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