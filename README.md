This is a [Next.js](https://nextjs.org/) project bootstrapped with [`create-next-app`](https://github.com/vercel/next.js/tree/canary/packages/create-next-app).

## Getting Started

# PHP & Next.js Project

This project integrates a PHP backend with a Next.js frontend. The PHP backend handles server-side functionality, while the Next.js frontend provides the user interface. The project involves setting up a **font-server** for managing font uploads.

## Prerequisites

Before getting started, ensure you have the following installed on your machine:

- **PHP** (Version 7.x or later)
- **Composer** (PHP dependency manager)
- **Node.js** (Version 14.x or later)
- **NPM** or **Yarn** (Package managers for Node.js)
- **XAMPP** (For running Apache and MySQL)

### 1. Clone the Repository

First, clone the repository and navigate to the project directory:

```bash
git clone https://github.com/your-username/your-repo.git
cd your-repo


First, run the development server:

```bash
npm run dev
# or
yarn dev
# or
pnpm dev
# or
bun dev
```
### 2. Setting up the PHP Backend
1. Inside the zepto_app directory, there is a folder named font-server. Move this folder to your PHP server directory. If you are using XAMPP, place it in the htdocs folder:
```bash
C:\xampp\htdocs\font-server
```
Start the XAMPP services:

 Open XAMPP Control Panel.
 Start the Apache server and MySQL.
 
### 3. Open phpMyAdmin in your browser and create new Database named 'font_group '

### 4. Open your code editor and navigate to the uploadFont.php file located inside the font-server folder.
```bash
C:\xampp\htdocs\font-server\uploadFont.php
```
### 5. In the uploadFont.php file, locate the $uploadDirectory variable. Update the path to your Next.js project's public/fonts directory where the uploaded fonts will be stored. For example:
```bash
$uploadDirectory = 'C:/path/to/your-nextjs-app/public/fonts' // as yours;
```
Open [http://localhost:3000](http://localhost:3000) with your browser to see the result.

You can start editing the page by modifying `app/page.js`. The page auto-updates as you edit the file.

This project uses [`next/font`](https://nextjs.org/docs/basic-features/font-optimization) to automatically optimize and load Inter, a custom Google Font.

## Learn More

To learn more about Next.js, take a look at the following resources:

- [Next.js Documentation](https://nextjs.org/docs) - learn about Next.js features and API.
- [Learn Next.js](https://nextjs.org/learn) - an interactive Next.js tutorial.

You can check out [the Next.js GitHub repository](https://github.com/vercel/next.js/) - your feedback and contributions are welcome!

## Deploy on Vercel

The easiest way to deploy your Next.js app is to use the [Vercel Platform](https://vercel.com/new?utm_medium=default-template&filter=next.js&utm_source=create-next-app&utm_campaign=create-next-app-readme) from the creators of Next.js.

Check out our [Next.js deployment documentation](https://nextjs.org/docs/deployment) for more details.
