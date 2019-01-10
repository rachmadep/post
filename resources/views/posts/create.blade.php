@extends('layouts.app')

@section('content-title', ucwords(__('posts.plural')))

@include('generator::components.models.create', [
  'panel_title' => ucwords(__('posts.singular')),
  'resource_route' => 'posts',
  'model_variable' => 'post'
])
