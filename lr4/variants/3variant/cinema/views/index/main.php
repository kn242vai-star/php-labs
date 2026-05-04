<div class="page-home">
    <h1>Cinema MVC</h1>
    <p class="page-home__subtitle">Ласкаво просимо до нашого кінотеатру!</p>

    <div class="card-grid">
        <div class="card">
            <h3 class="card__title">Структура MVC</h3>
            <p class="card__text">
                Додаток побудований за паттерном Model-View-Controller.
                Єдина точка входу <code>index.php</code>, маршрутизація через
                <code>?route=controller/action</code>.
            </p>
        </div>

        <div class="card">
            <h3 class="card__title">Реєстрація глядачів</h3>
            <p class="card__text">
                Форма з 9 полями та 4 різними типами вводу.
                Валідація: англійські імена, збіг паролів, формат E-mail, вік залежно від статі.
            </p>
            <a href="index.php?route=regform/form" class="btn btn--small">Перейти</a>
        </div>

        <div class="card">
            <h3 class="card__title">Параметри запиту</h3>
            <p class="card__text">
                Перегляд GET та POST параметрів. Форма для надсилання
                POST-запиту з довільними даними.
            </p>
            <a href="index.php?route=reqview/showrequest" class="btn btn--small">Перейти</a>
        </div>

        <div class="card">
            <h3 class="card__title">Квитки та бронювання</h3>
            <p class="card__text">
                Оберіть фільм, час сеансу та зручні місця у залі.
                Після підтвердження квиток буде куплено та місце заброньовано.
            </p>
            <a href="index.php?route=ticket/booking" class="btn btn--small">Перейти</a>
        </div>

        <div class="card">
            <h3 class="card__title">Сесії та Cookie</h3>
            <p class="card__text">
                Зміна кольору фону через сесію. Привітання користувача
                через cookie на всіх сторінках.
            </p>
            <a href="index.php?route=settings/color" class="btn btn--small">Перейти</a>
        </div>
    </div>

    <div class="info-block">
        <h2>Афіша кінотеатру</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Фільм</th>
                    <th>Жанр</th>
                    <th>Тривалість</th>
                    <th>Рейтинг</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>Інтерстеллар</td><td>Наукова фантастика</td><td>169 хв</td><td>PG-13</td></tr>
                <tr><td>Титанік</td><td>Романтика, Драма</td><td>195 хв</td><td>PG-13</td></tr>
                <tr><td>Темний лицар</td><td>Бойовик, Драма</td><td>152 хв</td><td>PG-13</td></tr>
                <tr><td>Шрек</td><td>Анімація, Комедія</td><td>90 хв</td><td>G</td></tr>
            </tbody>
        </table>
    </div>

    <div class="info-block">
        <h2>Наші зали</h2>
        <ul>
            <li><strong>Зал 1:</strong> Стандартний зал на 100 місць, 2D проекція</li>
            <li><strong>Зал 2:</strong> VIP зал на 50 місць, 3D проекція, комфортні крісла</li>
            <li><strong>Зал 3:</strong> IMAX зал на 200 місць, ультра HD проекція</li>
        </ul>
    </div>

    <div class="info-block">
        <h2>Розклад сеансів</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Час</th>
                    <th>Фільм</th>
                    <th>Зал</th>
                    <th>Ціна</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>10:00</td><td>Шрек</td><td>Зал 1</td><td>50 грн</td></tr>
                <tr><td>12:30</td><td>Інтерстеллар</td><td>Зал 3</td><td>120 грн</td></tr>
                <tr><td>15:00</td><td>Титанік</td><td>Зал 2</td><td>100 грн</td></tr>
                <tr><td>18:00</td><td>Темний лицар</td><td>Зал 1</td><td>80 грн</td></tr>
                <tr><td>20:30</td><td>Інтерстеллар</td><td>Зал 3</td><td>120 грн</td></tr>
            </tbody>
        </table>
    </div>

    <div class="info-block">
        <h2>Класи MVC</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Клас</th>
                    <th>Призначення</th>
                </tr>
            </thead>
            <tbody>
                <tr><td><code>Application</code></td><td>Завантаження додатку, виклик контролера</td></tr>
                <tr><td><code>Router</code></td><td>Розбір URL &rarr; контролер + дія</td></tr>
                <tr><td><code>Request</code></td><td>Обгортка для <code>$_GET</code> / <code>$_POST</code></td></tr>
                <tr><td><code>Controller</code></td><td>Базовий клас контролера</td></tr>
                <tr><td><code>PageController</code></td><td>Контролер для сторінок</td></tr>
                <tr><td><code>View</code></td><td>Базовий клас для відображення</td></tr>
                <tr><td><code>PageView</code></td><td>Шаблон сторінки з layout</td></tr>
            </tbody>
        </table>
    </div>
</div>
