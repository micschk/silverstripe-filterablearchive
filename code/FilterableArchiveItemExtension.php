<?php

class FilterableArchiveItemExtension extends SiteTreeExtension {

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
		$date = $this->dbObject(self::$date_field);
		return Controller::join_links($this->owner->Parent()->Link("archive"), $date->format("Y"));
	}
	
}