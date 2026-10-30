-- =====================================================================
-- Migração 001 — Sprint 2 (cadastro, login e aceite da política)
--
-- Complementa o script validado database/original/MYSQL1609(2).sql
-- SEM alterá-lo. Aplicar SOMENTE no banco de desenvolvimento ecospot_dev,
-- depois do script original. Nunca aplicar no banco compartilhado.
--
-- O que muda na tabela usuario:
--   1. usuario_id passa a ser AUTO_INCREMENT (o tipo INT é mantido);
--   2. senha_hash guarda o resultado de password_hash() do PHP;
--   3. aceite_privacidade_em e versao_politica registram o aceite
--      da política de privacidade no momento do cadastro.
--
-- As colunas novas aceitam NULL para não invalidar linhas que já existam.
-- A aplicação exige os três valores em todo cadastro novo.
-- =====================================================================

-- usuario_id é referenciado por chaves estrangeiras de cinco tabelas
-- (ponto_sustentavel, alerta_ambiental, midia, confirmacao, solicitacao_rota).
-- O MySQL 8 recusa alterar coluna referenciada com a verificação ligada;
-- desligá-la só durante esta alteração inclui o AUTO_INCREMENT sem recriar
-- as chaves. O tipo da coluna (INT) não muda.
SET FOREIGN_KEY_CHECKS = 0;
ALTER TABLE usuario
    MODIFY usuario_id INT NOT NULL AUTO_INCREMENT;
SET FOREIGN_KEY_CHECKS = 1;

ALTER TABLE usuario
    ADD COLUMN senha_hash VARCHAR(255) NULL AFTER email,
    ADD COLUMN aceite_privacidade_em DATETIME NULL AFTER data_criacao,
    ADD COLUMN versao_politica VARCHAR(20) NULL AFTER aceite_privacidade_em;
