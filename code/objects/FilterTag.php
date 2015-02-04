<?php

/**
 * A tag for keyword descriptions of a page
 *
 * @package silverstripe
 * @subpackage filterablearchive
 *
 * @author Michael Strong, adapted by Michael van Schaik
**/
class FilterTag extends DataObject {
	
	private static $db = array(
		"Title" => "Varchar(255)",
		'URLSegment' => 'Varchar(255)',
	);
	
	private static $has_one = array(
		"HolderPage" => "SiteTree",
	);

	private static $belongs_many_many = array(
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
		if($this->Title){
			$this->URLSegment = SiteTree::GenerateURLSegment($this->Name);
		}
	}


	/**
	 * Returns a relative URL for the tag link
	 *
	 * @return string URL
	**/
	public function getLink() {
		return Controller::join_links($this->HolderPage()->Link(), "tag", $this->URLSegment);
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