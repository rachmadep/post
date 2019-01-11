@extends('layouts.app')

@section('content-title', ucwords(__('users.plural')))

@include('generator::components.models.edit', [
  'panel_title' => ucwords(__('users.singular')),
  'resource_route' => 'users',
  'model_variable' => 'user',
  'model' => $user
])
