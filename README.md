# X-AdminPanel: Painel Administrativo Moderno

O **X-AdminPanel** é um sistema administrativo moderno, modular e escalável, desenvolvido para estruturar organizações, automatizar fluxos de trabalho e gerenciar atividades operacionais com segurança, rastreabilidade e performance.

🚧 **Projeto em desenvolvimento ativo** — Versão estável com arquitetura consolidada e módulos funcionais prontos para uso e expansão.

O objetivo é oferecer uma solução segura, intuitiva e eficiente para o gerenciamento de informações médicas, garantindo maior agilidade no atendimento, organização no histórico clínico e uma experiência transformadora no cuidado com os pacientes.

---

## 🧩 Módulos Implementados

O sistema é organizado por **módulos funcionais independentes**, cada um com sua estrutura de rotas, views, componentes Livewire e lógica de negócio:

### 🏢 **Organização**
- Visualização do organograma corporativo
- Gerenciamento administrativo da hierarquia
- Estruturação por setores e níveis hierárquicos

### 🔄 **Workflow**
- Definição e estruturação de processos organizacionais
- Criação de etapas e associação com setores
- Integração direta com atividades e tarefas

### 📋 **Gestão de Atividades (Tasks)**
- Criação e acompanhamento de tarefas
- Status e categorias administrativas
- Definição de responsáveis
- Integração nativa com módulo de Workflow
- Estrutura preparada para expansão futura

### 👥 **Administração**
- Gestão completa de usuários
- Controle de permissões e acessos

### ⚙️ **Configurações do Sistema**
- Cadastro de estabelecimentos
- Ocupações (CBO)
- Regiões (País, Estado, Cidade)
- Blocos financeiros

### 🛡️ **Auditoria**
- Registro centralizado de logs
- Visualização administrativa com filtros
- Rastreabilidade completa de ações

---

## ⚡ Funcionalidades Atuais

**🏢 Organograma** – Visualização e gestão hierárquica  
**🔄 Workflow** – Processos, etapas e integração com tarefas  
**📋 Tasks** – Criação, status, responsáveis e categorias  
**👥 Administração** – Usuários e permissões por namespace  
**⚙️ Configurações** – Estabelecimentos, CBO, regiões, blocos  
**🛡️ Auditoria** – Logs completos e rastreabilidade  
**🎨 UX/UI** – Design responsivo, sidebar inteligente, Livewire reativo  
**🔐 Auth** – Login, registro, verificação e recuperação

## Tecnologias Utilizadas

- **Backend:** Laravel (PHP 8.2+)
- **Frontend:** Blade / Livewire
- **Interatividade:** AlpineJS
- **Icons:** Font Awesome 6
- **Banco de Dados:** PostgreSQL
- **Controle de Versão:** Git

## Instalação e Uso

**Pré-requisitos:** PHP 8.2+, Composer, PostgreSQL, Node.js

1. **Clone o repositório:**

    ```bash
    git clone https://github.com/matheus1696/x-admin-panel.git
    ```

    ```bash
    cd x-admin-panel
    ```

2. **Instale as dependências:**

    ```bash
    composer install
    ```

    ```bash
    npm install
    ```

3. **Configure o ambiente:**

    ```bash
    cp .env.example .env
    ```

    ```bash
    php artisan key:generate
    ```

4. **Configure o banco de dados no arquivo `.env`** e execute as migrations:

    ```bash
    php artisan migrate --seed
    ```

5. **Inicie o servidor:**

    ```bash
    php artisan serve
    ```

    ```bash
    npm run dev
    ```

## Acesso ao Sistema

- **Usuário Padrão:** `admin@example.com`  
- **Senha:** `password`

## Bibliotecas Utilizadas no Projeto

- **Reactividade:** [Livewire](https://laravel-livewire.com/)

## Autores

**Projeto desenvolvido por Webxperts**  
**Matheus André Bezerra Mota** – Analista de Sistemas e Infraestrutura