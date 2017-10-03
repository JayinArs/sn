@extends('layouts.app')

@section('top')
    <a href="{{ $is_system ? route('event.system.create') : route('event.create') }}" class="btn btn-success">Add New</a>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body pb0">
                    <div class="table-responsive table-bordered mb-lg">
                        <table class="table table-striped table-bordered table-bordered-force" id="events-table"
                               style="width: 100%;">
                            <thead>
                            <tr>
                                <th>Event ID#</th>
                                <th>Event Date</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Circle</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('modals')
    @include('layouts.delete-modal')
@endsection

@push('scripts')
<script>
    $(function () {
        var $events_table = $('#events-table').DataTable({
            responsive: true,
            errMode: 'throw',
            ajax: '{{ ($is_system ? route('event.system.data') : route('event.data')) }}',
            fnInitComplete: function (settings) {

            },
            columns: [
                {
                    name: 'id',
                    data: function (row, type, val, meta) {
                        return row.id;
                    }
                },
                {
                    name: 'date',
                    data: function(row) {
                        var d = (row.hijri_date ? window.custom.parse_hij_date(row.hijri_date) : '');
                        d += (row.english_date ? window.custom.parse_eng_date(row.english_date) : '');

                        return d;
                    }
                },
                {
                    name: 'title',
                    data: 'title'
                },
                {
                    name: 'category.name',
                    data: function(row) {
                        return (row.category ? row.category.name : 'No category chosen');
                    }
                },
                {
                    name: 'organization_location.organization',
                    data: function(row) {
                        if(row.organization_location && row.organization_location.organization) {
                            return row.organization_location.organization.name + ', ' + row.organization_location.country;
                        }

                        return 'No organization';
                    }
                },
                {
                    bSortable: false,
                    name: 'action',
                    data: function (row) {
                        return '<div class="btn-group table-actions">' +
                            '<a data-id="' + row.id + '" data-action="delete" href="" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>' +
                            '<a data-id="' + row.id + '" data-action="detail" href="' + window.custom.url + '/event{{ $is_system ? '/system' : '' }}/' + row.id + '/edit" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i></a>' +
                            '</div>';
                    }
                }
            ],
            dom: 'lTfgitp',
            buttons: [
                {
                    extend: 'pdf',
                    text: 'PDF',
                }, {
                    extend: 'csv',
                    text: 'CSV'
                }, {
                    extend: 'print',
                    text: 'Print'
                }
            ]
        });
        var $delete_modal = $('#delete-modal');
        var $delete_modal_ids = $('#delete-modal-ids');

        $(document).on('click', '.table-actions a[data-action]', function (e) {
            e.preventDefault();

            $btn = $(this);
            $action = $btn.data('action');
            $id = $btn.data('id');

            var data = $events_table.row($btn.parents('tr')).data();

            if ($action === 'delete') {
                $delete_modal.on('show.bs.modal', function () {
                    $delete_modal.find('#delete-modal-message').html('Are you sure to delete this event?');
                    $delete_modal_ids.val(data['id']);
                });
                $delete_modal.modal('show');
            } else if ($action === 'detail') {
                window.location.href = window.custom.url + '/event{{ $is_system ? '/system' : '' }}/' + data['id'] + '/edit';
            }
        });

        $delete_modal.on('click', '#delete-modal-confirmed', function () {

            $delete_modal.modal('hide');
            $.notify(window.custom.messages.processing);

            $.ajax({
                type: 'DELETE',
                url: '{{ url("admin/event") }}/' + $delete_modal_ids.val(),
                success: function (data) {
                    $.notify.closeAll();
                    $.notify(data);
                },
                error: function (jqXHR, textStatus) {
                    $.notify.closeAll();
                    $.notify(window.custom.messages.internal_error);
                },
                complete: function (jqXHR) {
                    $events_table.ajax.reload();
                }
            });
        });
    });
</script>
@endpush