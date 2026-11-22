<?php

namespace App\Events;

use App\Models\Publication;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PublicationCreee
{
    use Dispatchable, SerializesModels;

    public function __construct(public Publication $publication)
    {
    }
}