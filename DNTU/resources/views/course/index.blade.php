@extends('template.Master')
@section('body')
    <h1>Danh sách lớp</h1>
    <a href="{{route('Courses.create')}}" class="btn btn-primary"> Thêm lớp</a>

    @if(session()->has('success'))
        <div class="alert alert-success">
            <span>{{session()->get('success')}}</span>
        </div>
    @endif
    <table class="table table-striped">
        <tr>
            <th>#</th>
            <th>Tên lớp</th>
            <th>Ngày tạo</th>
            <th>Sửa</th>
            <th>Xóa</th>
        </tr>
        @foreach($data as $row)
            <tr>
                <td>{{$row->id}}</td>
                <td>{{$row->name}}</td>
                <td>{{$row->CustomerDay}}</td>
                <td><a href="{{route('Courses.edit', ['Course' => $row->id])}}" class="btn btn-primary"> Sửa</a> </td>
                <td>
                    <form action="{{route('Courses.destroy', ['Course' => $row->id])}}" method="post">
                        @csrf
                        @method('delete')
                        <button class="btn btn-danger">Xóa</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
@endsection