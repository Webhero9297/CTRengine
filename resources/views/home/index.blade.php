@extends('layouts.app')

@section('content')
<div class="container">
    <a href="{{ route('feemng') }}" class="btn btn-success">Edit Fee</a>
    <a href="{{ route('ordermarket') }}" class="btn btn-success">Order Exchange</a>
</div>
@endsection