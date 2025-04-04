# Mreža Solidarnosti
Mreža solidarnosti je inicijativa IT Srbije za direktnu finansijsku podršku nastavnicima i vannastavnom osoblju čija je plata umanjena zbog obustave rada.

![image.jpg](https://raw.githubusercontent.com/IT-Srbija-Org/solidaritySF/refs/heads/main/public/image/readme.png)

# Tehnologije
 - [PHP 8.3](https://www.php.net/)
 - [NGINX](https://nginx.org/)
 - [MySQL](https://www.mysql.com/)
 - [Docker](https://www.docker.com/)
 - [Symfony 6.4](https://symfony.com/)
 - [TailwindCSS 4](https://tailwindcss.com/)
 - [daisyUI 4](https://daisyui.com/)
 - [Tabler Icons](https://tabler.io/icons)

# Proces instalacije projekta

1. Klonirajte projekt
```bash
$ git clone https://github.com/IT-Srbija-Org/solidaritySF; cd solidaritySF;
```

2. Podignite Docker
```bash
$ docker compose up -d;
```

3. Instalirajte composer
```bash
$ docker exec solidarity-php-container php composer.phar install;
```

4. Otvorite URL adresu u pretraživaču: http://localhost:1000