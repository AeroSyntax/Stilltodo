@extends('layouts.app')

@section('content')
    <h1>Every user Todo</h1>
    {!! Form::open(['action' => 'TodosController@search', 'method' => 'POST']) !!}
        <div class="form-group">
            {{Form::text('key', '', ['class' => 'form-control', 'placeholder' => 'Search in todos'])}}
        </div>
        {{Form::submit('Submit', ['class'=>'btn btn-primary'])}}
    {!! Form::close() !!}

    @if(count($todos) > 0)
        @foreach($todos as $todo)
            <div class="card card-body bg-light">
                <div class="row">
                    <div>
                        <h3><a href="/todos/{{$todo->id}}">{{$todo->title}}</a></h3>
                        <small>Posted {{$todo->created_at->diffForHumans()}} by {{$todo->user->name}}</small>
                        @if($todo->fulfilled)
                            <br>
                            <small>Completed: {{$todo->fulfilled_at}}</small>
                        @endif
                        
                        
                    </div>
                </div>
            </div>
        @endforeach
        {{$todos->links()}}
    @else
        <p>No todos found</p>
    @endif
@endsection