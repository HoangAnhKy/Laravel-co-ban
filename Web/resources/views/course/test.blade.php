@extends('template.Master')
@section('body_text')
    <table class="table table-striped">
        <thead>
        <tr>
            <th>#</th>
            <th>name</th>
            <th>created_at</th>
        </tr>
        @foreach ($data as $row)
            <tr>
                <td>{{ $row->id }}</td>
                <td>{{ $row->name }}</td>
                <td>{{ $row->getDay }}</td>
            </tr>
        @endforeach
        </thead>
    </table>
    <div class='pagination'>
        {{ $data->links() }}
    </div>
@endsection
