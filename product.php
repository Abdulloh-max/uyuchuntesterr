<?php
require_once 'config.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$products = getProducts();
$product = null;

foreach ($products as $p) {
    if ($p['id'] === $id) {
        $product = $p;
        break;
    }
}

if (!$product) {
    header('Location: index.php');
    exit;
}

$currentCurrency = isset($_SESSION['currency']) ? $_SESSION['currency'] : 'UZS';
$priceInCurrency = convertCurrency($product['price'], 'UZS');
$formattedPrice = formatPrice($priceInCurrency);

// Похожие товары
$similarProducts = array_filter($products, function($p) use ($product) {
    return $p['id'] !== $product['id'] && $p['brand'] === $product['brand'];
});
$similarProducts = array_slice(array_values($similarProducts), 0, 4);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['name']; ?> - UyUchun</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <!-- Шапка (такая же как в index.php) -->
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

    <main class="container mx-auto px-4 py-6">
        <!-- Хлебные крошки -->
        <nav class="text-sm mb-6">
            <a href="index.php" class="text-blue-600 hover:underline">Главная</a>
            <span class="text-gray-400 mx-2">/</span>
            <a href="index.php" class="text-blue-600 hover:underline">Каталог</a>
            <span class="text-gray-400 mx-2">/</span>
            <span class="text-gray-600"><?php echo $product['name']; ?></span>
        </nav>

        <!-- Карточка товара -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="grid md:grid-cols-2 gap-8 p-6">
                <!-- Изображение -->
                <!-- ИЗОБРАЖЕНИЕ ТОВАРА: <?php echo $product['name']; ?> -->
                <div class="bg-gray-50 rounded-xl p-8 flex items-center justify-center">
                    <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="max-h-[400px] object-contain">
                </div>

                <!-- Информация -->
                <div>
                    <div class="text-sm text-blue-600 font-semibold mb-2"><?php echo $product['brand']; ?></div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800"><?php echo $product['name']; ?></h1>
                    
                    <div class="mt-4">
                        <span class="text-3xl font-bold text-blue-600"><?php echo $formattedPrice; ?></span>
                    </div>

                    <p class="text-gray-600 mt-4 leading-relaxed"><?php echo $product['description']; ?></p>

                    <!-- Характеристики -->
                    <div class="mt-6">
                        <h3 class="font-bold text-gray-800 mb-3">Характеристики</h3>
                        <div class="bg-gray-50 rounded-xl p-4 space-y-2">
                            <?php foreach ($product['specs'] as $key => $value): ?>
                            <div class="flex justify-between py-1 border-b border-gray-200 last:border-0">
                                <span class="text-gray-600"><?php echo $key; ?></span>
                                <span class="font-medium text-gray-800"><?php echo $value; ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Кнопки -->
                    <div class="mt-6 flex flex-col sm:flex-row gap-3">
                        <button onclick="quickOrder('<?php echo $product['id']; ?>', '<?php echo addslashes($product['name']); ?>', '<?php echo $product['price']; ?>')" class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-blue-700 transition">
                            <i class="fas fa-shopping-cart mr-2"></i> Купить
                        </button>
                        <button onclick="quickOrder('<?php echo $product['id']; ?>', '<?php echo addslashes($product['name']); ?>', '<?php echo $product['price']; ?>')" class="flex-1 bg-blue-50 text-blue-600 px-6 py-3 rounded-xl font-semibold hover:bg-blue-100 transition border border-blue-200">
                            <i class="fas fa-clock mr-2"></i> В рассрочку
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Похожие товары -->
        <?php if (!empty($similarProducts)): ?>
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Похожие товары</h2>
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <?php foreach ($similarProducts as $similar): ?>
                <!-- ИЗОБРАЖЕНИЕ ТОВАРА: <?php echo $similar['name']; ?> -->
                <a href="product.php?id=<?php echo $similar['id']; ?>" class="bg-white rounded-xl shadow-md hover:shadow-lg transition p-4">
                    <div class="h-40 bg-gray-100 rounded-lg flex items-center justify-center p-3">
                        <img src="<?php echo $similar['image']; ?>" alt="<?php echo $similar['name']; ?>" class="max-h-full object-contain">
                    </div>
                    <div class="mt-3">
                        <div class="text-xs text-blue-600 font-semibold"><?php echo $similar['brand']; ?></div>
                        <h3 class="font-semibold text-gray-800 text-sm mt-1"><?php echo $similar['name']; ?></h3>
                        <div class="mt-2 font-bold text-blue-600">
                            <?php echo formatPrice(convertCurrency($similar['price'], 'UZS')); ?>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </main>

    <!-- Футер (такой же как в index.php) -->
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

    <!-- Модальные окна (такие же как в index.php) -->
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

        function quickOrder(id, name, price) {
            document.getElementById('quickProductId').value = id;
            document.getElementById('quickProductName').value = name;
            document.getElementById('quickPrice').value = '<?php echo $formattedPrice; ?>';
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
            
            fetch('api.php?action=order', {
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
    </script>
</body>
</html>