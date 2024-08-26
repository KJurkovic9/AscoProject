# Asco backend

Potrebni koraci za pokretanje backenda

## Sadržaj

- [Struktura](#structure)
- [Konfiguracija](#config)
- [Dokumentacija](#docs)

## Struktura

Backend je podijeljen u nekoliko dijelova:

- **src**
  - **Controller** - sadrže logiku za rute
  - **Entity** - sadrži entitete koji se koriste za rad s bazom podataka
  - **Repository** - sadrži repozitorije koji se koriste za rad s bazom podataka
  - **Form** - sadrži forme koje se koriste za validaciju podataka
  - **Command** - sadrži komande koje se koriste za rad s bazom podataka
- **Templates** - sadrži predloške za generiranje mailova
- **Migrations** - sadrži migracije za bazu podataka
- **config** - sadrži konfiguraciju za Symfony

## Konfiguracija

Također, morat ćete konfigurirati Symfony. [Preuzimanje Symfony-a](https://symfony.com/download)

> Trebat ćete omogućiti natrij u php.ini, jednostavno potražite extension=sodium -> trebate ukloniti točku-zarez ispred toga kako biste ispravno konfigurirali kolačiće.

Instalacija potrebnih paketa:

```bash
  composer require
```

Nakon toga, morat ćete konfigurirati .env datoteku (primjer se nalazi u `.env.example`). U njoj ćete morati postaviti `DATABASE_URL` na svoju bazu podataka. Primjer:

`DATABASE_URL="mysql://exampleUser:examplePassword@127.0.0.1:3306/exampleDBName?serverVersion=8.0.32&charset=utf8mb4"`

Potrebno je kreirati bazu podataka koristeći:

```bash
  symfony console doctrine:database:create
```

Nakon toga, morat ćete kreirati migracije koristeći

```bash
  symfony console make:migration
```

Nakon toga, morat ćete izvršiti migracije koristeći

```bash
  symfony console doctrine:migrations:migrate
```

Skoro ste gotovi, posljednja stvar koja je potrebna za ispravno funkcioniranje pozadine je instaliranje OpenSSL-a koji se koristi u generiranju tokena. Možete ga preuzeti [ovdje](https://slproweb.com/products/Win32OpenSSL.html)

Zadnji korak je generiranje ključeva za JWT token. To možete učiniti koristeći

```bash
  symfony console lexik:jwt:generate-keypair
```

Nakon što ste sve konfigurirali, možete pokrenuti server koristeći

```bash
  symfony server:start
```

## Dokumentacija

Nakon što ste pokrenuli server, možete pristupiti API na poveznici:

`http://127.0.0.1:8000/api/doc`


## Generiranje PDF-a

Kako bi ste mogli generirati pdf-ove potreban vam je 

[ovaj software](https://wkhtmltopdf.org/),

potom morate podesiti .env da postoji put to topdf verzije i toimage verzije