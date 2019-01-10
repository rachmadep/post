@extends('posts.show')

@include('generator::components.models.childs.index', [
  'resource_route' => 'posts.comments',
  'model_variable' => 'comment',
  'model_class' => \App\Comment::class,
  'parent' => $post,
  'models' => $comments,
  'action_buttons_view' => 'generator::components.models.childs.index.action_buttons',
])
