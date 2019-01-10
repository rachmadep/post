@extends('posts.show')

@include('generator::components.models.childs.create', [
  'resource_route' => 'posts.comments',
  'model_variable' => 'comment',
  'parent' => $post
])
