# Base Theme renatodlm.com.br

Tema base para desenvolvimento de temas para clientes.

## Ambientes

| Ambiente | Branch | `wp_get_environment_type()` | URL                     |
| -------- | ------ | --------------------------- | ----------------------- |
| Local    | -      | `local`                     | https://rlmtheme.local/ |

---

### Editar wp-config.php

1. Incluir no `wp-config.php` a seguinte linha após _$table_prefix_:
2. Criar link simbólico para este arquivo.

```php
include 'env-local.php';
```

---

## Links Úteis

-  /login

---

## Funções

-  debug()
-  render_svg()

---

## Classes

Adicionar classes em /includes/classes

Exemplo: "/includes/classes/Classe/Classe.php"

Utilizando:

```
namespace RLM_Theme\Classe\Classe;

$classe = new Classe();
$value = $classe->get();
```

---

### Clonar repositório

1. Todos os projetos WordPress devem ficar na mesma pasta localmente.
2. Nesta pasta, clonar este repositório.

### Assets

Para gerar os assets do projeto é necessário rodar o NPM (node).
Recomenda-se a versão do Node 18.16.0.

Se for a primeira vez, abra o terminal na pasta do projeto e instale as dependências:

```bash
npm install
```

Ao termino da instalação das dependências, ou se já as possui:

```bash
npm run assets # gera CSS e JS
npm run js # gera somente JS
npm run css # gera somente CSS
npm run css-all # gera somente CSS all
npm run css-login # gera somente CSS login
npm run css-admin # gera somente CSS admin

npm run js-watch # gera JS e inicia modo watch
npm run css-all-watch # gera CSS all e inicia modo watch
npm run css-login-watch # gera CSS login e inicia modo watch
npm run css-admin-watch # gera CSS admin e inicia modo watch
```

---

## Plugins

Plugins necessários e respectivas configurações.

| Plugin                     | Obrigatório | Ambiente | Notas          |
| -------------------------- | ----------- | -------- | -------------- |
| Advanced Custom Fields PRO | Sim         | Todos    | Custom fields. |

## Links úteis

-  [Tailwind](https://tailwindcss.com/docs/installation)
-  [Alpine](https://alpinejs.dev/)
-  [Swiperjs](https://swiperjs.com/)
