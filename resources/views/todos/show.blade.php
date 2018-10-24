@extends('layouts.app')

@section('content')
    <a href="/todos" class="btn btn-secondary float-right">Back</a>
    <h1>{{$todo->title}}</h1>
        
    <div>
        {!!$todo->description!!}
    </div>
    <hr>
    <div>
        @if($todo->fulfilled)
            <small>Fulfilled on {{$todo->fulfilled_at}}</small>
        @else
            <small>Ongoing todo</small>
        @endif
    </div>

    <div>
        @if($todo->file_name != '')
            <a href="/storage/user_uploads/{{$todo->file_name}}" class="btn btn-primary" target="_blank">Open file </a><small> ({{$todo->file_name}})</small>
        @endif
    </div>
    

    <hr>
    <small>{{$todo->created_at->diffForHumans()}} by {{$todo->user->name}}</small>
    <hr>
    @if(!Auth::guest())
        @if(Auth::user()->id == $todo->user_id)
            <a href="/todos/{{$todo->id}}/edit" class="btn btn-primary">Edit</a>

            {!!Form::open(['action' => ['TodosController@destroy', $todo->id], 'method' => 'POST', 'class' => 'float-right'])!!}
                {{Form::hidden('_method', 'DELETE')}}
                {{Form::submit('Delete', ['class' => 'btn btn-danger'])}}
            {!!Form::close()!!}
        @endif
    @endif
@endsection