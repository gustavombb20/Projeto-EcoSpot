# backend/

Código PHP executado pelo Apache do XAMPP.

```
backend/
├── api/sessao.php        # JSON {autenticado, nome} para o menu das páginas HTML
├── config/
│   ├── app.php                 # constantes (papel padrão, limites, versão da política)
│   ├── database.example.php    # modelo de credenciais (vai para o Git)
│   └── database.local.php      # credenciais reais (NÃO vai para o Git)
└── includes/
    ├── bootstrap.php   # carregado por toda página PHP
    ├── funcoes.php     # e(), redirecionar(), mensagens flash
    ├── db.php          # conexão PDO (exceções, utf8mb4, sem emulação de prepare)
    ├── sessao.php      # sessão segura, login, logout, exigir_login()
    ├── csrf.php        # token CSRF
    ├── usuario.php     # validação e consultas da tabela usuario
    └── layout.php      # cabeçalho e rodapé iguais aos das páginas HTML
```

Regras de segurança adotadas: consultas preparadas, `password_hash`/`password_verify`, CSRF em todo POST, logout só por POST, papel definido pelo servidor, ID de sessão renovado no login, cookie `HttpOnly` + `SameSite=Lax`, recusa de configuração apontando para o banco compartilhado `ECOSPORT_ECOALERT`.
