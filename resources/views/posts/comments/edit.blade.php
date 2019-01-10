@extends('posts.show')

@include('generator::components.models.childs.edit', [
  'resource_route' => 'posts.comments',
  'model_variable' => 'comment',
  'parent' => $post,
  'model' => $comment
])
