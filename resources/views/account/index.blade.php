@extends('layouts.app')

@section('top')

@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body pb0">
                    <div class="table-responsive table-bordered mb-lg">
                        <table class="table table-striped table-bordered table-bordered-force" id="accounts-table"
                               style="width: 100%;">
                            <thead>
                            <tr>
                                <th>Account ID#</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Registration</th>
                                <th>Role</th>
                                <th>Users</th>
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
        var $users_table = $('#accounts-table').DataTable({
            responsive: true,
            errMode: 'throw',
            ajax: '{{ route("account.data") }}',
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
                    name: 'email',
                    data: 'email'
                },
                {
                    name: 'status',
                    data: 'status'
                },
                {
                    name: 'registration_date',
                    data: 'registration_date'
                },
                {
                    name: 'role_name',
                    data: 'role.name'
                },
                {
                    name: 'users',
                    data: function(row) {
                        return row.users.length;
                    }
                },
                {
                    bSortable: false,
                    name: 'action',
                    data: function (row) {
                        return '<div class="btn-group table-actions">' +
                            '<a data-id="' + row.id + '" data-action="delete" href="" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>' +
                            '<a data-id="' + row.id + '" data-action="notify" href="'+window.custom.url+'/notify/'+row.id+'" class="btn btn-primary btn-sm"><i class="icons icon-cursor"></i></a>' +
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

            var data = $users_table.row($btn.parents('tr')).data();

            if ($action === 'delete') {
                $delete_modal.on('show.bs.modal', function () {
                    $delete_modal.find('#delete-modal-message').html('Are you sure to delete this account?');
                    $delete_modal_ids.val(data['id']);
                });
                $delete_modal.modal('show');
            } else if ($action === 'detail') {
                window.location.href = window.custom.url + '/account/' + data['id'] + '/edit';
            }
        });

        $delete_modal.on('click', '#delete-modal-confirmed', function () {

            $delete_modal.modal('hide');
            $.notify(window.custom.messages.processing);

            $.ajax({
                type: 'DELETE',
                url: '{{ url("admin/account") }}/' + $delete_modal_ids.val(),
                success: function (data) {
                    $.notify.closeAll();
                    $.notify(data);
                },
                error: function (jqXHR, textStatus) {
                    $.notify.closeAll();
                    $.notify(window.custom.messages.internal_error);
                },
                complete: function (jqXHR) {
                    $users_table.ajax.reload();
                }
            });
        });
    });
</script>
@endpush