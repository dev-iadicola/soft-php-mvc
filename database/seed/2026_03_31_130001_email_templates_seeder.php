<?php

use App\Core\DataLayer\Seeder\Seeder;

return Seeder::table('email_templates')
    ->row([
        'slug' => 'contact_auto_reply',
        'subject' => 'Abbiamo ricevuto il tuo messaggio',
        'body' => '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h2 style="color: #333;">Grazie per averci contattato</h2>
    <p>Gentile <strong>{nome}</strong>,</p>
    <p>abbiamo ricevuto il tuo messaggio e ti confermiamo che è stato preso in carico. Ti risponderemo il prima possibile.</p>
    <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
    <p style="color: #666; font-size: 0.9em;"><strong>Riepilogo del tuo messaggio:</strong></p>
    <blockquote style="border-left: 3px solid #4A90D9; padding-left: 12px; color: #555; margin: 10px 0;">
        {messaggio}
    </blockquote>
    <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
    <p>Distinti saluti,<br><strong>Il Team</strong></p>
    <p style="color: #999; font-size: 0.8em;">Questa è una email automatica. Per favore non rispondere a questo indirizzo.</p>
</div>',
        'is_active' => 1,
    ]);
