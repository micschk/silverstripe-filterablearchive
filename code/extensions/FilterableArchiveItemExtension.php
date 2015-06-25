<?php

class FilterableArchiveItemExtension extends SiteTreeExtension {
	
	private static $belongs_many_many = array(
		"Tags" => "FilterTag",
		"Categories" => "FilterCategory",
	);
	
	public function updateCMSFields(\FieldList $fields) {
		parent::updateCMSFields($fields);
		
		// Add Categories & Tags fields
		if($this->owner->Parent()->getFilterableArchiveConfigValue('categories_active')){
//			$categoriesField = ListboxField::create(
//				"Categories", 
//				_t("FilterableArchive.Categories", "Categories"), 
//				$this->owner->Parent()->Categories()->map()->toArray()
//			)->setMultiple(true);
			
			// Use tagfield instead (allows inline creation)
			//$availableCats = FilterCategory::get()->filter('HolderPageID',$this->owner->ParentID);
			$availableCats = $this->owner->Parent()->Categories();
			$categoriesField = new TagField(
				'Categories', 
				_t("FilterableArchive.Categories", "Categories"), 
				$availableCats,
				$this->owner->Categories()
			);
			//$categoriesField->setShouldLazyLoad(true); // tags should be lazy loaded (nope, gets all instead of just the parent's cats/tags)
			$categoriesField->setCanCreate(true); // new tag DataObjects can be created (@TODO check privileges)
			$fields->insertbefore($categoriesField, "Content");
		}
		
		if($this->owner->Parent()->getFilterableArchiveConfigValue('tags_active')){
//			$tagsField = ListboxField::create(
//				"Tags", 
//				_t("BlogPost.Tags", "Tags"), 
//				$this->owner->Parent()->Tags()->map()->toArray()
//			)->setMultiple(true);
			
			// Use tagfield instead (allows inline creation)
			//$availableTags = FilterTag::get()->filter('HolderPageID',$this->owner->ParentID);
			$availableTags = $this->owner->Parent()->Tags();
			$tagsField = new TagField(
				'Tags', 
				_t("FilterableArchive.Tags", "Tags"), 
				$availableTags,
				$this->owner->Tags()
			);
			//$tagsField->setShouldLazyLoad(true); // tags should be lazy loaded (nope, gets all instead of just the parent's cats/tags)
			$tagsField->setCanCreate(true); // new tag DataObjects can be created (@TODO check privileges)
			$fields->insertAfter($tagsField, "Categories");
		}
		
	}
	
	// Set HolderPage relationship on all categories and tags assigned to this item
	public function onAfterWrite() {
		parent::onAfterWrite();
		
		foreach($this->owner->Categories() as $category) {
			$category->HolderPageID = $this->owner->ParentID;
			$category->write();
		}
		
		foreach($this->owner->Tags() as $tag) {
			$tag->HolderPageID = $this->owner->ParentID;
			$tag->write();
		}
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
		$datefield = $this->owner->Parent()->getFilterableArchiveConfigValue('managed_object_date_field');
		$date = $this->owner->dbObject( $datefield );
		if($archiveunit == "month") {
			return Controller::join_links($this->owner->Parent()->Link("date"), 
				$date->format("Y"), $date->format("m"))."/";
		}
		if($archiveunit == "day") {
			return Controller::join_links(
				$this->owner->Parent()->Link("date"), 
				$date->format("Y"), 
				$date->format("m"), 
				$date->format("d")
			)."/";
		}
		return Controller::join_links($this->owner->Parent()->Link("date"), $date->format("Y"))."/";
	}

	/**
	 * Returns a yearly archive link for the current item.
	 *
	 * @return string URL
	**/
	public function getYearArchiveLink() {
		$datefield = $this->owner->Parent()->getFilterableArchiveConfigValue('managed_object_date_field');
		$date = $this->dbObject($datefield);
		return Controller::join_links($this->owner->Parent()->Link("date"), $date->format("Y"));
	}
	
}