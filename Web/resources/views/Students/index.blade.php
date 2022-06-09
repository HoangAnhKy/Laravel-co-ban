@extends('template.Master')
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.11.5/b-2.2.2/b-colvis-2.2.2/b-html5-2.2.2/b-print-2.2.2/date-1.1.2/fc-4.0.2/fh-3.2.2/r-2.2.9/rg-1.1.4/sc-2.0.5/sl-1.3.4/datatables.min.css"/>
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.11.5/b-2.2.2/b-colvis-2.2.2/b-html5-2.2.2/b-print-2.2.2/date-1.1.2/fc-4.0.2/fh-3.2.2/r-2.2.9/rg-1.1.4/sc-2.0.5/sl-1.3.4/datatables.min.css"/>
@endpush
@section('body_text')
    @if (session()->has('success'))
        <div class="col-12">
            <div class="alert alert-success">
                {{ session()->get('success') }}
            </div>
        </div>
    @endif

    <div>
        <a href="{{ route('student.create') }}" class="btn btn-success"> Thêm mới sinh viên</a>
        <div class="form-select">
            <select id="select-table" class="form-control"> </select>
        </div>
        <div class="form-select">
            <select id="select-status" class="form-control">
                <option value="-1">Tất cả</option>
                @foreach($arrEnum as  $option => $key)
                    <option value="{{ $key }}">{{$option}}</option>
                @endforeach
            </select>
        </div>
        <table class="table table-striped" id="student_table" width="100%">
            <thead>
            <tr>
                <th>#</th>
                <th>Tên</th>
                <th>Giới tính</th>
                <th>Ngày sinh</th>
                <th>Trạng thái</th>
                <th>Lớp học</th>
                <th>Ngày thêm</th>
                @if (check_level())
                    <th>Sửa</th>
                    <th>Xóa</th>
                @endif
            </tr>
            </thead>
        </table>
    </div>
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

            $("#select-table").select2({
                ajax: {
                    url: "{{ route('course.api.name') }}",
                    dataType: 'json',
                    data: function (params) {
                        return {
                            q: params.term,
                        };
                    },
                    processResults: function (data) {

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

            var buttonCommon = {
                exportOptions: {
                    columns: ':visible :not(.no-select)'
                }
            };

            var table = $('#student_table').DataTable({
                dom: 'Blrtip',
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
                ajax: '{!! route('student.api') !!}',
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name'},
                    {data: 'gender', name: 'gender'},
                    {data: 'birthdate', name: 'birthdate'},
                    {data: 'status', name: 'status'},
                    {data: 'course_id', name: 'course_id'},
                    {data: 'created_at', name: 'created_at'},


                    @if (check_level())
                        {
                            data: 'edit', target: 7, orderable: false, searchable: false,
                            render: function (data, type, row, meta) {
                                return `<a href="${data}" class="btn btn-success">Sửa</a>`;
                            }
                        },
                        {
                            data: 'delete', target: 8, orderable: false, searchable: false,
                            render: function (data, type, row, meta) {
                                return ` <form action="${data}" method="post">
                                @csrf
                                @method('delete')
                                <button type="button" class="btn-delete btn btn-danger">Xóa</button>
                            </form>`;
                            }
                        },
                    @endif
                ]
            });

            $("#select-table").change(function () {
                table.column(5).search(this.value).draw();
                // table.column(5).search($(this).val()).draw();
            })
            $("#select-status").change(function () {
                table.column(4).search(this.value).draw();
                // if(this.value === '-1'){
                //     table.column(4).search('').draw();
                // }else{
                //     table.column(4).search(this.value).draw();
                // }
            })

            $(document).on('click', '.btn-delete', function () {

                let form = $(this).parent('form');
                console.log(form);
                $.ajax({
                    url: form.attr('action'),
                    method: 'post',
                    dataType: 'json',
                    data: form.serialize(),
                    success: function (response) {
                        console.log("success");
                        table.draw();
                    },
                    error: function (response) {
                        console.log("error");
                    }
                })
            });

        });
    </script>
@endpush
