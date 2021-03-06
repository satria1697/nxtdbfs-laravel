<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use JamesDordoy\LaravelVueDatatable\Traits\LaravelVueDatatableTrait;

class Rank extends Model
{
    use HasFactory, LaravelVueDatatableTrait;
    protected $table = 'nxt_ranks';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'description',
    ];

    protected $dataTableColumns = [
        'id' => [
            'searchable' => false,
        ],
        'description' => [
            'searchable' => true,
        ]
    ];
}
