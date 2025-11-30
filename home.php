<!DOCTYPE html>
<html lang="bg">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Иновация в Автоматизацията на Счетоводството</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" />
  <style>
    .header-bg { background-color: #0E2038; }
    .button-bg { background-color: #146DAF; }
    .button-hover:hover { background-color: #0E2038; }
    .border-feature-1 { border-top-color: #769F86; }
    .border-feature-2 { border-top-color: #146DAF; }
    .border-feature-3 { border-top-color: #7CCC87; }
    .footer-bg { background-color: #05112C; }
    .focus-ring:focus {
      ring-width: 2px;
      ring-color: #769F86;
      border-color: transparent;
    }
    .dot-1 { background-color: #769F86; }
    .dot-2 { background-color: #146DAF; }
    .dot-3 { background-color: #7CCC87; }
    .dot-4 { background-color: #0E2038; }
    .contact-border { border-top-color: #146DAF; }
    .logo-container {
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .logo-img {
      height: 50px; 
      width: auto;
    }
  </style>
</head>
<body class="bg-gray-50 text-gray-800">
  <header class="header-bg text-white py-6 shadow-xl">
    <div class="max-w-6xl mx-auto px-4 flex items-center justify-between">
      <div class="logo-container">
        <img src="logo.png" alt="Фактурчо" class="logo-img">
        <h1 class="text-3xl font-bold">Фактурчо</h1>
      </div>
      <nav class="space-x-6 text-lg">
        <a href="#features" class="hover:underline">Функции</a>
        <a href="#benefits" class="hover:underline">Предимства</a>
        <!-- <a href="#contact" class="hover:underline">Контакт</a> -->
        <a href="login.php" class="hover:underline">Login</a>
        <a href="register.php" class="hover:underline">Register</a>
      </nav>
    </div>
  </header>

  <section class="max-w-6xl mx-auto px-4 mt-12 text-center">
    <h2 class="text-4xl font-bold mb-4">Добре дошли в бъдещето на счетоводната автоматизация</h2>
    <p class="text-lg text-gray-700 mb-8">Нашият проект предлага революционно решение – автоматично разпознаване и осчетоводяване на фактури само по една снимка. Забравете ръчната обработка и грешките. Нашата AI система трансформира хартиените документи в точни счетоводни записи за секунди.</p>
    <a href="#contact" class="button-bg text-white px-6 py-3 rounded-xl text-lg shadow button-hover transition">Научи повече</a>
  </section>

  <section id="features" class="max-w-6xl mx-auto px-4 mt-20">
    <h3 class="text-3xl font-bold mb-6 text-center">Основни функции</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      <div class="bg-white p-6 shadow rounded-2xl border-t-4 border-feature-1">
        <h4 class="text-xl font-semibold mb-2">Автоматично въвеждане на данни</h4>
        <p class="text-gray-700">Системата разпознава фактури, документи и извлечения и ги обработва мигновено.</p>
      </div>
      <div class="bg-white p-6 shadow rounded-2xl border-t-4 border-feature-2">
        <h4 class="text-xl font-semibold mb-2">AI анализи</h4>
        <p class="text-gray-700">Получавате прецизни анализи и прогнози на база финансови данни в реално време.</p>
      </div>
      <div class="bg-white p-6 shadow rounded-2xl border-t-4 border-feature-3">
        <h4 class="text-xl font-semibold mb-2">Интеграции</h4>
        <p class="text-gray-700">Лесна връзка с ERP системи, банки и онлайн платформи.</p>
      </div>
    </div>
  </section>

  <section id="benefits" class="max-w-6xl mx-auto px-4 mt-20">
    <h3 class="text-3xl font-bold mb-6 text-center">Предимства</h3>
    <ul class="list-inside text-lg space-y-3 text-gray-800">
      <li class="flex items-center"><span class="w-3 h-3 dot-1 rounded-full mr-3"></span>Намаляване на човешките грешки</li>
      <li class="flex items-center"><span class="w-3 h-3 dot-2 rounded-full mr-3"></span>Ускоряване на процесите</li>
      <li class="flex items-center"><span class="w-3 h-3 dot-3 rounded-full mr-3"></span>Оптимизация на разходите</li>
      <li class="flex items-center"><span class="w-3 h-3 dot-4 rounded-full mr-3"></span>Повишена прозрачност</li>
    </ul>
  </section>

  <!-- <section id="contact" class="max-w-6xl mx-auto px-4 mt-20 mb-20">
    <h3 class="text-3xl font-bold mb-6 text-center">Контакт</h3>
    <form class="bg-white p-8 shadow-xl rounded-2xl max-w-xl mx-auto border-t-4 contact-border">
      <input type="text" placeholder="Име" class="w-full mb-4 p-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-[#769F86] focus:border-transparent" />
      <input type="email" placeholder="Имейл" class="w-full mb-4 p-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-[#769F86] focus:border-transparent" />
      <textarea placeholder="Съобщение" rows="4" class="w-full mb-4 p-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-[#769F86] focus:border-transparent"></textarea>
      <button class="button-bg text-white px-6 py-3 w-full rounded-xl shadow button-hover transition">Изпрати</button>
    </form>
  </section> -->

  <footer class="footer-bg text-white py-4 text-center">
    <p>© 2025 Фактурчо — Всички права запазени. <a href="mailto:fakturcho@gmail.com">fakturcho@gmail.com</a></p>
  </footer>
</body>
</html>