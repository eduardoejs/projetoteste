Exemplo API REST com Silex e Doctrine
=================================


Este é um exemplo de uma api REST utilizando o micro-framework Silex e o Doctrine.

----------


Utilização
-------------

> **Instalação:**

> - git clone git@github.com:rogsilva/code-doctrine.git
> - cd code-doctrine
> - Baixe o composer e faça a instalação das dependências
> - mysql -uroot -p
> - mysql> source code_doctrine.sql;
> - mysql> quit
> - Acesse a pasta public do projeto e rode o comando "php -S localhost:8000".

#### <i class="icon-refresh"></i> Rotas

> **Utilizando o método HTTP GET:**

> - /api/produtos - Seleciona todos os registros.
> - /api/produtos/{id} - Seleciona um único registro de acordo com o id passado.

> - /api/categorias - Seleciona todos os registros.
> - /api/categorias/{id} - Seleciona um único registro de acordo com o id passado.

> - /api/tags - Seleciona todos os registros.
> - /api/tags/{id} - Seleciona um único registro de acordo com o id passado.

> **Utilizando o método HTTP POST:**

> - /api/produtos/ - Adiciona um novo registro.

> - /api/categorias/ - Adiciona um novo registro.

> - /api/tags/ - Adiciona um novo registro.

> **Utilizando o método HTTP PUT:**

> - /api/produtos/{id} - Altera um registro de acordo com o id passado.

> - /api/categorias/{id} - Altera um registro de acordo com o id passado.

> - /api/tags/{id} - Altera um registro de acordo com o id passado.

> **Utilizando o método HTTP DELETE:**

> - /api/produtos/{id} - Remove um registro de acordo com o id passado.

> - /api/categorias/{id} - Remove um registro de acordo com o id passado.

> - /api/tags/{id} - Remove um registro de acordo com o id passado.
