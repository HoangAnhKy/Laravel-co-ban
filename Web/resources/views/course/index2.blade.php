@extends('template.Master')
@section('body_text')
    <div>
        <a class="btn btn-success " href="{{route('course.create')}}">Thêm Sinh Viên</a>
        <table width="100%" border="1" class="table table-striped">
            <tr>
                <th>#</th>
                <th>Tên</th>
                <th>Thời gian tạo</th>
                <th>Sửa</th>
                <th>Xóa</th>
            </tr>
            @foreach($data as $row)
                <tr>
                    <td>{{$row->id}}</td>
                    <td>{{$row->name}}</td>
                    <td>{{$row->GetDay}}</td>
                    <td><a href="{{route('course.edit', $row->id)}}" class="btn btn-success">Sửa</a></td>
                    <td>
                        <form action="{{route('course.delete', $row->id)}}" method="post">
                            @csrf
                            @method('delete')
                            <button class="btn btn-danger">Xóa</button>
                        </form>
                    </td>
                </tr>
            @endforeach

        </table>
    </div>
@endsection
