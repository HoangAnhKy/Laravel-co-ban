@extends('template.Master')
@push('css')
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.11.5/b-2.2.2/b-colvis-2.2.2/b-html5-2.2.2/b-print-2.2.2/date-1.1.2/fc-4.0.2/fh-3.2.2/r-2.2.9/rg-1.1.4/sc-2.0.5/sl-1.3.4/datatables.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>

@endpush
@section('body')
    <h1>Danh sách sinh viên</h1>

    @if(session()->has('success'))
        <div class="alert alert-success">
            <span>{{session()->get('success')}}</span>
        </div>
    @endif
    <div>
        <select class="form-control" id="Search_name"> </select>
        <select class="form-control" id="Search_status">
            <option value="0">Tất cả</option>
            @foreach($arrEnum as  $option => $key)
                <option value="{{ $key }}">{{$option}}</option>
            @endforeach
        </select>
    </div>
    <table class="table table-striped" id="table_student">
        <thead>
        <tr>
            <th>#</th>
            <th>Tên Sinh viên</th>
            <th>Tên Lớp</th>
            <th>Trạng thái</th>
            <th>Ngày thêm</th>
            <th>Edit</th>
        </tr>
        </thead>
    </table>
@endsection
@push('js')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript"
            src="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.11.5/b-2.2.2/b-colvis-2.2.2/b-html5-2.2.2/b-print-2.2.2/date-1.1.2/fc-4.0.2/fh-3.2.2/r-2.2.9/rg-1.1.4/sc-2.0.5/sl-1.3.4/datatables.min.js"></script>

    <script>

        $(function () {
            $("#Search_name").select2({
                ajax: {
                    url: "{{route('course.apiName')}}",
                    dataType: 'json',
                    data: function (params) {
                        return {
                            q: params.term,
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;

                        return {
                            results: $.map(data, function (item) {
                                return {
                                    text: item.name,
                                    id: item.id
                                }
                            })
                        };
                    },
                    cache: true
                },
                placeholder: 'Tìm kiếm theo tên',
                allowClear: true,
            });

            var buttonCommon = {
                exportOptions: {
                    columns: ':visible :not(.no-select)'
                }
            };
            let colummEdit = 5;

            var table = $('#table_student').DataTable({
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
                processing: true,
                serverSide: true,
                ajax: '{!! route('student.api') !!}',
                columnDefs: [
                    {className: "no-select", "targets": [colummEdit]}
                ],
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name'},
                    {data: 'NameClass', name: 'NameClass'},
                    {data: 'status', name: 'status'},
                    {data: 'created_at', name: 'created_at'},
                    {
                        data: 'edit', name: 'edit', target: colummEdit, searchable: false, orderable: false,
                        render: function (data, type, row, meta) {
                            return `<a class="btn btn-primary" href="${data}">Sửa </a>`;
                        }
                    },
                ]
            });
            $('#Search_name').change(function () {
                table.column(2).search(this.value).draw();
            });
            $('#Search_status').change(function () {
                table.column(3).search(this.value).draw();
            });
        });
    </script>
@endpush