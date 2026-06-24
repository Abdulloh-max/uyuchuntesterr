<?php
require_once '../config.php';
$products = getProducts();
$brands = getBrands();

// Текущая валюта
$currentCurrency = isset($_SESSION['currency']) ? $_SESSION['currency'] : 'UZS';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UyUchun - Интернет-магазин бытовой техники</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <!-- Шапка сайта -->
    <header class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3">
            <div class="flex items-center justify-between">
                <!-- Логотип -->
                <div class="flex items-center space-x-3">
                    <a href="/" class="flex items-center">
                        <div class="logo-container" style="border-radius: 24px; overflow: hidden; width: 50px; height: 50px;">
                            <img src="https://i.postimg.cc/wyMVXxw6/avatar.png" alt="UyUchun" class="w-full h-full object-cover">
                        </div>
                        <span class="text-2xl font-bold text-blue-600 ml-2">UyUchun</span>
                    </a>
                </div>

                <!-- Поиск -->
                <div class="hidden md:flex flex-1 max-w-xl mx-6">
                    <div class="relative w-full">
                        <input type="text" id="searchInput" placeholder="Поиск товаров..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <i class="fas fa-search absolute right-3 top-3 text-gray-400"></i>
                    </div>
                </div>

                <!-- Правая часть -->
                <div class="flex items-center space-x-4">
                    <!-- Переключатель валюты -->
                    <div class="flex items-center space-x-1 bg-gray-100 rounded-lg p-1">
                        <button onclick="setCurrency('UZS')" class="currency-btn px-3 py-1 rounded-md text-sm font-medium <?php echo $currentCurrency === 'UZS' ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-200'; ?>" data-currency="UZS">UZS</button>
                        <button onclick="setCurrency('USD')" class="currency-btn px-3 py-1 rounded-md text-sm font-medium <?php echo $currentCurrency === 'USD' ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-200'; ?>" data-currency="USD">$</button>
                    </div>

                    <!-- Иконки -->
                    <button onclick="showLocation()" class="text-gray-600 hover:text-blue-600 transition" title="Наши магазины">
                        <i class="fas fa-map-marker-alt text-xl"></i>
                    </button>
                    <a href="../contact.php" class="text-gray-600 hover:text-blue-600 transition">
                        <i class="fas fa-envelope text-xl"></i>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Основной контент -->
    <main class="container mx-auto px-4 py-6">
        <div class="flex flex-col md:flex-row gap-6">
            <!-- Левая панель - Фильтр по брендам -->
            <aside class="md:w-64 flex-shrink-0">
                <div class="bg-white rounded-xl shadow-md p-4 sticky top-24">
                    <h3 class="font-bold text-lg text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-filter text-blue-600 mr-2"></i>
                        Бренды
                    </h3>
                    <div class="space-y-1 max-h-[600px] overflow-y-auto">
                        <button onclick="filterByBrand('all')" class="brand-filter w-full text-left px-3 py-2 rounded-lg hover:bg-blue-50 transition text-sm font-medium text-gray-700 hover:text-blue-600 active-brand-filter" data-brand="all">
                            <i class="fas fa-th-large mr-2"></i>Все товары
                        </button>
                        <?php foreach ($brands as $brand): ?>
                        <button onclick="filterByBrand('<?php echo $brand; ?>')" class="brand-filter w-full text-left px-3 py-2 rounded-lg hover:bg-blue-50 transition text-sm text-gray-600 hover:text-blue-600" data-brand="<?php echo $brand; ?>">
                            <?php echo $brand; ?>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </aside>

            <!-- Правая часть - Каталог -->
            <div class="flex-1">
                <!-- Баннер -->
                <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-2xl p-6 mb-6 text-white shadow-lg">
                    <div class="flex flex-col md:flex-row items-center justify-between">
                        <div>
                            <h2 class="text-2xl md:text-3xl font-bold">Скидки до 50%</h2>
                            <p class="text-blue-100 mt-1">На технику Apple и Samsung</p>
                            <button class="mt-3 bg-white text-blue-700 px-6 py-2 rounded-full font-semibold hover:bg-blue-50 transition">
                                Подробнее
                            </button>
                        </div>
                        <div class="mt-4 md:mt-0">
                            <i class="fas fa-tags text-6xl text-blue-300 opacity-50"></i>
                        </div>
                    </div>
                </div>

                <!-- Список товаров -->
                <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6" id="productsGrid">
                    <?php foreach ($products as $product): ?>
                    <div class="product-card bg-white rounded-xl shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden" data-brand="<?php echo $product['brand']; ?>">
                        <a href="../product.php?id=<?php echo $product['id']; ?>">
                            <div class="h-48 bg-gray-100 relative overflow-hidden">
                                <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="w-full h-full object-contain p-3 hover:scale-105 transition-transform duration-300">
                            </div>
                            <div class="p-4">
                                <div class="text-xs text-blue-600 font-semibold"><?php echo $product['brand']; ?></div>
                                <h3 class="font-semibold text-gray-800 text-sm mt-1 line-clamp-2"><?php echo $product['name']; ?></h3>
                                <div class="mt-2 flex items-center justify-between">
                                    <span class="text-lg font-bold text-blue-600 product-price" data-price="<?php echo $product['price']; ?>">
                                        <?php echo formatPrice(convertCurrency($product['price'], 'UZS')); ?>
                                    </span>
                                    <button onclick="quickOrder('<?php echo $product['id']; ?>', '<?php echo addslashes($product['name']); ?>', '<?php echo $product['price']; ?>')" class="bg-blue-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-blue-700 transition">
                                        Купить
                                    </button>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
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
                        UyUchun — это сеть магазинов бытовой техники и электроники, ориентированная на предоставление клиентам возможности покупать товары в рассрочку без лишних усилий. Сеть была основана в 2019 году. Основной концепцией магазина является «Один документ, одна цена, одна idea».
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
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-4 text-blue-400">Мы в соцсетях</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-blue-400 transition text-2xl"><i class="fab fa-telegram"></i></a>
                        <a href="#" class="text-gray-400 hover:text-blue-400 transition text-2xl"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400 text-sm">
                &copy; 2024 UyUchun. Все права защищены.
            </div>
        </div>
    </footer>

    <!-- Модальные окна -->
    <div id="quickOrderModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 relative">
            <button onclick="closeQuickOrder()" class="absolute right-4 top-4 text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
            <h3 class="text-xl font-bold text-gray-800 mb-4">Быстрый заказ</h3>
            <form id="quickOrderForm" onsubmit="submitQuickOrder(event)">
                <input type="hidden" id="quickProductId" name="product_id">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Товар</label>
                    <input type="text" id="quickProductName" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ваше имя</label>
                    <input type="text" id="quickName" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Телефон</label>
                    <input type="tel" id="quickPhone" required placeholder="+998 (__) ___-__-__" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Цена</label>
                    <input type="text" id="quickPrice" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50">
                </div>
                <button type="submit" id="quickOrderSubmit" class="w-full bg-blue-600 text-white py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
                    Оформить заказ
                </button>
            </form>
        </div>
    </div>

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

    <script src="../assets/js/main.js"></script>
    <script>
        function setCurrency(currency) {
            fetch('../api.php?action=set_currency', {
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

        function filterByBrand(brand) {
            document.querySelectorAll('.brand-filter').forEach(btn => {
                btn.classList.remove('active-brand-filter', 'text-blue-600', 'bg-blue-50');
                btn.classList.add('text-gray-600');
            });
            
            const activeBtn = document.querySelector(`.brand-filter[data-brand="${brand}"]`);
            if (activeBtn) {
                activeBtn.classList.add('active-brand-filter', 'text-blue-600', 'bg-blue-50');
                activeBtn.classList.remove('text-gray-600');
            }
            
            document.querySelectorAll('.product-card').forEach(card => {
                if (brand === 'all' || card.dataset.brand === brand) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function quickOrder(id, name, price) {
            document.getElementById('quickProductId').value = id;
            document.getElementById('quickProductName').value = name;
            document.getElementById('quickPrice').value = '<?php echo formatPrice(convertCurrency($product["price"], "UZS")); ?>';
            document.getElementById('quickOrderModal').classList.remove('hidden');
        }

        function closeQuickOrder() {
            document.getElementById('quickOrderModal').classList.add('hidden');
        }

        function submitQuickOrder(event) {
            event.preventDefault();
            const formData = new FormData(document.getElementById('quickOrderForm'));
            const data = {
                name: formData.get('name'),
                phone: formData.get('phone'),
                product_id: formData.get('product_id'),
                product: document.getElementById('quickProductName').value,
                price: document.getElementById('quickPrice').value
            };
            
            const btn = document.getElementById('quickOrderSubmit');
            btn.textContent = 'Отправка...';
            btn.disabled = true;
            
            fetch('../api.php?action=order', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    closeQuickOrder();
                    document.getElementById('successModal').classList.remove('hidden');
                    document.getElementById('quickOrderForm').reset();
                }
                btn.textContent = 'Оформить заказ';
                btn.disabled = false;
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

        document.querySelectorAll('.fixed.inset-0').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const allBtn = document.querySelector('.brand-filter[data-brand="all"]');
            if (allBtn) {
                allBtn.classList.add('active-brand-filter', 'text-blue-600', 'bg-blue-50');
                allBtn.classList.remove('text-gray-600');
            }
        });
    </script>
</body>
</html>