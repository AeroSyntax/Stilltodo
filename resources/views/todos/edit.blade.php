@extends('layouts.app')

@section('content')
    <h1>Edit Todo</h1>
    <a href="/dashboard" class="btn btn-secondary float-right">Back</a>
    {!! Form::open(['action' => ['TodosController@update', $todo->id], 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
        <div class="form-group">
            {{Form::label('title', 'Title')}}
            {{Form::text('title', $todo->title, ['class' => 'form-control', 'placeholder' => 'Title'])}}
        </div>
        <div class="form-group">
            {{Form::label('description', 'Description')}}
            {{Form::textarea('description', $todo->description, ['id' => 'article-ckeditor', 'class' => 'form-control', 'placeholder' => 'Description Text'])}}
        </div>
        <div class="form-group">
            {{Form::file('file_name')}}
            @if($todo->file_name != '')
                <p>Current file: {{$todo->file_name}}</p> 
            @endif
        </div>

        <div class="form-group">
            @if($todo->fulfilled)
                <small>Fulfilled on {{$todo->fulfilled_at}}</small>
            @else
                {{Form::label('fulfilled', 'Fulfilled')}}
                {{Form::checkbox('fulfilled', $todo->fulfilled)}}
            @endif
        </div>
        {{Form::hidden('_method','PUT')}}
        {{Form::submit('Submit', ['class'=>'btn btn-primary'])}}
    {!! Form::close() !!}
@endsection