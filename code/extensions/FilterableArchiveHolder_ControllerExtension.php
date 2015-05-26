<?php

class FilterableArchiveHolder_ControllerExtension extends Extension {

	private static $allowed_actions = array(
		'archive', # renamed to 'date'
		'date',
		'tag',
		'cat'
	);

	private static $url_handlers = array(
		'archive/$Year!/$Month/$Day' => 'date', # renamed to 'date'
		'date/$Year!/$Month/$Day' => 'date',
		'tag/$Tag!' => 'tag',
		'cat/$Category!' => 'cat',
	);
	
	/**
	 * Renders an archive for a specificed date. This can be by year or year/month
	 *
	 * @return SS_HTTPResponse
	**/
	public function date() {
		$year = $this->owner->getArchiveYear();
		$month = $this->owner->getArchiveMonth();
		$day = $this->owner->getArchiveDay();

		// If an invalid month has been passed, we can return a 404.
		if($this->owner->request->param("Month") && !$month) {
			return $this->owner->httpError(404, "Not Found");
		}

		// Check for valid day
		if($month && $this->owner->request->param("Day") && !$day) {
			return $this->owner->httpError(404, "Not Found");
		}

		if($year) {
			$this->owner->Items = $this->owner->getFilteredArchiveItems($year, $month, $day);
			return $this->owner->render();
		} else {
			return $this->owner->redirect($this->owner->AbsoluteLink(), 303); //301: movedperm, 302: movedtemp, 303: see other
		}
	}
	
	/**
	 * Tag Getter for use in templates.
	 *
	 * @return BlogTag|null
	**/
	public function getCurrentTag() {
		$tag = $this->owner->request->param("Tag");
		if($tag) {
			return $this->owner->dataRecord->Tags()
				->filter("URLSegment", $tag)
				->first();
		}
		return null;
	}
	/**
	 * Category Getter for use in templates.
	 *
	 * @return BlogCategory|null
	**/
	public function getCurrentCategory() {
		$category = $this->owner->request->param("Category");
		if($category) {
			return $this->owner->dataRecord->Categories()
				->filter("URLSegment", $category)
				->first();
		}
		return null;
	}
	
	/**
	 * Renders the blog posts for a given tag.
	 *
	 * @return SS_HTTPResponse
	**/
	public function tag() {
		$tag = $this->owner->getCurrentTag();
		if($tag) {
			$this->owner->Items = $tag->Pages();
			return $this->owner->render();
		}
		return $this->owner->httpError(404, "Not Found");
	}
	/**
	 * Renders the blog posts for a given category
	 *
	 * @return SS_HTTPResponse
	**/
	public function cat() {
		$category = $this->owner->getCurrentCategory();
		if($category) {
			$this->owner->Items = $category->Pages();
			return $this->owner->render();
		}
		return $this->owner->httpError(404, "Not Found");
	}
	
	/**
	 * Returns a list of paginated blog posts based on the blogPost dataList
	 *
	 * @return PaginatedList
	**/
	public function PaginatedItems() {
		
		$items = new PaginatedList($this->owner->Items, $this->owner->request);
		// If pagination is set to '0' then no pagination will be shown.
		if($this->owner->ItemsPerPage > 0) $items->setPageLength($this->owner->ItemsPerPage);
		else $items->setPageLength($this->owner->getItems()->count());
		return $items;

	}
	
	/**
	 * Fetches the archive year from the url
	 *
	 * @return int|null
	**/
	public function getArchiveYear() {
		$year = $this->owner->request->param("Year");
		if(preg_match("/^[0-9]{4}$/", $year)) {
			return (int) $year;
		}
		return null;
	}

	/**
	 * Fetches the archive money from the url.
	 *
	 * @return int|null
	**/
	public function getArchiveMonth() {
		$month = $this->owner->request->param("Month");
		if(preg_match("/^[0-9]{1,2}$/", $month)) {
			if($month > 0 && $month < 13) {
				// Check that we have a valid date.
				if(checkdate($month, 01, $this->owner->getArchiveYear())) {
					return (int) $month;
				}
			}
		}
		return null;
	}

	/**
	 * Fetches the archive day from the url
	 *
	 * @return int|null
	**/
	public function getArchiveDay() {
		$day = $this->owner->request->param("Day");
		if(preg_match("/^[0-9]{1,2}$/", $day)) {

			// Check that we have a valid date
			if(checkdate($this->owner->getArchiveMonth(), $day, $this->owner->getArchiveYear())) {
				return (int) $day;
			}
		}
		return null;
	}

	/**
	 * Returns the current archive date.
	 *
	 * @return Date
	**/
	public function getArchiveDate() {
		$year = $this->owner->getArchiveYear();
		$month = $this->owner->getArchiveMonth();
		$day = $this->owner->getArchiveDay();

		if($year) {
			if($month) {
				$date = $year . '-' . $month . '-01';
				if($day) {
					$date = $year . '-' . $month . '-' . $day;
				}
			} else {
				$date = $year . '-01-01';
			}
			return DBField::create_field("Date", $date);
		}
	}
	
}