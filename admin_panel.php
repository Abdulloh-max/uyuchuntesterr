<?php
require_once 'config.php';

// Проверка аутентификации
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Если есть POST запрос на логин
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login']) && isset($_POST['password'])) {
        if (loginAdmin($_POST['login'], $_POST['password'])) {
            header('Location: admin_panel.php');
            exit;
        } else {
            $error = 'Неверный логин или пароль';
        }
    }
    
    // Показываем форму входа
    ?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Вход в админ-панель - UyUchun</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-100">
        <div class="min-h-screen flex items-center justify-center">
            <div class="bg-white p-8 rounded-2xl shadow-lg max-w-sm w-full">
                <h1 class="text-2xl font-bold text-center text-blue-600 mb-6">UyUchun</h1>
                <h2 class="text-lg font-semibold text-gray-800 mb-4 text-center">Вход в админ-панель</h2>
                
                <?php if (isset($error)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Логин</label>
                        <input type="text" name="login" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Пароль</label>
                        <input type="password" name="password" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
                        Войти
                    </button>
                </form>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Выход
if (isset($_GET['logout'])) {
    logoutAdmin();
    header('Location: admin_panel.php');
    exit;
}

// Получение данных
$orders = json_decode(file_get_contents(ORDERS_FILE), true);
$messages = json_decode(file_get_contents(MESSAGES_FILE), true);

// Обновление статуса
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_status' && isset($_POST['order_id']) && isset($_POST['status'])) {
        $orderId = intval($_POST['order_id']);
        foreach ($orders as &$order) {
            if ($order['id'] === $orderId) {
                $order['status'] = $_POST['status'];
                break;
            }
        }
        file_put_contents(ORDERS_FILE, json_encode($orders, JSON_PRETTY_PRINT));
        header('Location: admin_panel.php');
        exit;
    }
    
    if ($_POST['action'] === 'delete_order' && isset($_POST['order_id'])) {
        $orderId = intval($_POST['order_id']);
        $orders = array_filter($orders, function($o) use ($orderId) {
            return $o['id'] !== $orderId;
        });
        $orders = array_values($orders);
        file_put_contents(ORDERS_FILE, json_encode($orders, JSON_PRETTY_PRINT));
        header('Location: admin_panel.php');
        exit;
    }
    
    if ($_POST['action'] === 'delete_message' && isset($_POST['message_id'])) {
        $messageId = intval($_POST['message_id']);
        $messages = array_filter($messages, function($m) use ($messageId) {
            return $m['id'] !== $messageId;
        });
        $messages = array_values($messages);
        file_put_contents(MESSAGES_FILE, json_encode($messages, JSON_PRETTY_PRINT));
        header('Location: admin_panel.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель - UyUchun</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Админ-панель UyUchun</h1>
            <a href="?logout=1" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">
                <i class="fas fa-sign-out-alt mr-2"></i> Выйти
            </a>
        </div>

        <!-- Заказы -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-shopping-cart text-blue-600 mr-2"></i>
                    Заказы (<?php echo count($orders); ?>)
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Имя</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Телефон</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Товар</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Цена</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Статус</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Дата</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-gray-500">Нет заказов</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach (array_reverse($orders) as $order): ?>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm">#<?php echo $order['id']; ?></td>
                                <td class="px-6 py-4 text-sm font-medium"><?php echo htmlspecialchars($order['name']); ?></td>
                                <td class="px-6 py-4 text-sm">
                                    <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $order['phone']); ?>" class="text-blue-600 hover:text-blue-800 font-medium">
                                        <i class="fas fa-phone mr-1"></i> <?php echo htmlspecialchars($order['phone']); ?>
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-sm"><?php echo htmlspecialchars($order['product']); ?></td>
                                <td class="px-6 py-4 text-sm font-semibold"><?php echo htmlspecialchars($order['price']); ?></td>
                                <td class="px-6 py-4 text-sm">
                                    <form method="POST" class="inline">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                        <select name="status" onchange="this.form.submit()" class="text-sm border border-gray-300 rounded px-2 py-1 <?php 
                                            echo $order['status'] === 'Новый' ? 'bg-yellow-50 text-yellow-800' : 
                                                ($order['status'] === 'В обработке' ? 'bg-blue-50 text-blue-800' : 
                                                'bg-green-50 text-green-800'); 
                                        ?>">
                                            <option value="Новый" <?php echo $order['status'] === 'Новый' ? 'selected' : ''; ?>>Новый</option>
                                            <option value="В обработке" <?php echo $order['status'] === 'В обработке' ? 'selected' : ''; ?>>В обработке</option>
                                            <option value="Завершен" <?php echo $order['status'] === 'Завершен' ? 'selected' : ''; ?>>Завершен</option>
                                        </select>
                                    </form>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500"><?php echo $order['date']; ?></td>
                                <td class="px-6 py-4 text-sm">
                                    <button onclick="confirmDelete('order', <?php echo $order['id']; ?>)" class="text-red-500 hover:text-red-700">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Сообщения -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-envelope text-blue-600 mr-2"></i>
                    Сообщения (<?php echo count($messages); ?>)
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Имя</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Телефон</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Сообщение</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Дата</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($messages)): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">Нет сообщений</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach (array_reverse($messages) as $message): ?>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm">#<?php echo $message['id']; ?></td>
                                <td class="px-6 py-4 text-sm font-medium"><?php echo htmlspecialchars($message['name']); ?></td>
                                <td class="px-6 py-4 text-sm">
                                    <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $message['phone']); ?>" class="text-blue-600 hover:text-blue-800 font-medium">
                                        <i class="fas fa-phone mr-1"></i> <?php echo htmlspecialchars($message['phone']); ?>
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-sm max-w-xs truncate"><?php echo htmlspecialchars($message['message']); ?></td>
                                <td class="px-6 py-4 text-sm text-gray-500"><?php echo $message['date']; ?></td>
                                <td class="px-6 py-4 text-sm">
                                    <button onclick="confirmDelete('message', <?php echo $message['id']; ?>)" class="text-red-500 hover:text-red-700">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Модальное окно подтверждения -->
    <div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 relative">
            <button onclick="closeConfirm()" class="absolute right-4 top-4 text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
            <div class="text-center">
                <div class="text-5xl mb-4">⚠️</div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Подтверждение удаления</h3>
                <p class="text-gray-600 mb-6">Вы уверены, что хотите удалить эту заявку?</p>
                <div class="flex gap-3 justify-center">
                    <button onclick="closeConfirm()" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Отмена
                    </button>
                    <button id="confirmDeleteBtn" class="px-6 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                        Удалить
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let deleteType = '';
        let deleteId = 0;

        function confirmDelete(type, id) {
            deleteType = type;
            deleteId = id;
            document.getElementById('confirmModal').classList.remove('hidden');
        }

        function closeConfirm() {
            document.getElementById('confirmModal').classList.add('hidden');
        }

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            const form = document.createElement('form');
            form.method = 'POST';
            
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = deleteType === 'order' ? 'delete_order' : 'delete_message';
            
            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = deleteType === 'order' ? 'order_id' : 'message_id';
            idInput.value = deleteId;
            
            form.appendChild(actionInput);
            form.appendChild(idInput);
            document.body.appendChild(form);
            form.submit();
        });

        document.querySelector('#confirmModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeConfirm();
            }
        });
    </script>
</body>
</html>