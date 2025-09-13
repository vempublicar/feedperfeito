# Padrão de Implementação CRUD para Novos Cadastros

Este documento descreve o padrão a ser seguido para a implementação de funcionalidades de Cadastro, Leitura, Atualização e Exclusão (CRUD) de dados no sistema, visando padronização e eficiência.

## Processos Chave:

1.  **Página de Listagem (Frontend)**:
    *   **Localização**: `includes-adm/{nome-da-entidade-no-plural}.php` (ex: `includes-adm/produtos.php`).
    *   **Conteúdo**: Tabela HTML para exibir os registros.
    *   **Obtenção de Dados**: A página deve buscar os dados diretamente do PHP (ex: `require_once __DIR__ . '/../models/{NomeDoModelo}.php'; $modelo = new {NomeDoModelo}(); $dados = $modelo->all();`).
    *   **Botões de Ação**: Deve conter um botão "Novo {Entidade}" para abrir o modal de cadastro e, para cada registro na tabela, botões "Editar" e "Excluir".

2.  **Modal para Cadastrar Novos Registros (Frontend)**:
    *   **Localização**: Incorporado diretamente na página de listagem (`includes-adm/{nome-da-entidade-no-plural}.php`).
    *   **Conteúdo**: Formulário HTML com campos para os dados da nova entidade.
    *   **Ação do Formulário**: O `action` do formulário deve apontar para o endpoint da API de criação (`api/post/insert_{nome-da-entidade}.php`) e o `method` deve ser `POST`.
    *   **Botão "Salvar"**: Submete o formulário tradicionalmente.

3.  **Modal para Editar Registros Existentes (Frontend)**:
    *   **Localização**: Reutiliza o mesmo modal de cadastro na página de listagem (`includes-adm/{nome-da-entidade-no-plural}.php`).
    *   **Preenchimento**: Ao clicar em "Editar", o JavaScript deve preencher os campos do formulário no modal usando os `data-attributes` do botão "Editar" (que contêm os dados do registro).
    *   **Ação do Formulário**: O `action` do formulário deve ser dinamicamente alterado para o endpoint da API de atualização (`api/post/update_{nome-da-entidade}.php`) e um campo `hidden` `_method` com valor `PUT` deve ser adicionado para "method spoofing".

4.  **Botão para Excluir Registro (Frontend)**:
    *   **Localização**: Na tabela, para cada registro.
    *   **Implementação**: Deve ser um pequeno formulário `POST` com um campo `hidden` para o ID do registro e outro `hidden` para `_method` com valor `DELETE`.
    *   **Confirmação**: Usar `onsubmit="return confirm('Tem certeza que deseja excluir?');"` para solicitar confirmação ao usuário.
    *   **Ação do Formulário**: O `action` do formulário deve apontar para o endpoint da API de exclusão (`api/delete/{nome-da-entidade}.php`).

5.  **Função para Buscar Todos os Dados (Backend/Modelo)**:
    *   **Localização**: No arquivo do Modelo PHP (`models/{NomeDoModelo}.php`).
    *   **Método**: `public function all()` ou similar, que utiliza a função global `get_all()` ou `supabase_request()` para retornar todos os registros da tabela correspondente.

6.  **API POST para Cadastrar Novos Registros (Backend)**:
    *   **Localização**: `api/post/insert_{nome-da-entidade}.php`.
    *   **Método HTTP**: Aceita requisições `POST`.
    *   **Validação**: Valida os dados recebidos via `$_POST`.
    *   **Criação**: Utiliza o método `create()` do modelo para inserir o novo registro.
    *   **Retorno**: Armazena a mensagem de sucesso/erro em `$_SESSION['status_type']` e `$_SESSION['status_message']` e redireciona para a página de listagem (`header('Location: ...')`).

7.  **API para Editar e Atualizar Registros (Backend)**:
    *   **Localização**: `api/post/update_{nome-da-entidade}.php`.
    *   **Método HTTP**: Aceita requisições `POST` (para method spoofing).
    *   **Validação**: Valida os dados recebidos via `$_POST` e verifica `_method='PUT'`.
    *   **Atualização**: Utiliza o método `update()` do modelo para atualizar o registro, passando o ID e os dados.
    *   **Retorno**: Armazena a mensagem de sucesso/erro em `$_SESSION['status_type']` e `$_SESSION['status_message']` e redireciona para a página de listagem.

8.  **API para Deletar Registros (Backend)**:
    *   **Localização**: `api/delete/{nome-da-entidade}.php`.
    *   **Método HTTP**: Aceita requisições `POST` (para method spoofing).
    *   **Validação**: Valida o ID recebido via `$_POST` e verifica `_method='DELETE'`.
    *   **Exclusão**: Utiliza o método `delete()` do modelo para remover o registro.
    *   **Retorno**: Armazena a mensagem de sucesso/erro em `$_SESSION['status_type']` e `$_SESSION['status_message']` e redireciona para a página de listagem.

## Considerações Adicionais:

*   **Sessões**: Garanta que `session_start()` seja chamado no início de todos os scripts PHP que utilizam sessões. O `config/session.php` deve ser incluído nos arquivos principais (`dashboard-adm.php`).
*   **Mensagens de Status**: Na página de listagem (`includes-adm/{nome-da-entidade-no-plural}.php`), adicione um bloco PHP para verificar e exibir as mensagens de status da sessão, e em seguida, `unset()` as variáveis de sessão para limpá-las.
*   **Modelos**: Certifique-se de que os métodos `create()`, `update()`, `delete()` e `all()` nos modelos estejam corretamente implementados e chamem as funções globais ou métodos Supabase apropriados.
*   **`_SESSION['base_url']`**: Utilize `$_SESSION['base_url']` para construir URLs de forma dinâmica e evitar problemas com caminhos absolutos/relativos.