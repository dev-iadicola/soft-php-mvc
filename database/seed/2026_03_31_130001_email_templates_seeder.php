<?php

use App\Core\DataLayer\Seeder\Seeder;

return Seeder::table('email_templates')
    ->row([
        'slug' => 'contact_auto_reply',
        'subject' => 'Abbiamo ricevuto il tuo messaggio',
        'body' => '<h2>Grazie per averci contattato</h2>
<p>Gentile <strong>{nome}</strong>,</p>
<p>abbiamo ricevuto il tuo messaggio e ti confermiamo che è stato preso in carico. Ti risponderemo il prima possibile.</p>
<p><strong>Riepilogo del tuo messaggio:</strong></p>
<blockquote>{messaggio}</blockquote>
<p>Distinti saluti,</p>
<p><strong>Il Team</strong></p>
<p><em>Questa è una email automatica. Per favore non rispondere a questo indirizzo.</em></p>',
        'is_active' => 1,
    ]);
