<?php

namespace App\Models;


class Status extends Model
{
    protected $table = "status";

    const STATUS_KEY_PENDING = "pending";
    const STATUS_KEY_REVIEW = "review";
    const STATUS_KEY_VALID = "valid";
    const STATUS_KEY_INVALID = "invalid";
    const STATUS_KEY_EXCLUDED = "excluded";

    // public $STATUS_FOR_DOCUMENTS = [
    //     $this::STATUS_KEY_PENDING,
    //     $this::STATUS_KEY_REVIEW,
    //     $this::STATUS_KEY_VALID,
    //     $this::STATUS_KEY_INVALID,
    //     $this::STATUS_KEY_EXCLUDED
    // ];
    // public $STATUS_FOR_MACHINERY = [
    //     $this::STATUS_KEY_PENDING,
    //     $this::STATUS_KEY_INVALID,
    // ];
    // public $STATUS_FOR_LEASES = [
    //     $this::STATUS_KEY_PENDING,
    //     $this::STATUS_KEY_REVIEW,
    //     $this::STATUS_KEY_VALID,
    //     $this::STATUS_KEY_INVALID,
    // ];

    public function documents()
    {
        return $this->hasMany(Document::class, 'status_id', 'id');
    }
}