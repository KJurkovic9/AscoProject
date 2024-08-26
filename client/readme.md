## Asco klijent

### Instalacija & konfiguracija

Kopirati `.example.env` u `.env` i konfigurirati

```bash
cp .example.env .env
```

> Preporučuje se koristiti [bun](https://bun.sh), ali može se koristiti i npm/pnpm/yarn za instalaciju paketa (i node runtime)

```bash
bun install
```

### Pokretanje

> Za ispravan rad potrebno je pokrenuti i [server](../server/)

```bash
bun dev
```

## Struktura

- `src` - Izvorni kod

  - `components` - Komponente

    - `ui` - osnovne komponente

  - `app` - Stranice
  - `hooks` - React hooks
    - `api` - API hooks (react-query)
  - `types` - TypeScript tipovi
  - `providers` - React konteksti
  - `lib` - Osnovne funkcije (axios, const, enum, env, etc.)

- `public` - Statički fajlovi
