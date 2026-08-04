# 📋 Kanban App — Sistema de Gestão de Tarefas

Um sistema web simples pra gerenciar tarefas no estilo Kanban.
Feito com PHP puro, MySQL e JavaScript vanilla (sem framework nenhum).

## ✨ O que tem no sistema

- Cadastro e login de usuário (usando sessão do PHP)
- Cada usuário só vê e mexe nas **próprias tarefas**
- CRUD completo (criar, listar, editar e excluir tarefas)
- Quadro Kanban com 4 colunas: A Fazer, Em Andamento, Em Revisão e Concluído
- Prioridade das tarefas (Baixa, Média e Alta)
- Interface responsiva e leve, sem biblioteca pesada de frontend

## 🛠 Tecnologias usadas

| Camada         | Tecnologia                       |
|----------------|----------------------------------|
| Backend        | PHP 8+ (puro e orientado a objetos) |
| Banco de Dados | MySQL 8+                         |
| Frontend       | HTML5, CSS3 e JavaScript Vanilla |
| Autenticação   | Sessão do PHP + `password_hash()`|

## 📁 Estrutura do Projeto

kanban-app/
├── app/ # Lógica de aplicação (models, controllers, helpers)
├── public/ # Webroot público (entry points, assets, API)
├── database/ # Scripts SQL (schema e seed)
└── storage/logs/ # Logs da aplicação

