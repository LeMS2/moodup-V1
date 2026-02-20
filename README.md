🚀 MoodUp API (V1)

API RESTful para registro e análise de humor diário, com autenticação segura via Laravel Sanctum, suporte a categorias (many-to-many) e geração de resumos semanais e mensais.

Deploy em produção via Railway.

---

🌐 Deploy (Produção)

Base URL: https://moodup-v1-production.up.railway.app

Health check público: GET /api/health

---

🛠️ Stack Tecnológica

- PHP 8.3
- Laravel 12
- MySQL
- Laravel Sanctum (Bearer Token)
- Railway (Deploy & Database)
- Postman (Testes manuais)

---

📦 Funcionalidades

✅ Registro de usuário
✅ Login com geração de token
✅ Logout
✅ CRUD completo de humor (moods)
✅ CRUD completo de categorias
✅ Relatório semanal e mensal
✅ Filtros por período
✅ Filtro por categoria
✅ Segurança por usuário (isolamento de dados)
✅ API estruturada com Resources

---

🔐 Autenticação

A autenticação é feita via Bearer Token utilizando Laravel Sanctum.

Registro: POST /api/auth/register

Body:

{
  "name": "nome",
  "email": "seu_email_teste",
  "password": "sua_senha_teste",
  "password_confirmation": "sua_senha_teste"
}

Resposta:

{
  "token": "1|xxxxxxxxxxxxxxxx"
}

---

Usar Token nas rotas protegidas

Header obrigatório:

Authorization: Bearer SEU_TOKEN_AQUI
Accept: application/json

Dados do usuário autenticado:

GET /api/auth/me

---

🧠 Moods (Registro de Humor)

Listar:

GET /api/moods

Filtros opcionais:

?start_date=2026-02-01
?end_date=2026-02-28
?category_id=1

---

Criar:

POST /api/moods

JSON 

{
  "date": "2026-02-22",
  "level": 4,
  "note": "Dia produtivo.",
  "category_ids": [1, 2]
}

----

Atualizar

PATCH /api/moods/{id}

----

Remover

DELETE /api/moods/{id}

----

🏷️ Categorias

Listar

GET /api/categories

Criar

POST /api/categories

JSON

{
  "name": "Trabalho"
}

Atualizar

PATCH /api/categories/{id}

Remover

DELETE /api/categories/{id}

---

📊 Resumos e Estatísticas

Resumo Semanal

GET /api/moods/summary/weekly

Resumo Mensal

GET /api/moods/summary/monthly

Suporta filtros:

?start_date=YYYY-MM-DD
?end_date=YYYY-MM-DD
?category_id=ID

Retorna:

- Média do período
- Quantidade de registros
- Distribuição por nível (1–5)
- Melhor dia
- Pior dia
- Top 3 melhores
- Top 3 piores

---

🧪 Rodando Localmente

composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve

Acesse:

http://127.0.0.1:8000

---

⚙️ Variáveis de Ambiente (Produção)

Principais variáveis utilizadas:

APP_NAME
APP_ENV=production
APP_KEY
APP_DEBUG=false
APP_URL
DB_CONNECTION=mysql
DB_HOST
DB_PORT
DB_DATABASE
DB_USERNAME
DB_PASSWORD

----

🧱 Estrutura da Arquitetura

- Controllers organizados por domínio (Auth, Mood, Category, Summary)
- Validações via FormRequest
- Resources para padronização de resposta
- Middleware auth:sanctum
- Many-to-many (moods ↔ categories)
- Filtros com query builder

----

🔒 Segurança

- Autenticação via token
- Proteção de rotas com middleware
- Isolamento de dados por usuário
- Validação de categoria por ownership
- Proteção contra acesso cruzado

----

📈 Melhorias Futuras

- Testes automatizados (Feature Tests)
- Documentação Swagger/OpenAPI
- Rate limiting avançado
- Logs estruturados
- Deploy com CI/CD
- Versão mobile Flutter consumindo a API

---

👩‍💻 Autora

Desenvolvido por Letícia Marques, estudante de ADS - Análise e Desenvolvimento de Sistemas
Projeto de estudo com foco em arquitetura backend e deploy em produção.