@php
    use App\Helpers\CommonHelper;
@endphp
@extends('layouts.layouts')

@section('content')
    @include($page . 'create',['pageOptionType' => $pageOptionType])
@endsection

