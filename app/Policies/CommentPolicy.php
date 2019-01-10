<?php

namespace App\Policies;

use App\User;
use App\Comment;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Comment Policy
 */
class CommentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view list of model.
     *
     * @param User $user
     * @return mixed
     */
    public function index(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the comment.
     *
     * @param User $user
     * @param Comment $comment
     * @return mixed
     */
    public function view(User $user, Comment $comment)
    {
        return true;
    }

    /**
     * Determine whether the user can create dummyPluralModel.
     *
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the comment.
     *
     * @param User $user
     * @param Comment $comment
     * @return mixed
     */
    public function update(User $user, Comment $comment)
    {
        return $comment->user_id == $user->id;
    }

    /**
     * Determine whether the user can delete the comment.
     *
     * @param User $user
     * @param Comment $comment
     * @return mixed
     */
    public function delete(User $user, Comment $comment)
    {
        return $comment->user_id == $user->id;
    }
}
