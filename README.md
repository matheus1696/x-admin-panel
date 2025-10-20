# X-AdminPanel: Painel Administrativo Moderno

O **X-AdminPanel** é um painel administrativo moderno construído com Laravel, Livewire e Tailwind CSS. Desenvolvido para acelerar a criação de sistemas administrativos com componentes reutilizáveis.

**🚧 Status Atual:** Sistema em desenvolvimento - Módulo de Painel de Chamadas disponível

O objetivo é oferecer uma solução segura, intuitiva e eficiente para o gerenciamento de informações médicas, garantindo maior agilidade no atendimento, organização no histórico clínico e uma experiência transformadora no cuidado com os pacientes.

## ⚡ Funcionalidades Atuais

- **🎨 Design Moderno:** CInterface limpa com gradientes e animações suaves
- **📱 Layout Responsivo:** Sidebar inteligente (colapsável no desktop, overlay no mobile)
- **🔐 Sistema de Autenticação:** Login, registro, recuperação de senha e verificação de email
- **⚡ Componentes Livewire:** Reactividade sem complicação
- **🎯 Navegação Inteligente:** Dropdowns com comportamento accordion
- **🎯 👤 Perfil de Usuário:** Menu lateral com informações e ações

## Tecnologias Utilizadas

- **Backend:** Laravel (PHP)
- **Frontend:** Blade / Livewire
- **Icons:** Font Awesome 6
- **Banco de Dados:** PostgreSQL
- **Controle de Versão:** Git & GitHub

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