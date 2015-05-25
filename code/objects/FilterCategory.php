<?php

/**
 * A tag for keyword descriptions of a page
 *
 * @package silverstripe
 * @subpackage filterablearchive
 *
 * @author Michael Strong, adapted by Michael van Schaik
**/
class FilterCategory extends DataObject {
	
	private static $db = array(
		"Title" => "Varchar(255)",
		'URLSegment' => 'Varchar(255)',
	);
	
	private static $has_one = array(
		"HolderPage" => "SiteTree",
	);

	private static $many_many = array(
		"Pages" => "SiteTree",
	);

	public function getCMSFields() {
		$fields = new FieldList(
			TextField::create("Title", _t("FilterableArchive.CategoryTitle", "Category"))
		);
		$this->extend("updateCMSFields", $fields);
		return $fields;
	}
	
	public function onBeforeWrite(){
		parent::onBeforeWrite();
		// set URLSegment
		if($this->Title){
			$filter = URLSegmentFilter::create();
			$this->URLSegment = $filter->filter($this->Title);
			//$this->URLSegment = SiteTree::GenerateURLSegment($this->Title);
		}
		// Some cleaning up
//		$this->Title = trim( $this->Title ); // strip spaces
//		// if multiple with same title, combine in this one & remove duplicates;
//		$sameTitleTag = FilterCategory::get()->filter('Title', $this->Title)->exclude('ID',$this->ID);
//		if( $sameTitleTag->count() && $this->ID ){ //only if editing existing (ID is set)
//			foreach( $sameTitleTag as $duplicate ){
//				debug::dump($duplicate->Pages());
////				foreach( $duplicate->Pages() as $page ){
////					//add each page to this cat/tag
////					//$this->Pages()->add( $page->ID );
////				}
//			}
//		}
	}
	
//	public function onAfterWrite() {
//		parent::onAfterWrite();
//		if( ! $this->Title ){ $this->delete(); }
//		// remove duplicates (relations already copied to this one onbeforewrite)
//		$sameTitleTag = FilterCategory::get()->filter('Title', $this->Title)->exclude('ID',$this->ID);
//		if( $sameTitleTag->count() && $this->ID ){
////			$sameTitleTag->removeAll();
//		}
//	}


	/**
	 * Returns a relative URL for the tag link
	 *
	 * @return string URL
	**/
	public function getLink() {
		return Controller::join_links($this->HolderPage()->Link(), "cat", $this->URLSegment);
	}



	/**
	 * Can be overwritten using a DataExtension
	 *
	 * @param $member Member
	 *
	 * @return boolean
	 */
	public function canView($member = null) {
		$extended = $this->extendedCan(__FUNCTION__, $member);
		if($extended !== null) {
			return $extended;
		}
		return true;
	}



	/**
	 * Can be overwritten using a DataExtension
	 *
	 * @param $member Member
	 *
	 * @return boolean
	 */
	public function canCreate($member = null) {
		$extended = $this->extendedCan(__FUNCTION__, $member);
		if($extended !== null) {
			return $extended;
		}
		return true;
	}



	/**
	 * Can be overwritten using a DataExtension
	 *
	 * @param $member Member
	 *
	 * @return boolean
	 */
	public function canDelete($member = null) {
		$extended = $this->extendedCan(__FUNCTION__, $member);
		if($extended !== null) {
			return $extended;
		}
		return true;
	}



	/**
	 * Can be overwritten using a DataExtension
	 *
	 * @param $member Member
	 *
	 * @return boolean
	 */
	public function canEdit($member = null) {
		$extended = $this->extendedCan(__FUNCTION__, $member);
		if($extended !== null) {
			return $extended;
		}
		return true;
	}

}