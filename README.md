# ArsenalPay for PrestaShop CMS

[Arsenal Media LLC](http://www.arsenalmedia.ru/index.php/en)

[Arsenal Pay processing center](https://arsenalpay.ru/)



## Version
1.0.0

*Has been tested on PrestaShop 1.6*


Basic feature list:

 * Allows seamlessly integrate unified payment frame into your site.
 * New payment method ArsenalPay will appear to pay for your products and services.
 * Allows to pay using mobile commerce and bank aquiring. More methods are about to become available. Please check for updates.
 * Supports two languages (Russian, English).
 
## How to install 
1. Download the ArsenalPay payment module from http://github... 
2. Upload the **arsenalpay** directory to your PrestaShop **modules** directory
2. Login to the PrestaShop admin section 
3. Go to **Modules** under **Modules** on the left side menu
3. Find **ArsenalPay** in the list of extensions 
4. Click **Install**
5. New payment method will appear while doing an order

## Settings
1. In admin section of PrestaShop choose **Modules** under **Modules** on the left side menu
2. Find **ArsenalPay** in the list of extensions
3. Click on **Configuration** 
4. Make there proper settings:
 - Fill out **Unique token**, **Sign key** fields with your received token and key.
 - Check **Frame URL** to be as `https://arsenalpay.ru/payframe/pay.php`
 - Set payment type in **src parameter** as `card` to activate payments with bank cards or `mk` to activate payments from mobile phone accounts.
 - **css parameter**. You can specify CSS file to apply it to the view of payment frame by inserting its url.
 - You can specify ip address only from which it will be allowed to receive callback requests about payments onto your site in **Allowed IP address** field.
 - Your online shop will be receiving callback requests about processed payments for automatically order status change. The callbacks will being received onto the address assigned in the field **Callback URL** of the payment module settings. Callback is set to address: `http(s)://yourSiteAddress/index.php?fc=module&module=arsenalpay&controller=callback`
 - If it is needed to check a payer order number before payment processing you should fill out the field of **Check URL** in the module settings with url-address to which ArsenalPay will be sending requests with check parameters. By default the address is the same with **Callback URL**. 
 - Set **Frame mode** as `1` to display payment frame inside your site, otherwise a payer will be redirected directly to the payment frame url.
 - You can adjust **width**, **height**, **frameborder** and **scrolling** of ArsenalPay payment frame by setting iframe parameters. For instance, you can insert string in format: `width='100%' height'500' frameborder='0' scrolling='no'`. Go to html standard reference for more detailes about iframe parameters.
5. Finally, save your settings by clicking on **Update Settings**

## How to uninstall
1. In admin section of PrestaShop choose **Modules** under **Modules** on the left side menu
2. Find "Arsenalpay" in the list of extensions
3. Open dropdown list by clicking on the arrow near **Configuration** button
4. Find **Delete** in this list and click on it.

## Usage
After successful install and proper settings new choice of payment method with ArsenalPay will appear on your site. To make payment for an order a payer will need to:

1. Choose goods from the shop catalog.
2. Go into the order page.
3. Choose the ArsenalPay payment method.
4. Check the order detailes and confirm the order.
5. After filling out the information depending on the payment type he will receive SMS about payment confirmation or will be redirected to the page with the result of his payment.

------------------
### О МОДУЛЕ
* Модуль платежной системы ArsenalPay под Joomshopping позволяет легко встроить платежную страницу на Ваш сайт.
* После установки модуля у Вас появится новый вариант оплаты товаров и услуг через платежную систему ArsenalPay.
* Платежная система ArsenalPay позволяет совершать оплату с различных источников списания средств: мобильных номеров (МТС/Мегафон/Билайн/TELE2), пластиковых карт (VISA/MasterCard/Maestro). Перечень доступных источников средств постоянно пополняется. Следите за обновлениями.
* Модуль поддерживает русский и английский языки.

За более подробной информацией о платежной системе ArsenalPay обращайтесь по адресу [arsenalpay.ru](http://arsenalpay.ru)

### УСТАНОВКА
1. Скопируйте содержимое архива модуля в каталог **modules** Вашего сайта;
2. Зайдите в администрирование PrestaShop;
3. Выберите закладку **Модули** в левом меню;
4. Найдите в списке модуль **ArsenalPay**;
5. Нажмите **Установить**;
6. После установки платежная система сразу появится в списке при оформлении заказа.

### НАСТРОЙКА
1. Зайдите в администрирование Prestashop;
2. Выберите закладку **Модули** в левом меню;
3. Найдите в списке модуль Arsenalpay;
5. Нажмите на кнопку **Настройть**;
6. Заполните необходимые настройки:
  - Заполните поля **Уникальный токен** и **Ключ (key) **, присвоенными Вам токеном и ключом для подписи.
 - Проверьте, **URL-адрес фрейма** должен быть установлен как `https://arsenalpay.ru/payframe/pay.php`
 - Установите тип оплаты в поле **Параметр src** как `card` для активации платежей с пластиковых карт или  как `mk` — платежей с аккаунтов мобильных телефонов.
 - Вы можете задать **Параметр css** для применения к отображению платежного фрейма, указав url css-файла.
 - Вы можете задать ip-адрес, только с которого будут разрешены обратные запросы о совершаемых платежах, в поле **Разрешенный IP-адрес**.
 - Ваш интернет-магазин будет получать уведомления о совершенных платежах: на адрес, указанный в поле **URL для обратного запроса**, от ArsenalPay будет поступать запрос с результатом платежа для фиксирования статусов заказа в системе предприятия. Обратный запрос настроен на адрес: `http(s)://адресВашегоСайта/index.php?fc=module&module=arsenalpay&controller=callback`
 - При необходимости осуществления проверки номера заказа перед проведением платежа, Вы должны заполнить поле **URL для проверки**, на который от ArsenalPay будет поступать запрос на проверку. По умолчанию значение совпадает с **URL для обратного запроса**.
 - Вы можете устанавливать **Режим отображения фрейма**. Значение `1` соответствует отображению фрейма внутри Вашего сайта , иначе пользователь будет перенаправляться напрямую на адрес платежного фрейма.
 - Вы можете подгонять ширину, высоту, границу и прокрутку платежного фрейма, задавая соответствующие значения параметров iframe в формате ` width='100%' height'500' frameborder='0' scrolling='no'`. За более пдоробной информацией о параметрах iframe обращайтесь к стандарту html.
7. Нажмите **Обновить настройки**.

### УДАЛЕНИЕ
1. Зайдите в администрирование PrestaShop;
2. Выберите закладку **Модули** в левом меню;
3. Найдите в списке модуль **ArsenalPay**;
4. Нажмите на кнопку выпадающего списка справа от кнопки **Настроить**;
5. Нажмите кнопку **Удалить**.

### ИСПОЛЬЗОВАНИЕ
После успешной установки и настройки модуля на сайте появится возможность выбора платежной системы ArsenalPay.
Для оплаты заказа с помощью платежной системы ArsenalPay нужно:

1. Выбрать из каталога товар, который нужно купить.
2. Перейти на страницу оформления заказа (покупки).
3. В разделе "Платежные системы" выбрать платежную систему ArsenalPay.
4. Перейти на страницу подтверждения введенных данных и ввода источника списания средств (мобильный номер, пластиковая карта и т.д.).
5. После ввода данных об источнике платежа, в зависимости от его типа, либо придет СМС о подтверждении платежа, либо покупатель будет перенаправлен на страницу с результатом платежа.
 



 
