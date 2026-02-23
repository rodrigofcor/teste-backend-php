# Teste Técnico – Desenvolvedor PHP Laravel

## Objetivo

Desenvolver uma aplicação backend responsável pelo processamento, transformação e sincronização de dados de produtos e preços, utilizando Views SQL para padronização das informações e disponibilizando os dados por meio de uma API REST.

---

## Requisitos Técnicos

Tecnologias obrigatórias:

* PHP 8.0+
* Laravel 11.0+
* SQLite
* Docker
* Docker Compose

---

## Restrições Obrigatórias

O projeto deve:

* Rodar integralmente via Docker.
* Possuir arquivo `docker-compose.yml`.
* Expor exclusivamente endpoints de API REST.
* Conter testes automatizados.
* Incluir instruções de execução no `README.md`.
* Documentar os endpoints disponíveis.

O projeto não deve:

* Exigir instalação de dependências na máquina host além do Docker.
* Conter qualquer tipo de interface web.

---

## Modelagem de Banco de Dados

### Tabelas de Origem

Devem ser criadas duas tabelas base:

* `produtos_base`
* `precos_base`

O script de criação das tabelas base encontra-se na raiz do projeto.

### Tabelas de Destino

Devem ser criadas duas tabelas para armazenamento dos dados processados:

* `produto_insercao`
* `preco_insercao`

Considere modelagem adequada, chaves e índices quando necessário.

---

## Processamento com Views SQL

A transformação dos dados deve ser realizada obrigatoriamente por meio de Views SQL.

Devem ser criadas:

* Uma View para produtos.
* Uma View para preços.

As Views devem contemplar:

* Normalização dos dados.
* Processamento apenas de registros ativos.

---

## Processo de Sincronização

A sincronização deve:

* Consumir os dados a partir das Views.
* Inserir, atualizar ou remover registros nas tabelas de destino.
* Evitar duplicidade.
* Evitar operações desnecessárias.

---

## API REST

A aplicação deve disponibilizar os seguintes endpoints:

### Sincronizar Produtos

POST /api/sincronizar/produtos

Executa o processo de transformação e sincronização dos dados de `produtos_base` para `produto_insercao`.

---

### Sincronizar Preços

POST /api/sincronizar/precos

Executa o processo de transformação e sincronização dos dados de `precos_base` para `preco_insercao`.

---

### Listar Produtos Sincronizados (Paginado)

GET /api/produtos-precos

Deve retornar os produtos processados com seus respectivos preços de forma paginada.
A paginação deve aceitar parâmetros de controle via query string.

---

## Como executar o projeto?

### Primeira vez rodando

**1) Crie o arquivo `.env` a partir do `.env.example`**
```bash
cp .env.example .env
```

**2) Inicie o ambiente Docker**
```bash
docker compose up -d
```

**3) Entre no container da aplicação**
```bash
docker exec -it test_backend_app bash
```

**4) Dentro do container, execute as migrations e o script base**
```bash
composer install
php artisan key:generate
php artisan migrate
sqlite3 database/database.sqlite < base_scripts.sql
```
Agora pode sair do container para utilizar

---

### Nas próximas vezes rodando basta executar
```bash
docker compose up -d
```

### Sincronizando os produtos
Utilize um aplicativo de requisições (como Postman ou Insomnia) ou o curl para realizar uma requisição `POST` na rota abaixo:
```
POST http://127.0.0.1:8000/api/sincronizar/produtos
```

Exemplo com curl:
```bash
curl -X POST http://127.0.0.1:8000/api/sincronizar/produtos
```
### Sincronizando os preços
Utilize um aplicativo de requisições (como Postman ou Insomnia) ou o curl para realizar uma requisição `POST` na rota abaixo:
```
POST http://127.0.0.1:8000/api/sincronizar/precos
```

Exemplo com curl:
```bash
curl -X POST http://127.0.0.1:8000/api/sincronizar/precos
```

### Consultando produtos e preços

Utilize um aplicativo de requisições ou o próprio navegador para acessar:
```
GET http://127.0.0.1:8000/api/produtos-precos
```

**Paginação** — acrescente `?page=` com o número da página desejada:
```
http://127.0.0.1:8000/api/produtos-precos?page=2
```

**Filtro por texto** — acrescente `?q=` com o texto desejado:
```
http://127.0.0.1:8000/api/produtos-precos?q=teclado
```

**Combinando filtro e paginação:**
```
http://127.0.0.1:8000/api/produtos-precos?q=teclado&page=2
```
### Testes automatizados

Dentro do container da aplicação, execute:
```bash
php artisan test
```
