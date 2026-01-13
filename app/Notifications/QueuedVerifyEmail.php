<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Auth\Notifications\VerifyEmail;

class QueuedVerifyEmail extends VerifyEmail implements ShouldQueue
{
    use Queueable;

    // BURADA BAŞKA HİÇBİR KOD OLMASIN.
    // toMail, toArray vs. hepsini sil.
    // Sildiğin an Laravel, "Burada yoksa atasına (VerifyEmail) bakayım" der 
    // ve o güzel orijinal mail şablonunu kullanır.
}