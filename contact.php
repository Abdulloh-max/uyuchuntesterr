<?php
require_once 'config.php';

$currentCurrency = isset($_SESSION['currency']) ? $_SESSION['currency'] : 'UZS';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Контакты - UyUchun</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <!-- Шапка -->
    <header class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <a href="/" class="flex items-center">
                        <div class="logo-container" style="border-radius: 24px; overflow: hidden; width: 50px; height: 50px;">
                            <img src="https://i.postimg.cc/wyMVXxw6/avatar.png" alt="UyUchun" class="w-full h-full object-cover">
                        </div>
                        <span class="text-2xl font-bold text-blue-600 ml-2">UyUchun</span>
                    </a>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-1 bg-gray-100 rounded-lg p-1">
                        <button onclick="setCurrency('UZS')" class="currency-btn px-3 py-1 rounded-md text-sm font-medium <?php echo $currentCurrency === 'UZS' ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-200'; ?>" data-currency="UZS">UZS</button>
                        <button onclick="setCurrency('USD')" class="currency-btn px-3 py-1 rounded-md text-sm font-medium <?php echo $currentCurrency === 'USD' ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-200'; ?>" data-currency="USD">$</button>
                    </div>
                    <button onclick="showLocation()" class="text-gray-600 hover:text-blue-600 transition">
                        <i class="fas fa-map-marker-alt text-xl"></i>
                    </button>
                    <a href="contact.php" class="text-gray-600 hover:text-blue-600 transition">
                        <i class="fas fa-envelope text-xl"></i>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Контакты</h1>

        <div class="grid md:grid-cols-2 gap-8">
            <!-- Контактная информация -->
            <div>
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">Свяжитесь с нами</h2>
                    
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="bg-blue-100 rounded-full p-3 mr-4">
                                <i class="fas fa-map-marker-alt text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-700">Адрес</h4>
                                <p class="text-gray-600">г. Ташкент, ул. Лабзак, 86</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="bg-blue-100 rounded-full p-3 mr-4">
                                <i class="fas fa-phone text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-700">Телефоны</h4>
                                <p class="text-gray-600">+998-90-905-88-80</p>
                                <p class="text-gray-600">+998-99-844-07-00</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="bg-blue-100 rounded-full p-3 mr-4">
                                <i class="fas fa-clock text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-700">Режим работы</h4>
                                <p class="text-gray-600">Пн-Вс: 9:00 - 21:00</p>
                            </div>
                        </div>
                    </div>

                    <!-- Карта -->
                    <div class="mt-6">
                        <div class="relative w-full" style="padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 12px;">
                            <iframe 
                                src="https://yandex.uz/map-widget/v1/?um=constructor%3A1a2b3c4d5e6f7g8h9i0j&source=constructor"
                                class="absolute top-0 left-0 w-full h-full border-0"
                                allowfullscreen
                                loading="lazy">
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Форма обратной связи -->
            <div>
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">Напишите нам</h2>
                    
                    <form id="contactForm" onsubmit="submitContact(event)">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ваше имя</label>
                            <input type="text" id="contactName" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Телефон</label>
                            <input type="tel" id="contactPhone" required placeholder="+998 (__) ___-__-__" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Сообщение</label>
                            <textarea id="contactMessage" rows="4" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        </div>

                        <button type="submit" id="contactSubmit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                            Отправить
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- Футер -->
    <footer class="bg-gray-900 text-white mt-16">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div>
                    <h4 class="font-bold text-lg mb-4 text-blue-400">О нас</h4>
                    <p class="text-gray-300 text-sm leading-relaxed">
                        UyUchun — это сеть магазинов бытовой техники и электроники, ориентированная на предоставление клиентам возможности покупать товары в рассрочку без лишних усилий. Сеть была основана в 2019 году. Основной концепцией магазина является «Один документ, одна цена, одна idea». Поэтому оформить банковскую рассрочку и купить желаемый товар можно без лишней суеты и дополнительных документов всего за несколько минут. Товары можно оформить в рассрочку без первоначального взноса на 12, 18 или 24 месяцев. Более того, в UyUchun широкий ассортимент товаров, сертифицированная техника мировых брендов, с заводской гарантией и последующим сервисом, а также квалифицированный персонал, который всегда готов помочь сделать клиенту правильный выбор.
                    </p>
                </div>

                <div>
                    <h4 class="font-bold text-lg mb-4 text-blue-400">Контакты</h4>
                    <ul class="space-y-3 text-gray-300 text-sm">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt text-blue-400 mt-1 mr-3"></i>
                            <span>г. Ташкент, ул. Лабзак, 86</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-phone text-blue-400 mt-1 mr-3"></i>
                            <div>
                                <div>+998-90-905-88-80</div>
                                <div>+998-99-844-07-00</div>
                            </div>
                        </li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-lg mb-4 text-blue-400">Информация</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="https://t.me/begzodsunnatovich" target="_blank" class="text-gray-300 hover:text-blue-400 transition">Задать вопрос</a></li>
                        <li><a href="https://t.me/begzodsunnatovich" target="_blank" class="text-gray-300 hover:text-blue-400 transition">Возврат и обмен</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-blue-400 transition">Доставка</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-blue-400 transition">Оплата</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-lg mb-4 text-blue-400">Мы в соцсетях</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-blue-400 transition text-2xl"><i class="fab fa-telegram"></i></a>
                        <a href="#" class="text-gray-400 hover:text-blue-400 transition text-2xl"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-gray-400 hover:text-blue-400 transition text-2xl"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-gray-400 hover:text-blue-400 transition text-2xl"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400 text-sm">
                &copy; 2024 UyUchun. Все права защищены.
            </div>
        </div>
    </footer>

    <!-- Модалки -->
    <div id="locationModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-2xl w-full p-6 relative">
            <button onclick="closeLocation()" class="absolute right-4 top-4 text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
            <h3 class="text-xl font-bold text-gray-800 mb-4">Наши магазины</h3>
            <p class="text-gray-600 mb-4"><i class="fas fa-map-marker-alt text-blue-600 mr-2"></i>Узбекистан, Ташкент, ул. Лабзак, 86</p>
            <div class="relative w-full" style="padding-bottom: 56.25%; height: 0; overflow: hidden;">
                <iframe 
                    src="https://yandex.uz/map-widget/v1/?um=constructor%3A1a2b3c4d5e6f7g8h9i0j&source=constructor"
                    class="absolute top-0 left-0 w-full h-full border-0 rounded-lg"
                    allowfullscreen
                    loading="lazy">
                </iframe>
            </div>
        </div>
    </div>

    <div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 text-center relative">
            <div class="text-6xl mb-4">✅</div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Спасибо!</h3>
            <p class="text-gray-600">Ваша заявка принята, мы свяжемся с вами в ближайшее время.</p>
            <button onclick="closeSuccess()" class="mt-4 bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
                Закрыть
            </button>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
    <script>
        function setCurrency(currency) {
            fetch('api.php?action=set_currency', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ currency: currency })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }

        function showLocation() {
            document.getElementById('locationModal').classList.remove('hidden');
        }

        function closeLocation() {
            document.getElementById('locationModal').classList.add('hidden');
        }

        function closeSuccess() {
            document.getElementById('successModal').classList.add('hidden');
        }

        function submitContact(event) {
            event.preventDefault();
            
            const name = document.getElementById('contactName').value.trim();
            const phone = document.getElementById('contactPhone').value.trim();
            const message = document.getElementById('contactMessage').value.trim();
            
            // Простая валидация
            if (!name || !phone || !message) {
                alert('Пожалуйста, заполните все поля');
                return;
            }
            
            const btn = document.getElementById('contactSubmit');
            btn.textContent = 'Отправка...';
            btn.disabled = true;
            
            fetch('api.php?action=contact', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ name, phone, message })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    document.getElementById('contactForm').reset();
                    document.getElementById('successModal').classList.remove('hidden');
                } else {
                    alert('Произошла ошибка. Попробуйте позже.');
                }
                btn.textContent = 'Отправить';
                btn.disabled = false;
            })
            .catch(() => {
                btn.textContent = 'Отправить';
                btn.disabled = false;
                alert('Произошла ошибка. Попробуйте позже.');
            });
        }

        document.querySelectorAll('.fixed.inset-0').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                }
            });
        });
    </script>
</body>
</html>