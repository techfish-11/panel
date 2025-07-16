@extends('layouts.admin')

@section('title')
    サーバーテンプレート一覧
@endsection

@section('content')
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">サーバーテンプレート一覧</h3>
    </div>
    <div class="box-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>テンプレート名</th>
                    <th>説明</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach($templates as $template)
                <tr>
                    <td>{{ $template['name'] }}</td>
                    <td>{{ $template['description'] }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.servers.templates.create', ['egg' => $template['id']]) }}">
                            @csrf
                            <select name="owner_id" class="form-control" required>
                                <option value="">オーナー選択</option>
                                @foreach($owners as $owner)
                                    <option value="{{ $owner->id }}">{{ $owner->name }} ({{ $owner->email }})</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-success btn-sm" style="margin-top:5px;">このテンプレートでサーバー作成</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
