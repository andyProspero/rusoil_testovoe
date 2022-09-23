<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Testo");

// testovoe
echo '<h1>Тестовое задание</h1>';


// подключаем bootstrap (v. 5.0.0)
use Bitrix\Main\UI\Extension;
Extension::load('ui.bootstrap4');


// 1. Предлагаем авторизацию/регистрацию
// если пользователь не авторизован, выводим форму авторизации/регистрации
if (!$USER->IsAuthorized()) { ?>

    <div class="row">
        <div id="authorization" class="m-auto col-6">
            <b>Авторизация</b>
            <?  // авторизация
            $APPLICATION->IncludeComponent(
                "bitrix:system.auth.form",
                "",
                [
                    "REGISTER_URL" => "register.php",
                    "FORGOT_PASSWORD_URL" => "",
                    "PROFILE_URL" => "profile.php",
                    "SHOW_ERRORS" => "Y"
                ]
            );
            ?>
        </div>

        <div id="register" class="m-auto col-6">
            <?  // регистрация
            $APPLICATION->IncludeComponent(
                "bitrix:main.register",
                "",
                Array(
                    "REQUIRED_FIELDS" => array("NAME", "LAST_NAME"),
                    "SHOW_FIELDS" => array("EMAIL", "NAME", "LAST_NAME", "PERSONAL_PHONE")
                )
            );
            ?>
        </div>
    </div>

<?
} else {
    // если пользователь авторизован, то
    // 2. Выводим форму заявки

    // Подключаем iblock
    \Bitrix\Main\Loader::includeModule('iblock');

    // "Одежда" в ИМ, демо-данные, ред. "Бизнес", iblock_id = 2 (подключите свой iblock, если не совпадает)
    $arFilter = array('IBLOCK_ID' => 2);
    $arSelect = array('IBLOCK_ID', 'ID', 'NAME');
    $rsSect = CIBlockSection::GetList(
        Array("SORT"=>"ASC"),
        $arFilter,
        false,
        $arSelect
    );
    while ($arSect = $rsSect->GetNext()) {
        // наполняем массив разделов для селекта с разделами ИБ ниже
        $arSections[] = $arSect;
    }
?>

        <?php// <form action="/local/scripts/send.php" method="post">?>
        <form action="">
            <h2>Новая заявка</h2>

            <div class="input-group mb-4" style="display: grid">
                <label for="order_name" class="form-label">Заголовок заявки</label>
                <input id="order_name" type="text" class="form-control w-100" placeholder="12345678" aria-label="order_name" required>
            </div>

            <div class="col-md-3 mb-4">
                <label for="category" class="form-label">Категория</label>
                <select id="category" class="form-select" required>
                    <?foreach ($arSections as $section)
                    {?>
                        <option class="id_<?=$section['ID']?>"><?=$section['NAME']?></option>
                    <?}?>
                </select>
            </div>

            <div class="col-md-3 mb-4">
                <label for="order_type" class="form-label">Вид заявки</label>
                <select id="order_type" class="form-select" required>
                    <option class="id_1">Запрос цены и сроков поставки</option>
                    <option class="id_2">Сводка по остаткам на складах</option>
                    <option class="id_3">Спецзаказ</option>
                </select>
            </div>


            <div class="col-md-3 mb-4">
                <label for="storage" class="form-label">Склад поставки</label>
                <select id="storage" class="form-select">
                    <option class="id_1">Москва</option>
                    <option class="id_2">Казань</option>
                    <option class="id_3">Екатеринбург</option>
                </select>
            </div>


            <h3>Состав заявки</h3>

            <? // Здесь явно не хватает простого поиска по элементам выбранного раздела, но задача есть задача ?>

            <div class="item_list">

                <div class="col-12 d-flex item_id">

                    <div class="input-group mr-1 mb-4" style="display: grid">
                        <label for="item_name" class="form-label">Бренд</label>
                        <input id="item_name" type="text" class="form-control w-100" placeholder="" aria-label="order_name">
                    </div>

                    <div class="input-group mr-1 mb-4" style="display: grid">
                        <label for="item_name" class="form-label">Наименование</label>
                        <input id="item_name" type="text" class="form-control w-100" placeholder="" aria-label="order_name">
                    </div>

                    <div class="input-group mr-1 mb-4" style="display: grid">
                        <label for="item_name" class="form-label">Количество</label>
                        <input id="item_name" type="text" class="form-control w-100" placeholder="" aria-label="order_name">
                    </div>

                    <div class="input-group mr-1 mb-4" style="display: grid">
                        <label for="item_name" class="form-label">Фасовка</label>
                        <input id="item_name" type="text" class="form-control w-100" placeholder="" aria-label="order_name">
                    </div>

                    <div class="input-group mr-1 mb-4" style="display: grid">
                        <label for="item_name" class="form-label">Клиент</label>
                        <input id="item_name" type="text" class="form-control w-100" placeholder="" aria-label="order_name">
                    </div>

                    <div class="col d-flex">
                        <button class="btn btn-sm btn-outline-primary btn_plus mr-1">+</button>
                        <button class="btn btn-sm btn-outline-dark btn_minus" onclick="del_item();">-</button>
                    </div>

                </div>

            </div>

            <script>

                // копируемый DOM элемент
                const item = document.querySelector(".item_id");
                const parent = document.querySelector(".item_list");

                // добавляем новый
                document.querySelector(".btn_plus").addEventListener(
                    "click",
                    function()
                    {
                        // клонируем DOM элемент
                        let clone = item.cloneNode(true);
                        parent.appendChild(clone);
                    }
                );

                // удаляем текущий
                document.querySelector(".btn_minus").addEventListener(
                    "click",
                    function()
                    {
                        this.parentElement.remove();
                    }
                );

            </script>

            <div class="input-group mb-4">
                <input type="file">
            </div>

            <div class="input-group mb-4">
                <label for="order_comment" class="form-label">Комментарий</label>
                <textarea name="order_comment" id="order_comment" cols="30" rows="10"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Отправить</button>
        </form>

        <?php
}





require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
