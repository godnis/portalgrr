<?php

return [
    // ✅ agora é HASH, não texto puro
    'senha_hash'  => env('AUDITORIA_SENHA_HASH', ''),
    'ttl_minutes' => (int) env('AUDIT_REVEAL_TTL', 2), //minutos (Lembre-se de por no .env tbm) - Evitar mexer aqui!
];
