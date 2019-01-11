@extends('layouts.app')

@section('content-title', ucwords(__('users.plural')))

@include('generator::components.models.index', [
  'col_class' => 'col-md-8 col-md-offset-2 offset-md-2',
  'panel_title' => ucwords(__('users.plural')),
  'resource_route' => 'users',
  'model_variable' => 'user',
  'model_class' => \App\User::class,
  'models' => $users,
  'action_buttons_view' => 'generator::components.models.index.action_buttons',
])
