<?php

namespace App\Observers;

use App\Comment;
use App\Notifications\EditComment;
use App\Notifications\NewComment;
use Illuminate\Support\Facades\Notification;

class CommentObserver
{
    /**
     * Handle the post "creating" event.
     *
     * @param  \App\Comment  $comment
     * @return void
     */
    public function creating(Comment $comment)
    {
        if (!$comment->user_id)
            $comment->user()->associate(auth()->user());
    }

    /**
     * Handle the comment "created" event.
     *
     * @param  \App\Comment  $comment
     * @return void
     */
    public function created(Comment $comment)
    {
        $user = $comment->post->user;
        $post = $comment->post;
        $users = $post->comments->map(function ($comment){
            return $comment->user;
        });
        $users->push($user);
        $users = $users->reject(function ($user) use($comment) {
            return $user->id == $comment->user_id;
        });

        if (app()->runningUnitTests())
            Notification::fake();
        Notification::send($users, new NewComment($comment));
    }

    /**
     * Handle the comment "updated" event.
     *
     * @param  \App\Comment  $comment
     * @return void
     */
    public function updated(Comment $comment)
    {
        $user = $comment->post->user;
        $post = $comment->post;
        $users = $post->comments->map(function ($comment){
            return $comment->user;
        });
        $users->push($user);
        $users = $users->reject(function ($user) use($comment) {
            return $user->id == $comment->user_id;
        });

        if (app()->runningUnitTests())
            Notification::fake();
        Notification::send($users, new EditComment($comment));
    }

    /**
     * Handle the comment "deleted" event.
     *
     * @param  \App\Comment  $comment
     * @return void
     */
    public function deleted(Comment $comment)
    {
        //
    }

    /**
     * Handle the comment "restored" event.
     *
     * @param  \App\Comment  $comment
     * @return void
     */
    public function restored(Comment $comment)
    {
        //
    }

    /**
     * Handle the comment "force deleted" event.
     *
     * @param  \App\Comment  $comment
     * @return void
     */
    public function forceDeleted(Comment $comment)
    {
        //
    }
}
