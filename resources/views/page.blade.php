@extends('layouts.app')

{{--@section('title', $meta_title->raw() ? $meta_title .' - '. config('app.name') : config('app.name'))--}}
{{--@section('meta_description', (isset($meta_description) && !empty($meta_description->raw())) ? $meta_description : config('app.name'))--}}

@section('content')
    @include('statamic-page-builder-kit::page_builder')
@endsection
