<?php

class FilterableArchiveItemExtension extends SiteTreeExtension {
	
	private static $belongs_many_many = array(
		"Tags" => "FilterTag",
		"Categories" => "FilterCategory",
	);
	
	public function updateCMSFields(\FieldList $fields) {
		parent::updateCMSFields($fields);
		
		// Add Categories & Tags fields
		$categoriesField = ListboxField::create(
			"Categories", 
			_t("FilterableArchive.Categories", "Categories"), 
			$this->owner->Parent()->Categories()->map()->toArray()
		)->setMultiple(true);
		
//		$newCategoriesField = MultiValueTextField::create('NewCategories',
//				_t("FilterableArchive.NewCategories", 'Add new categories'));
		
		//$dateField = $this->owner->Parent()->getConfigValue('managed_object_date_field');
		//Debug::dump($dateField);
		$fields->insertbefore($categoriesField, "Content");
//		$fields->insertAfter($newCategoriesField, "Categories");
		//$fields->push($categoriesField);
		
		$tagsField = ListboxField::create(
			"Tags", 
			_t("BlogPost.Tags", "Tags"), 
			$this->owner->Parent()->Tags()->map()->toArray()
		)->setMultiple(true);
		$fields->insertAfter($tagsField, "Categories");
		//$fields->push($tagsField);
		
	}
	
	public function onBeforeWrite() {
		parent::onBeforeWrite();
		
		// create & add new categories
//		$newCatArr = $this->owner->NewCategories;
//		Debug::dump($this->owner->getField('NewCategories'));
//		foreach($newCatArr as $newCat){
//			if($catObj = FilterCategory::get()->filter('Title',$newCat)->first()){
//				// add existing
//				$this->owner->Categories()->add($catObj);
//			} else {
//				// create & add
//				$catObj = new FilterCategory();
//				$catObj->Title = $newCat;
//				$catObj->write();
//				$this->owner->Categories()->add($catObj);
//			}
//		}
		
	}

	/**
	 * Returns a monthly archive link for the current item.
	 *
	 * @param $type string day|month|year
	 *
	 * @return string URL
	**/
	public function getArchiveLink($archiveunit = false) {
		if(!$archiveunit) $archiveunit = $this->owner->Parent()->ArchiveUnit;
		if(!$archiveunit) $archiveunit = 'month'; // default
		$datefield = $this->owner->Parent()->getConfigValue('managed_object_date_field');
		$date = $this->owner->dbObject( $datefield );
		if($archiveunit == "month") {
			return Controller::join_links($this->owner->Parent()->Link("archive"), 
				$date->format("Y"), $date->format("m"))."/";
		}
		if($archiveunit == "day") {
			return Controller::join_links(
				$this->owner->Parent()->Link("archive"), 
				$date->format("Y"), 
				$date->format("m"), 
				$date->format("d")
			)."/";
		}
		return Controller::join_links($this->owner->Parent()->Link("archive"), $date->format("Y"))."/";
	}

	/**
	 * Returns a yearly archive link for the current item.
	 *
	 * @return string URL
	**/
	public function getYearArchiveLink() {
		$datefield = $this->owner->Parent()->getConfigValue('managed_object_date_field');
		$date = $this->dbObject($datefield);
		return Controller::join_links($this->owner->Parent()->Link("archive"), $date->format("Y"));
	}
	
}