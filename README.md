Esse projeto é o back-end de um questionário, neste projeto criamos usuários, formulários e questões, também podemos receber respostas sem o usuário estar logado e o dono do formulário irá receber as respostas por e-mail.

Como rodar:

git clone https://github.com/RebecaSSilva/Questionario-back-end.git

cd questionario-back-end

composer install

cp .env.example .env

Configure as credenciais do Mailtrap no arquivo .env. Você precisará preencher os seguintes campos:

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=seu_mailtrap_username
MAIL_PASSWORD=sua_mailtrap_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=seu_email@exemplo.com
MAIL_FROM_NAME="${APP_NAME}"

php artisan key:generate

php artisan migrate

php artisan db:seed

php artisan serve

As questões incluem dois filtros, de quem respondeu todas as questões do formulário 

{
    "filter_type": "completed"
}
ou de todas as pessoas(vem assim por padrão):
{
    "filter_type": "all"
}

Exemplo json de formulário para criar:

{
    "title": "Formulário de Teste",
    "url": "URL do formulário",
    "button_color": "#ffffff",
    "question_color": "#000000",
    "answer_color": "#333333",
    "background_color": "#f0f0f0",
    "background_image": "URL da imagem de fundo",
    "logo": "URL do logo",
    "font": "Arial",
    "questions": [
        {
            "field_title": "Frutas Preferidas?",
            "field_description": "Digite sua fruta",
            "field_type": "text",
            "is_last": false,
            "mandatory": false,
            "value_key": "[\"Abacaxi 🍍\",\"Melancia 🍉\",\"Morango 🍓\",\"Laranja 🍊\"]",
        },
        {
            "field_title": "Qual é o seu e-mail?",
            "field_description": "Digite seu endereço de e-mail",
            "field_type": "email",
            "is_last": true,
            "mandatory": false,
            "value_key": null
        }
    ]
}

Exemplo json de formulário para criar:
{
    "email":"jose@hotmail.com",
    "password":"password"
}

Depois de usar o seeders você pode se autenticar pegando o email da tabela users onde o id for igual ao user_id do formulário criado.