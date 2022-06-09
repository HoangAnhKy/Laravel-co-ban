@extends('template.Master')
@push('css')
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.11.5/b-2.2.2/b-colvis-2.2.2/b-html5-2.2.2/b-print-2.2.2/date-1.1.2/fc-4.0.2/fh-3.2.2/r-2.2.9/rg-1.1.4/sc-2.0.5/sl-1.3.4/datatables.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
@endpush
@section('body_text')
    <div>
        <a class="btn btn-success " href="{{route('course.create')}}">Thêm Sinh Viên</a>
        <div class="form-select">
            <select id="select-table" style="width:100%;"> </select>
        </div>
        <table width="100%" border="1" class="table table-striped" id="table-index">
            <thead>
            <tr>
                <th>#</th>
                <th>Tên</th>
                <th>Thời gian tạo</th>
                <th>Số lượng sinh viên</th>
                <th>Sửa</th>
                <th>Xóa</th>
            </tr>
            </thead>
        </table>
    </div>
@endsection

@push('js')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript"
            src="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.11.5/b-2.2.2/b-colvis-2.2.2/b-html5-2.2.2/b-print-2.2.2/date-1.1.2/fc-4.0.2/fh-3.2.2/r-2.2.9/rg-1.1.4/sc-2.0.5/sl-1.3.4/datatables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(function () {
            $("#select-table").select2({
                ajax: {
                    url: "{{route('course.api.name')}}",
                    dataType: 'json',
                    data: function (params) {
                        return {
                            q: params.term,
                        };
                    },
                    processResults: function (data, params) {

                        return {
                            results: $.map(data, function (item) {
                                return {
                                    text: item.name,
                                    id: item.id
                                }
                            })
                        };
                    },
                },
                placeholder: 'Search for a name',

            });

            $('#select-table').change(function () {
                table.column(0).search(this.value).draw();
            });

            let buttonCommon = {
                exportOptions: {
                    columns: ':visible :not(.no-select)',
                }
            }

            var table = $('#table-index').DataTable({
                dom: 'Blfrtip',
                buttons: [
                    $.extend(true, {}, buttonCommon, {
                        extend: 'copyHtml5'
                    }),
                    $.extend(true, {}, buttonCommon, {
                        extend: 'excelHtml5'
                    }),
                    $.extend(true, {}, buttonCommon, {
                        extend: 'pdfHtml5'
                    }), $.extend(true, {}, buttonCommon, {
                        extend: 'csvHtml5'
                    }), $.extend(true, {}, buttonCommon, {
                        extend: 'print'
                    }),
                    'colvis',
                ],
                select: true,
                processing: true,
                serverSide: true,
                ajax: '{!! route('course.api')!!}',
                columnDefs: [
                    {className: "no-select", "targets": [3]},
                    {className: "no-select", "targets": [4]},
                ],
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'student_count', name: 'student_count'},
                    {
                        data: 'edit', target: 3, orderable: false, searchable: false,
                        render: function (data, type, row, meta) {
                            return `<a href="${data}" class="btn btn-success">Sửa</a>`;
                        }
                    },
                    {
                        data: 'delete', target: 4, orderable: false, searchable: false,
                        render: function (data, type, row, meta) {
                            return ` <form action="${data}" method="post">
                            @csrf
                            @method('delete')
                            <button type="button" class="btn-delete btn btn-danger">Xóa</button>
                        </form>`;
                        }
                    },
                ]
            });

            $(document).on('click', '.btn-delete', function () {
                let form = $(this).parents('form');
                $.ajax({
                    url: form.attr('action'),
                    method: 'post',
                    datatype: 'json',
                    data: form.serialize(),
                    success: function () {
                        console.log('success');
                        table.draw();
                    },
                    error: function () {
                        console.log('error');
                    }
                })
            })
        });
    </script>
@endpush
