# database/

| Arquivo | Conteúdo |
|---|---|
| `original/MYSQL1609(2).sql` | Modelo validado pelo prof. Abimael. **Não editar.** Cópia byte a byte (SHA-256 `b05d610b…1a56`). |
| `migrations/001_sprint2_autenticacao.sql` | Complemento da Sprint 2 para a tabela `usuario`. |
| `instalar_dev.php` | Cria o banco de desenvolvimento `ecospot_dev` (somente linha de comando). |

## Migração 001 (Sprint 2)

A tabela `usuario` original não tinha senha, aceite da política nem `AUTO_INCREMENT`. A migração, aplicada **somente no `ecospot_dev`**:

- torna `usuario_id` `AUTO_INCREMENT` (mantém `INT`; desliga a verificação de chaves estrangeiras só durante a alteração, porque cinco tabelas referenciam `usuario_id`);
- adiciona `senha_hash VARCHAR(255)`, `aceite_privacidade_em DATETIME` e `versao_politica VARCHAR(20)`, todas `NULL` para não invalidar linhas existentes.

Valores gravados pela aplicação no cadastro público: `papel = 'cidadao'`, `situacao = 'ativo'`, `versao_politica = '2026-10'` (ver `backend/config/app.php`). **Pendente:** confirmar esses valores de domínio com o prof. Abimael.

## Criar o ecospot_dev

```
C:\xampp\php\php.exe database\instalar_dev.php            (cria)
C:\xampp\php\php.exe database\instalar_dev.php --recriar   (apaga e recria)
```

Observação: o banco do script original se chama `ECOSPORT_ECOALERT` (com "SPORT"). O nome foi mantido como está no arquivo validado.
