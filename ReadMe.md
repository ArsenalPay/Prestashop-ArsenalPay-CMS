# ArsenalPay for PrestaShop CMS

[Arsenal Media LLC](http://www.arsenalmedia.ru/index.php/en)

[Arsenal Pay processing center](https://arsenalpay.ru/)



## Version
1.0.0


Basic feature list:

 * Module allows seamlessly integrate unified payment frame into your site 
 * New payment method will appear to pay for your products and services
 * Allows to pay using mobile commerce and bank aquiring. More are to come.
 
## How to install 
1. Download the ArsenalPay payment module from http://github... to 
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
4. Make proper settings and click on **Update Settings**

## How to uninstall
1. In admin section of PrestaShop choose **Modules** under **Modules** on the left side menu
2. Find "Arsenalpay" in the list of extensions
3. Open dropdown list by clicking on the arrow near **Configuration** button
4. Find **Delete** in this list and click on it.

##Usage
After successful installation and proper settings new choice of payment method with ArsenalPay will appear on your site. To make payment for an order you will need:
1. Choose goods from the shop catalog.
2. Go into the order page.
3. Choose the ArsenalPay payment method.
4. Check the order detailes and confirm the order.
5. After filling out the information depending on your payment type you will receive SMS about payment confirmation or will be redirected to the page with the result of your payment.
6. Your online shop can receive callbacks about processed payments if needed. The callbacks will be received for fixing payment statuses onto the address assigned in the field **Callback URL** of the payment module settings.
Callback address is `http(s)://адресВашегоСайта/index.php?fc=module&module=arsenalpay&controller=callback`
The callback code can be changed by modifying the file modules/arsenalpay/controllers/front/callback.php.
7. If it is needed to make checking of payer order number before the payment processing you should fill out the field of **Check URL** in the module settings with address to which ArsenalPay will be sending requests with check parameters. By default the address is the same with **Callback URL**. 

------------------
### О МОДУЛЕ
* Модуль платежной системы ArsenalPay под Prestashop позволяет легко встроить платежную страницу на Ваш сайт.
* После установки модуля у Вас появится новый вариант оплаты товаров и услуг через платежную систему ArsenalPay.
* Платежная система ArsenalPay позволяет совершать оплату с различных источников списания средств: мобильных номеров (МТС/Мегафон/Билайн/TELE2), пластиковых карт (VISA/MasterCard/Maestro). Перечень доступных источников средств постоянно пополняется. Следите за обновлениями.

За более подробной информацией о платежной системе ArsenalPay обращайтесь по адресу [arsenalpay.ru](http://arsenalpay.ru)

### УСТАНОВКА
1. Скопируйте содержимое архива модуля в каталог **\your_presashop\modules\**
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
6. Заполните необходимые настройки и нажмите сохранить.

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
5. После ввода данных об источнике платежа в зависимости от его типа, Вам либо придет СМС о подтверждении платежа, либо Вы будуете перенаправлены на страницу с результатом платежа.
6. При необходимости, предприятие может получать уведомления о совершенных платежах: на адрес, указанный в поле "Url колбэка", от ArsenalPay поступит запрос с результатом платежа для фиксирования его в системе предприятия.
Колбэк доступен по адресу `http(s)://адресВашегоСайта/index.php?fc=module&module=arsenalpay&controller=callback`
Изменить код можно в файле modules/arsenalpay/controllers/front/callback.php
7. При необходимости осуществления проверки номера получателя перед совершением платежа, Вы должны заполнить поле "Url проверки номера получателя", на который от ArsenalPay поступит запрос на проверку.
 



 
