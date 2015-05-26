<?php

class FilterableArchiveHolderExtension extends SiteTreeExtension {
	
	private static $managed_object_class = "Page";
	private static $managed_object_date_field = "PublishDate";
	
	private static $pagination_control_tab = "Root.Main";
	private static $pagination_insert_before = null;
	private static $pagination_active = true;
	
	private static $tags_active = true;
	private static $categories_active = true;
	private static $datearchive_active = true;
	
	private static $tags_active = true;
	private static $categories_active = true;
	private static $datearchive_active = true;
	
	static $db = array(
		'ItemsPerPage' => 'Int',
		'ArchiveUnit' => 'Enum("year, month, day")',
    );
	
	private static $has_many = array(
		"Tags" => "FilterTag",
		"Categories" => "FilterCategory",
	);
	
	// get configurations from extended class, self or private static
	//test
	public function getConfigValue($name){
		$conf = Config::inst()->get($this->owner->className, $name);
		if(!$conf===null) $conf = Config::inst()->get("FilterableArchiveHolderExtension", $name);
		if(!$conf===null) $conf = self::$$name;
		return $conf;
	}
	
	// add fields to CMS
	public function updateCMSFields(FieldList $fields) {
		
		// check if the insertbefore field is present (may be added later, in which case the above 
		// fields never get added
		$insertOnTab = $this->owner->getConfigValue('pagination_control_tab');
		$insertBefore = $this->owner->getConfigValue('pagination_insert_before');
		if(!$fields->fieldByName("$insertOnTab.$insertBefore")){
			$insertBefore = null;
		}
		
		if($this->owner->getConfigValue('datearchive_active')){
			$fields->addFieldToTab($this->owner->getConfigValue('pagination_control_tab'), 
				DropdownField::create('ArchiveUnit', 
					_t('filterablearchive.ARCHIVEUNIT', 'Archive unit'),
					array(
						'year' => _t('filterablearchive.YEAR', 'Year'),
						'month' => _t('filterablearchive.MONTH', 'Month'),
						'day' => _t('filterablearchive.DAY', 'Day'),
					)), $insertBefore);
		}
		
		$pagerField = NumericField::create("ItemsPerPage", 
				_t("filterablearchive.ItemsPerPage", "Pagination: items per page"))
				->setRightTitle(_t("filterablearchive.LeaveEmptyForNone", 
						"Leave empty or '0' for no pagination"));
		
		$fields->addFieldToTab(
				$insertOnTab, 
				$pagerField, 
				$insertBefore
				);
		
		//
		// Create categories and tag config
		//
//		$config = GridFieldConfig_RecordEditor::create();
//		$config->removeComponentsByType("GridFieldAddNewButton");
//		$config->addComponent(new GridFieldAddByDBField("buttons-before-left"));
		
		// Lets just use what others have made already...
		$config = GridFieldConfig::create()
        ->addComponent(new GridFieldButtonRow('before'))
        ->addComponent(new GridFieldToolbarHeader())
        ->addComponent(new GridFieldTitleHeader())
        ->addComponent(new GridFieldEditableColumns())
        ->addComponent(new GridFieldDeleteAction())
        ->addComponent(new GridFieldAddNewInlineButton('toolbar-header-right'));
		
		if($this->owner->getConfigValue('categories_active')){
			$fields->addFieldToTab($insertOnTab, 
					$categories = GridField::create(
						"Categories",
						_t("FilterableArchive.Categories", "Categories"),
						$this->owner->Categories(),
						$config
					), $insertBefore);
		}
		if($this->owner->getConfigValue('tags_active')){
			$fields->addFieldToTab($insertOnTab, 
					$tags = GridField::create(
						"Tags",
						_t("FilterableArchive.Tags", "Tags"),
						$this->owner->Tags(),
						$config
					), $insertBefore);
		}
		
	}
	
	/**
	 * Return unfiltered items
	 *
	 * @return DataList of managed_object_class
	**/
	public function getItems() {
		
		$class = $this->owner->getConfigValue('managed_object_class');
		$dateField = $this->owner->getConfigValue('managed_object_date_field');
		$items = $class::get()->filter('ParentID',$this->owner->ID)->sort("$dateField DESC");
		
		// workaround for Embargo/Expiry (augmentSQL for embargo/expiry is not working yet);
		if( $class::has_extension("EmbargoExpirySchedulerExtension") ){
			$items = $items->where( EmbargoExpirySchedulerExtension::extraWhereQuery($class) );
		}
		
		//Allow decorators to manipulate list, eg to use this to manage non SiteTree Items
		$this->owner->extend('updateGetItems', $items);
		
		return $items;
		
	}
	
	/**
	 * Returns items for a given date period.
	 *
	 * @param $year int
	 * @param $month int
	 * @param $dat int
	 *
	 * @return DataList
	**/
	public function getFilteredArchiveItems($year, $month = null, $day = null) {
		
		$class = $this->owner->getConfigValue('managed_object_class');
		$dateField = $this->owner->getConfigValue('managed_object_date_field');
		
		if($month) {
			if($day) {
				return $this->owner->getItems()
						->where("DAY({$dateField}) = '" . Convert::raw2sql($day) . "' 
							AND MONTH({$dateField}) = '" . Convert::raw2sql($month) . "'
							AND YEAR({$dateField}) = '" . Convert::raw2sql($year) . "'");
			}
			return $this->owner->getItems()
					->where("MONTH({$dateField}) = '" . Convert::raw2sql($month) . "'
						AND YEAR({$dateField}) = '" . Convert::raw2sql($year) . "'");
		} else {
			return $this->owner->getItems()->where("YEAR({$dateField}) = '" . Convert::raw2sql($year) . "'");
		}
		
	}
	
	//
	// Dropdowns for available archiveitems
	//
	public function ArchiveUnitDropdown() {

		$months = array();
		$months['1'] = _t('filterablearchive.JANUARY', 'Januari');
		$months['2'] = _t('filterablearchive.FEBRUARY', 'Februari');
		$months['3'] = _t('filterablearchive.MARCH', 'Maart');
		$months['4'] = _t('filterablearchive.APRIL', 'April');
		$months['5'] = _t('filterablearchive.MAY', 'Mei');
		$months['6'] = _t('filterablearchive.JUNE', 'Juni');
		$months['7'] = _t('filterablearchive.JULY', 'Juli');
		$months['8'] = _t('filterablearchive.AUGUST', 'Augustus');
		$months['9'] = _t('filterablearchive.SEPTEMBER', 'September');
		$months['10'] = _t('filterablearchive.OCTOBER', 'Oktober');
		$months['11'] = _t('filterablearchive.NOVEMBER', 'November');
		$months['12'] = _t('filterablearchive.DECEMBER', 'December');
		
		// build array with available archive 'units'
		$items = $this->owner->getItems();
		$dateField = $this->owner->getConfigValue('managed_object_date_field');
		$itemArr = array();
		foreach ($items as $item) {
			if (!$item->$dateField) {
				continue;
			}
			$dateObj = DBField::create_field('Date', strtotime($item->$dateField));
			// add month if not yet in array;
			if ($this->owner->ArchiveUnit == 'day') {
				$arrkey = $dateObj->Format('Y/m/d/');
				$arrval = $dateObj->Format('d ').$months[$dateObj->Format('n')].$dateObj->Format(' Y');
			} elseif ($this->owner->ArchiveUnit == 'month') {
				$arrkey = $dateObj->Format('Y/m/');
				$arrval = $months[$dateObj->Format('n')].$dateObj->Format(' Y');
			} else {
				$arrkey = $dateObj->Format('Y/');
				$arrval = $dateObj->Format('Y');
			}
			if (!array_key_exists($arrkey, $itemArr)) {
				$itemArr[$arrkey] =  $arrval;
			}
		}
		
		$DrDown = new DropdownField( 'archiveunits', '', $itemArr );
		$DrDown->setEmptyString(_t('filterablearchive.FILTER', 'Filter items'));
		$DrDown->addExtraClass("dropdown form-control");
		
		// specific to the 'archive' action defined by FilterableArchiveHolder_ControllerExtension (if available)
		$ctrl = Controller::curr();
		$activeUnit = "";
		if( $ctrl::has_extension("FilterableArchiveHolder_ControllerExtension") ){
			if( $cYear = $ctrl->getArchiveYear() ) $activeUnit .= "$cYear/";
			if( $cMonth = $ctrl->getArchiveMonth() ) $activeUnit .= str_pad("$cMonth/", 3, "0", STR_PAD_LEFT);
			if( $cDay = $ctrl->getArchiveDay() ) $activeUnit .= str_pad("$cDay/", 3, "0", STR_PAD_LEFT);
		}
		$DrDown->setValue($activeUnit);
		
		// again, tie this to the 'archive' action;
		$DrDown->setAttribute('onchange', "location = '{$this->owner->AbsoluteLink()}date/'+this.value;");
		return $DrDown;
	}
	
}
	
