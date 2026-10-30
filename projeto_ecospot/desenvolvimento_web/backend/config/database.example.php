<?php
/**
 * Modelo de configuração do banco de dados.
 *
 * Copie este arquivo para database.local.php (na mesma pasta) e ajuste.
 * O database.local.php está no .gitignore: nunca publique credenciais.
 */
return [
    'host'    => '127.0.0.1',
    'porta'   => 3306,
    'banco'   => 'ecospot_dev',   // banco de desenvolvimento, nunca o compartilhado
    'usuario' => 'root',          // padrão do XAMPP
    'senha'   => '',              // padrão do XAMPP: sem senha
    // true mostra detalhes de erros de banco na tela (apenas em desenvolvimento).
    'debug'   => true,
];
