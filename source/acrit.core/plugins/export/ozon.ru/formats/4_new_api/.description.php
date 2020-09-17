<p>Данный формат выгрузки предназначен для экспорта товаров в маркетплейс ОЗОН с помощью API.</p>

<div class="acrit-exp-note-compact" style="margin-bottom:15px;">
	<div class="adm-info-message-wrap">
		<div class="adm-info-message">
			<b>Внимание! Важная информация без которой Вы не сможете корректно настроить выгрузку!</b><br/>
			Выбор категорий Ozon - обязателен (два режима - стандартный и нестандартный)!<br/><br/>
			И <b style="color:maroon">для каждой категории Ozon Вам потребуется заполнить как минимум обязательные атрибуты</b> (они появляются в общем списке полей товаров, внизу, с разделением по категориям). Без этого корректная выгрузка <b style="color:maroon">невозможна</b>!<br/><br/>
			Больше информации - в <a href="#" data-role="acrit_exp_ozon_decscription_teacher" class="acrit-inline-link">кратком уроке по настройке профиля Ozon</a>.
			<script>
			$('a[data-role="acrit_exp_ozon_decscription_teacher"]').bind('click', function(e){
				e.preventDefault();
				if(typeof window.acritTeachers == 'object'){
					for(let i in window.acritTeachers){
						if(window.acritTeachers[i].code == 'EXPORT_OZON_NEW_API'){
							$("#adm-workarea").acritTeacher(window.acritTeachers[i]);
						}
					}
				}
			});
			</script>
		</div>
	</div>
</div>

<div data-role="ozon_description_hotwo">
	<h2>Как начать работу в Ozon?</h2>
	<ul>
		<li>Шаг 1. <a href="https://seller.ozon.ru/signup" target="_blank">Зарегистрируйтесь</a> и активируйте аккаунт.</li>
		<li>Шаг 2. Прочитайте и примите <a href="https://docs.ozon.ru/partners/dogovor-dlya-prodavtsov-na-platforme-ozon" target="_blank">оферту</a> (откроется при первом входе в личный кабинет).</li>
		<li>Шаг 3. Подключите электронный <a href="https://docs.ozon.ru/partners/nachalo-raboty/shag-3-podklyuchite-elektronnyj-dokumentooborot" target="_blank">документооборот</a>.</li>
		<li>Шаг 4. Загрузите <a href="https://seller.ozon.ru/products?filter=all" target="_blank">товары</a> - на этом шаге Вам поможет данный модуль.</li>
		<li>Шаг 5. Дождитесь результатов модерации (обычно не более 3х дней).</li>
		<li>Шаг 6. Выберите <a href="https://docs.ozon.ru/partners/nachalo-raboty/shag-6-vyberite-shemu-raboty-i-nachnite-prodavat" target="_blank">схему работы</a> (FBO или FBS) и начните продавать.</li>
	</ul>
</div>

<p><br/></p>

<div data-role="ozon_description_recommendations">
	<h2>Наши рекомендации для эффективной выгрузки</h2>
	<ol>
		<li style="margin-bottom:4px;"><b>Один профиль - для одной категории Озон</b><br/>
		Для каждой категории Озон свой набор характеристик для загрузки, поэтому все дополнительные поля (атрибуты) в настройках прикреплены к конкретным разделам.<br/>
		В некоторых случаях может быть удобно настроить несколько категорий, но имейте в виду что в некоторых категориях большой перечень полей, и чем больше категорий тем больше полей.</li>
		<li style="margin-bottom:4px;"><b>Выбор и сопоставление категорий</b><br/>
		После выбора категорий в списке обязательно настройте соответствия категорий сайта категориям на озоне (кнопка «Настроить названия»).<br/>
		После этого сохраните изменения в профиле и затем запустите загрузку атрибутов категорий (процесс может занять длительное время).</li>
		<li style="margin-bottom:4px;"><b>Обязательные поля и справочники</b><br/>
		Обращайте внимание на обязаные поля (указаны жирным шрифтом), и поля-справочники, в которых возможны только определенные значения (желтый восклицательный знак).</li>
	</ol>
</div>

<p><br/></p>

<div data-role="ozon_description_nuances">
	<h2>Важные нюансы</h2>
	<ul>
		<li>Справочники для различных категорий используются общие, кроме справочников «Тип» и «Коммерческий тип».</li>
		<li>После изменения выбранной категории не забывайте настроить сопоставление категорий и после этого применить изменения в профиле. После этого необходимо загрузить атрибуты и значения справочников.</li>
		<li>Для ускорения работы, загрузка значений справочников осуществляется с шагом 5000, несмотря на то, что рекомендация техподдержки Озон - 1000. В случае необходимости изменния значения используется конфигурационный параметр <b><code>ozon_new_api_step_size</code></b> нашего модуля.</li>
		<li>Для большинства атрибутов Озон требует точного соответствия значений. Например, если Вы выгружаете футболку, в которой пол указан как «М», «Для мужчин», «Мужские» - это неправильно. Должно выгружаться строго «Мужской».</li>
		<li>Картинки для товаров в личном кабинете сразу после загрузки не показываются - требуется достаточно много времени, чтобы они загрузились (несколько часов).</li>
		<li>Выгрузить более чем один товар с одинаковым offer_id невозможно. В таком случае в Озоне обновится существующий товар (это также относится и к архиву).</li>
	</ul>
</div>

<p><br/></p>

<h2>Полезные ссылки:</h2>

<ul>
	<li>
		<a href="https://docs.ozon.ru/partners/nachalo-raboty" target="_blank">
			Начало работы
		</a>
	</li>
	<li>
		<a href="https://docs.ozon.ru/partners/" target="_blank">
			Инструкция по работе с маркетплейсом ОЗОН
		</a>
	</li>
	<li>
		<a href="https://docs.ozon.ru/partners/trebovaniya-k-tovaram/obyazatel-nye-harakteristiki" target="_blank">
			Обязательные характеристики
		</a>
	</li>
	<li>
		<a href="https://seller.ozon.ru/settings/api-keys" target="_blank">
			Получение API ключа
		</a>
	</li>
	<li>
		<a href="https://cb-api.ozonru.me/apiref/ru/#t-title_sandbox" target="_blank">
			Тестовая среда для проверки
		</a>
	</li>
	<li>
		<a href="https://partner.market.yandex.ru/supplier/" target="_blank">
			Документация
		</a>
	</li>
	<li>
		<a href="https://docs.ozon.ru/partners/zagruzka-tovarov/moderatsiya/kritichnye-oshibki" target="_blank">
			Частые ошибки
		</a>
	</li>
	<li>
		<a href="https://api-seller.ozon.ru/docs/#/CategoryAPI/CategoryAPI_GetCategoryAttributes" target="_blank">
			Swaggger (отладка)
		</a>
	</li>
	<li>
		<a href="https://docs.ozon.ru/su" target="_blank">
			Обучение
		</a>
	</li>
</ul>
