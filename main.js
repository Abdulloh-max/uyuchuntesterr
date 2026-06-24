// main.js
// Основные скрипты UyUchun

// Маска для телефона
document.addEventListener('DOMContentLoaded', function() {
    const phoneInputs = document.querySelectorAll('input[type="tel"]');
    
    phoneInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            let value = this.value.replace(/\D/g, '');
            
            if (value.length === 0) {
                this.value = '+998';
                return;
            }
            
            let formatted = '+998';
            
            if (value.length > 3) {
                const rest = value.substring(3);
                const parts = rest.match(/(\d{0,2})(\d{0,3})(\d{0,2})(\d{0,2})/);
                
                if (parts) {
                    if (parts[1]) formatted += ' (' + parts[1];
                    if (parts[2]) formatted += ') ' + parts[2];
                    if (parts[3]) formatted += '-' + parts[3];
                    if (parts[4]) formatted += '-' + parts[4];
                }
            } else {
                formatted += value.substring(3);
            }
            
            this.value = formatted;
        });
        
        // Установка начального значения
        if (!input.value) {
            input.value = '+998';
        }
        
        // Обработка удаления
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && this.value === '+998') {
                e.preventDefault();
            }
        });
    });
});

// Функция для конвертации валют через API
async function convertPrice(price, from, to) {
    try {
        const response = await fetch('api.php?action=convert', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ price, from, to })
        });
        const data = await response.json();
        return data.result;
    } catch (error) {
        console.error('Ошибка конвертации:', error);
        return price;
    }
}

// Функция для поиска товаров
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            const cards = document.querySelectorAll('.product-card');
            
            cards.forEach(card => {
                const name = card.querySelector('h3')?.textContent?.toLowerCase() || '';
                const brand = card.querySelector('.text-blue-600')?.textContent?.toLowerCase() || '';
                
                if (name.includes(query) || brand.includes(query) || query === '') {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
});

// Автоматическое закрытие модальных окон при клике вне
document.addEventListener('click', function(e) {
    document.querySelectorAll('.fixed.inset-0').forEach(modal => {
        if (e.target === modal) {
            modal.classList.add('hidden');
        }
    });
});

// Обработка отправки форм через AJAX
document.addEventListener('DOMContentLoaded', function() {
    // Автоматическая обработка всех форм с data-ajax
    document.querySelectorAll('form[data-ajax]').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            
            try {
                const response = await fetch(this.action, {
                    method: this.method || 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Показываем модалку успеха
                    const successModal = document.getElementById('successModal');
                    if (successModal) {
                        successModal.classList.remove('hidden');
                    }
                    this.reset();
                    
                    // Обновляем маску телефона
                    const phoneInput = this.querySelector('input[type="tel"]');
                    if (phoneInput) {
                        phoneInput.value = '+998';
                    }
                } else {
                    alert('Произошла ошибка. Попробуйте позже.');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                alert('Произошла ошибка. Попробуйте позже.');
            }
        });
    });
});

// Поддержка клавиши Escape для закрытия модалок
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.fixed.inset-0:not(.hidden)').forEach(modal => {
            modal.classList.add('hidden');
        });
    }
});