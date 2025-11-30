# Fakturcho

## Това е Фактурчо. Интелигентният финансоф помощник на стартъпи и малки бизнеси.

## Tech Stack
- PHP
- MySQL
- HTML/CSS/JavaScript

## Ползване
На някои места в `config.php` и `forgot_password.php` трябва да се добавят данни от потребителя.


## Структура
- PHP файловете изграждат структурата на сайта
- Двата Python файла се интегрират с базата и със сайта, като обработват файловете на потребителя
- `forgot_password.php` изпраща имейли с Gmail посредством SMTP


## Dependencies

### Python

```
Pillow>=8.0.0
pytesseract>=0.3.8
pdf2image>=1.16.0
opencv-python>=4.5.0
numpy>=1.19.0
```
OR
``` shell
pip install -r requirements.txt
```
### PHP
``` shell
composer require swiftmailer/swiftmailer
```

Направено с много любов за Rose Valley Hackathon Казанлък 2025 


