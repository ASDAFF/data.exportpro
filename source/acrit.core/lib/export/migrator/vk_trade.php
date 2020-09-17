<?
/**
 * Class for migrate profiles from old export module's core
 */

namespace Acrit\Core\Export\Migrator;

use \Acrit\Core\Helper;

/**
 *	
 */
class VkTrade extends Base {
	
	const PLUGIN = 'VK';
	const FORMAT = 'VK_GOODS';
	
	const DEFAULT_CURRENCY = 'RUB';
	
	const OLD_TYPE = 'vk_trade';
	
	/**
	 *
	 */
	public function _getFieldsMap(){
		$arResult = array(
			'ID' => 'ID',
			'AVAILABLE' => 'AVAILABLE',
			'BID' => 'BID',
			'CBID' => 'CBID',
			'URL' => 'URL',
			'PRICE' => 'PRICE',
			'OLDPRICE' => 'OLD_PRICE',
			'CURRENCYID' => 'CURRENCY_ID',
			'VAT' => 'VAT',
			'GROUPID' => 'GROUP_ID',
			'PICTURE' => 'GROUP_ID',
			'STORE' => 'STORE',
			'PICKUP' => 'PICKUP',
			'DELIVERY' => 'DELIVERY',
			'LOCAL_DELIVERY_COST' => 'DELIVERY_OPTIONS_COST',
			'LOCAL_DELIVERY_DAYS' => 'DELIVERY_OPTIONS_DAYS',
			'LOCAL_ORDER_BEFORE' => 'DELIVERY_OPTIONS_ORDER_BEFORE',
			'NAME' => 'NAME',
			'VENDOR' => 'VENDOR',
			'VENDORCODE' => 'VENDOR_CODE',
			'DESCRIPTION' => 'DESCRIPTION',
			'SALES_NOTES' => 'SALES_NOTES',
			'MANUFACTURER_WARRANTY' => 'MANUFACTURER_WARRANTY',
			'COUNTRY_OF_ORIGIN' => 'COUNTRY_OF_ORIGIN',
			'ADULT' => 'ADULT',
			'AGE' => 'AGE',
			'BARCODE' => 'BARCODE',
			'UTM_SOURCE' => 'UTM_SOURCE',
			'UTM_MEDIUM' => 'UTM_MEDIUM',
			'UTM_TERM' => 'UTM_TERM',
			'UTM_CONTENT' => 'UTM_CONTENT',
			'UTM_CAMPAIGN' => 'UTM_CAMPAIGN',
		);
		return $arResult;
	}
	
	/**
	 *
	 */
	public function compileParams(&$arNewProfile){
		$arParams = &$arNewProfile['PARAMS'];
		$this->arOldProfile['_TOOLS']['MARKET_CATEGORY']['CATEGORY_LIST'] = $this->arOldProfile['_TOOLS']['MARKET_CATEGORY']['VK']['CATEGORY_LIST'];
		if(is_array($this->arOldProfile['_TOOLS']['MARKET_CATEGORY']['CATEGORY_LIST'])){
			foreach($this->arOldProfile['_TOOLS']['MARKET_CATEGORY']['CATEGORY_LIST'] as $intKey => $intVkCategoryId){
				$this->arOldProfile['_TOOLS']['MARKET_CATEGORY']['CATEGORY_LIST'][$intKey] = $this->getVkCategoryName($intVkCategoryId);
			}
		}
		#
		$arParams['ACCESS_TOKEN'] = $this->arOldProfile['_TOOLS']['VK']['VK_ACCESS_TOKEN'];
		$arParams['GROUP_ID'] = $this->arOldProfile['_TOOLS']['VK']['VK_GROUP_PUBLISH'];
		$arParams['PROCESS_CREATE_ALBUMS'] = 'Y';
		$arParams['PROCESS_DELETE_OTHER'] = 'N';
		$arParams['PROCESS_DELETE_DUPLICATES'] = 'Y';
	}
	
	/**
	 *
	 */
	protected function getVkCategoryName($intVkCategoryID){
		$arCategories = array(
			1 => 'Женская одежда',
			2 => 'Мужская одежда',
			3 => 'Детская одежда',
			4 => 'Обувь и сумки',
			5 => 'Аксессуары и украшения',
			100 => 'Автокресла',
			101 => 'Детские коляски',
			102 => 'Детская комната',
			103 => 'Игрушки',
			104 => 'Малышам и родителям',
			105 => 'Обучение и творчество',
			106 => 'Школьникам',
			200 => 'Телефоны и аксессуары',
			201 => 'Фото- и видеокамеры',
			202 => 'Аудио- и видеотехника',
			203 => 'Портативная техника',
			204 => 'Игровые приставки и игры',
			205 => 'Техника для автомобилей',
			206 => 'Оптические приборы',
			207 => 'Радиотовары',
			300 => 'Компьютеры',
			301 => 'Ноутбуки, нетбуки',
			302 => 'Комплектующие и аксессуары',
			303 => 'Периферийные устройства',
			304 => 'Сетевое оборудование',
			305 => 'Оргтехника и расходники',
			306 => 'Фильмы, музыка, программы',
			400 => 'Автомобили',
			401 => 'Мотоциклы и мототехника',
			402 => 'Грузовики и спецтехника',
			403 => 'Водный транспорт',
			404 => 'Запчасти и аксессуары',
			500 => 'Квартиры',
			501 => 'Комнаты',
			502 => 'Дома, дачи, коттеджи',
			503 => 'Земельные участки',
			504 => 'Гаражи и машиноместа',
			505 => 'Коммерческая недвижимость',
			506 => 'Недвижимость за рубежом',
			600 => 'Бытовая техника',
			601 => 'Мебель и интерьер',
			602 => 'Кухонные принадлежности',
			603 => 'Текстиль',
			604 => 'Хозяйственные товары',
			605 => 'Ремонт и строительство',
			606 => 'Дача, сад и огород',
			700 => 'Декоративная косметика',
			701 => 'Парфюмерия',
			702 => 'Уход за лицом и телом',
			703 => 'Приборы и аксессуары',
			704 => 'Оптика',
			800 => 'Активный отдых',
			801 => 'Туризм',
			802 => 'Охота и рыбалка',
			803 => 'Тренажеры и фитнес',
			804 => 'Игры',
			900 => 'Билеты и путешествия',
			901 => 'Книги и журналы',
			902 => 'Коллекционирование',
			903 => 'Музыкальные инструменты',
			904 => 'Настольные игры',
			905 => 'Подарочные наборы и сертификаты',
			906 => 'Сувениры и цветы',
			907 => 'Рукоделие и творчество',
			1000 => 'Собаки',
			1001 => 'Кошки',
			1002 => 'Грызуны',
			1003 => 'Птицы',
			1004 => 'Рыбы',
			1005 => 'Другие животные',
			1006 => 'Корма и аксессуары',
			1100 => 'Бакалея',
			1101 => 'Биопродукты',
			1102 => 'Детское питание',
			1103 => 'Еда на заказ',
			1104 => 'Напитки',
			1200 => 'Фото- и видеосъёмка',
			1201 => 'Удалённая работа',
			1202 => 'Организация мероприятий',
			1203 => 'Красота и здоровье',
			1204 => 'Установка и ремонт техники',
			1205 => 'Уборка и помощь по хозяйству',
			1206 => 'Курьеры и грузоперевозки',
			1207 => 'Обучение и развитие',
			1208 => 'Финансовые услуги',
			1209 => 'Консультации специалистов',
		);
		$strResult = isset($arCategories[$intVkCategoryID]) ? $intVkCategoryID.': '.$arCategories[$intVkCategoryID] : '';
		if(!Helper::isUtf()){
			$strResult = Helper::convertEncoding($strResult, 'UTF-8', 'CP1251');
		}
		return $strResult;
	}
	
}

?>