<?php
/**
 * Constantes da aplicação. Não contém credenciais.
 */
declare(strict_types=1);

// Banco compartilhado definido no SQL validado. A aplicação se recusa a usá-lo.
const BANCO_ORIGINAL = 'ECOSPORT_ECOALERT';

// Valores gravados em usuario.papel e usuario.situacao no cadastro público.
// O papel é sempre definido pelo servidor; o formulário não o envia.
const PAPEL_PADRAO = 'cidadao';
const SITUACAO_ATIVA = 'ativo';

// Versão da política de privacidade aceita no cadastro (ver politica-privacidade.html).
const POLITICA_VERSAO = '2026-10';

// Limites de validação (iguais aos tamanhos das colunas do SQL validado).
const NOME_MIN = 2;
const NOME_MAX = 100;
const EMAIL_MAX = 100;
const SENHA_MIN = 8;
const SENHA_MAX = 72; // limite do algoritmo bcrypt usado por password_hash()

// Sessão expira após este tempo sem atividade (segundos).
const SESSAO_INATIVIDADE = 7200;
