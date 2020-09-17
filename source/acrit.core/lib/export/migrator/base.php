<?
/**
 * Base class for migrator
 */

namespace Acrit\Core\Export\Migrator;

/**
 *	
 */
abstract class Base {
	
	protected $arOldProfile = array();
	
	protected $arFieldsMap = array();
	
	const PLUGIN = NULL;
	const FORMAT = NULL;
	
	const DEFAULT_CURRENCY = NULL;
	
	public function __construct(){
		$this->arFieldsMap = $this->_getFieldsMap();
	}
	
	final public function setOldProfile(&$arOldProfile){
		$this->arOldProfile = &$arOldProfile;
	}
	
	/**
	 *	Get generated fields map
	 */
	final public function getFieldsMap(){
		return $this->arFieldsMap;
	}
	
	/**
	 *	Get fields map (old_field => new_field)
	 */
	abstract public function _getFieldsMap();	/**
	
	 *	
	 */
	public function getMultipleFields(){
		return array();
	}
	
	/**
	 *	Compile additional ['PARAMS']
	 */
	abstract public function compileParams(&$arNewProfile);
	
}

?>