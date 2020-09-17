<?

/**
 * Acrit Core: youla.ru base plugin
 * @documentation https://docs.google.com/document/d/1flyFODQ1UGy6pKh5jwi0-yuNz2SzqkeZsNwEvD1zbmU/edit#
 */

namespace Acrit\Core\Export\Plugins;

use \Acrit\Core\Export\UniversalPlugin,
		\Acrit\Core\Helper;

abstract class YoulaRu extends UniversalPlugin
{

	protected function getCategoriesCacheFile()
	{
		$strCacheDir = __DIR__ . '/cache';
		if (!is_dir($strCacheDir))
		{
			mkdir($strCacheDir, BX_DIR_PERMISSIONS, true);
		}
		return $strCacheDir . '/categories.txt';
	}

	public function getCategoriesTree($intProfileID)
	{
		$arCategories = [];
		$strFileName = $this->getCategoriesCacheFile();
		$arCategories = unserialize(file_get_contents($strFileName));
		if (Helper::isUtf())
		{
			return Helper::convertEncoding($arCategories, 'CP1251', 'UTF-8');
		} else
		{
			return $arCategories;
		}
	}

	function getCategoryRedefinitionName($categoryIdBitrix)
	{
		$arCategoryRedefinitionsAll = Helper::call($this->strModuleId, 'CategoryRedefinition', 'getForProfile', [$this->arProfile['ID']]);
		return $arCategoryRedefinitionsAll[$categoryIdBitrix];
	}

	public function getCategoriesIdByName($name, $intProfileID)
	{
		$categoriesTree = $this->getCategoriesTree($intProfileID);

		foreach ($categoriesTree as $item)
		{
			if ($item['title'] == $name)
			{
				return $item['category_id'];
			}
			if ($item['children'])
			{
				foreach ($item['children'] as $itemLevel2)
				{
					if ($itemLevel2['title'] == $name)
					{
						return $itemLevel2['category_id'];
					}
				}
			}
		}
	}

	public function getCategoriesParentIdById($id, $intProfileID)
	{
		$categoriesTree = $this->getCategoriesTree($intProfileID);

		foreach ($categoriesTree as $item)
		{
			if ($item['children'])
			{
				foreach ($item['children'] as $itemLevel2)
				{
					if ($itemLevel2['category_id'] == $id)
					{
						return $itemLevel2['parent_category_id'];
					}
				}
			}
		}
	}

	public function getCategoriesList($intProfileID)
	{
		$arCategories = [];
		$categoriesTree = $this->getCategoriesTree($intProfileID);

		foreach ($categoriesTree as $item)
		{
			$arCategories[] = $item['title'];
			if ($item['children'])
			{
				foreach ($item['children'] as $itemLevel2)
				{
					$arCategories[] = $itemLevel2['title'];
				}
			}
		}

		return $arCategories;
	}

}

?>