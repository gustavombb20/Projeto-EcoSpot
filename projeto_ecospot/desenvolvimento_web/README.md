# EcoSpot + EcoAlert

Projeto acadêmico com foco no município de **Curitiba (PR)**:

- **EcoSpot** — consulta de pontos sustentáveis em um mapa.
- **EcoAlert** — registro de problemas ambientais.

A proposta é que, no futuro, as informações reunidas possam apoiar a Prefeitura Municipal de Curitiba e a Secretaria Municipal do Meio Ambiente (SMMA). O projeto é acadêmico e não tem vínculo oficial com esses órgãos.

## Status: Sprint 1 (09/10 a 15/10)

Entregue nesta sprint:

- Estrutura de pastas para HTML, CSS, JavaScript e futura integração PHP/MySQL (XAMPP).
- Página inicial (`index.html`), página EcoSpot (`ecospot.html`) e página EcoAlert (`ecoalert.html`).
- Navegação entre as páginas (menu principal, menu mobile, rodapé e trilha de navegação).
- Layout responsivo e acessível (link "pular para o conteúdo", foco visível, ARIA no menu e nos botões, alvos de toque ≥ 44 px).
- Google Maps incorporado no EcoSpot (iframe oficial, sem chave de API), centralizado em Curitiba.

## Status: Sprint 2 — cadastro, login e sessão

Entregue nesta sprint:

- Conexão PHP/MySQL com **PDO** e consultas preparadas (`backend/includes/db.php`).
- **Cadastro** (`cadastro.php`): nome, e-mail, senha, confirmação e **aceite obrigatório da Política de Privacidade**. Papel (`cidadao`) e situação (`ativo`) são definidos pelo servidor; o formulário não permite escolher papel.
- **Login** (`login.php`) com `password_verify`, mensagem de erro genérica e renovação do ID de sessão.
- **Sessão PHP** com cookie `HttpOnly` e `SameSite=Lax`, expiração por inatividade (2 h) e página **Minha conta** (`minha-conta.php`) com os dados gravados no banco.
- **Logout** (`logout.php`) somente por POST com token CSRF.
- **Proteção CSRF** em todos os formulários e **validação no servidor**.
- **Política de Privacidade** (`politica-privacidade.html`) — rascunho para revisão da equipe.
- Menu das páginas HTML mostra **Entrar** ou **Minha conta** conforme a sessão (`backend/api/sessao.php`).
- Banco de desenvolvimento **`ecospot_dev`** criado a partir do SQL validado + **migração 001**, sem alterar o arquivo original.

Fora do escopo (Sprints 3 a 7): registro de ocorrências, cadastro de pontos, área administrativa, recuperação de senha e edição de perfil.

### Regra de acesso

- **Visitantes sem conta** podem acessar a página inicial, consultar o EcoSpot, visualizar o mapa e conhecer as funcionalidades do EcoAlert.
- **Registrar uma ocorrência no EcoAlert** exigirá conta cadastrada e login.
- Cadastro e login funcionam desde a Sprint 2. O registro de ocorrências aparece como "Em breve" até ser implementado.

### Textos da interface

A interface pública usa linguagem voltada ao usuário final ("Em breve", "Em desenvolvimento"). Informações sobre sprints, planejamento e tecnologias ficam somente neste README. A identificação institucional aparece apenas no rodapé: "Projeto acadêmico desenvolvido por estudantes da PUCPR. Sem vínculo oficial com a Prefeitura de Curitiba."

## Estrutura

```
ecospot-ecoalert/
├── index.html                 # Página inicial
├── ecospot.html               # EcoSpot (mapa)
├── ecoalert.html              # EcoAlert
├── politica-privacidade.html  # Política de Privacidade (rascunho)
├── cadastro.php               # Criar conta
├── login.php                  # Entrar
├── logout.php                 # Sair (POST + CSRF)
├── minha-conta.php            # Dados da conta (exige login)
├── assets/
│   ├── css/style.css          # Estilos compartilhados por todas as páginas
│   ├── js/main.js             # Scripts compartilhados (menu, voltar ao topo, conta)
│   └── img/                   # Logotipo e ilustração (SVG)
├── backend/
│   ├── api/sessao.php         # Estado da sessão para o menu das páginas HTML
│   ├── config/                # app.php, database.example.php (+ database.local.php, fora do Git)
│   └── includes/              # bootstrap, PDO, sessão, CSRF, usuário, layout
└── database/
    ├── original/MYSQL1609(2).sql            # SQL validado pelo prof. Abimael (não editar)
    ├── migrations/001_sprint2_autenticacao.sql
    └── instalar_dev.php                     # Cria o ecospot_dev (linha de comando)
```

Tecnologias: HTML5, CSS3, JavaScript puro, PHP 8 (PDO) e MySQL/MariaDB (XAMPP), sem frameworks.

## Como visualizar

### No computador

Abra o `index.html` no navegador (duplo clique). O mapa precisa de internet.

Ou rode um servidor local na pasta do projeto:

```bash
python -m http.server 8000
# acesse http://localhost:8000
```

### No celular

1. Com o computador e o celular **na mesma rede Wi-Fi**, rode um servidor local no computador (o Apache do XAMPP ou o comando `python -m http.server 8000`).
2. Descubra o IP local do computador (Windows: `ipconfig` → "Endereço IPv4", ex.: `192.168.0.10`).
3. No navegador do celular, acesse `http://SEU-IP/ecospot-ecoalert/` (XAMPP) ou `http://SEU-IP:8000` (Python).
4. Se não abrir, libere a porta no firewall do Windows.

Para um teste rápido sem servidor, use o modo responsivo do navegador (F12 → ícone de celular).

## Como executar no XAMPP

1. Coloque o projeto em `C:\xampp\htdocs\ecospot-ecoalert` (via GitHub Desktop ou ZIP).
2. No **XAMPP Control Panel**, inicie **Apache** e **MySQL**.
3. Configure o banco: copie `backend\config\database.example.php` para `backend\config\database.local.php`. Para o XAMPP padrão (usuário `root`, sem senha) não é preciso mudar nada.
4. Crie o banco de desenvolvimento `ecospot_dev`. No Prompt de Comando:
   ```
   cd C:\xampp\htdocs\ecospot-ecoalert
   C:\xampp\php\php.exe database\instalar_dev.php
   ```
   O script lê o SQL original sem modificá-lo, cria as tabelas no `ecospot_dev` e aplica a migração 001. Para recriar do zero: acrescente `--recriar`. Ele se recusa a usar o banco compartilhado `ECOSPORT_ECOALERT`.
5. Acesse `http://localhost/ecospot-ecoalert/`, clique em **Entrar → Criar conta**, cadastre-se, entre e saia.
6. Confira os dados no phpMyAdmin (`http://localhost/phpmyadmin` → `ecospot_dev` → `usuario`): `senha_hash` começa com `$2y$`, `papel` = `cidadao`, `aceite_privacidade_em` e `versao_politica` preenchidos.

Alternativa sem linha de comando (phpMyAdmin): crie o banco `ecospot_dev` com agrupamento `utf8mb4_unicode_ci`; na aba **SQL** desse banco, cole o conteúdo de `MYSQL1609(2).sql` **sem as linhas 1 a 3** (`CREATE DATABASE` e `USE`) e execute; depois importe `database/migrations/001_sprint2_autenticacao.sql`.

As pastas `backend/config`, `backend/includes` e `database` têm `.htaccess` que bloqueia acesso pelo navegador.
