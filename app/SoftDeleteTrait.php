<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

trait SoftDeleteTrait
{
    use SoftDeletes;

    /**
     * Restore a soft-deleted model.
     */
    public function restoreModel()
    {
        $this->restore();
    }

    /**
     * Soft delete a model.
     */
    public function softDeleteModel()
    {
        $this->delete();
    }

    /**
     * Check if a model is soft-deleted.
     */
    public function isTrashed()
    {
        return $this->trashed();
    }
}
