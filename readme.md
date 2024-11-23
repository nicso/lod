# Lights On Devs

This is a [Next.js](https://nextjs.org/) project bootstrapped with [`create-next-app`](https://github.com/vercel/next.js/tree/canary/packages/create-next-app).

## Getting Started

First, create a next project with the following command in frontend folder:

```bash
npx create-next-app . --ts --tailwind --eslint --app --no-src-dir --turbopack --use-npm --import-alias @/*# or
```
(test it at http://localhost:3000)

Then, install the dependencies with the following command in the frontend folder:

```bash
npm install
```

## Docker stack :

To run the project with docker-compose, run the following command in the root folder:

```bash
docker-compose up -d
```

the stack is composed of :

- an apache server
- a mariadb database
- a phpmyadmin interface


