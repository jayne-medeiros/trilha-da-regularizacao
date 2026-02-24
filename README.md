# Trilha da Regularização

Site da **Trilha da Regularização** com painel administrativo simples (PHP) para gerenciar:

- Módulos (trilhas)
- Posts (blog/notícias/materiais)

## Estrutura

- `index.html`: página pública
- `admin/painel.html`: painel administrativo
- `admin/api.php`: API (GET/POST) que lê/grava `admin/dados.json` e gerencia uploads em `admin/uploads/`
- `enviar.php`: endpoint do formulário de contato

## Configuração (importante)

### Chave do admin

A API do admin exige uma chave.

Você pode configurar de uma das formas abaixo:

1) Variável de ambiente (recomendado)

- `ADMIN_API_KEY` = uma chave forte

2) Arquivo local (não versionado)

Crie `admin/config.php` (este arquivo é ignorado pelo `.gitignore`) com o conteúdo:

```php
<?php

return [
    'ADMIN_API_KEY' => 'coloque-uma-chave-forte-aqui'
];
```

No primeiro acesso ao `admin/painel.html`, a chave será solicitada e salva no `localStorage` do navegador.

### E-mail de destino do formulário

O `enviar.php` usa a variável de ambiente:

- `CONTACT_TO_EMAIL` = email que deve receber as mensagens do formulário

## Rodando localmente

Este projeto é um site estático com endpoints PHP.

- Para acessar o painel, abra `admin/painel.html` em um servidor que execute PHP.
- A API `admin/api.php` precisa estar acessível via HTTP (ex: Apache/Nginx, XAMPP/WAMP, ou servidor da sua hospedagem).

## Arquivos ignorados

Por padrão, não sobem para o GitHub:

- `admin/config.php`
- `admin/uploads/`
- `admin/dados.json`
