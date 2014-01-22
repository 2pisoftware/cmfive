<?php

/**
 * This class serves as an abstract (sudo interface) for any ChannelProcessor.
 * When creating a Processor, extend this class for access to the DbService
 * and to ensure concurrency between processors
 */
abstract class ProcessorType extends DbService {

	/** 
	 * This settings array will be Processor sepcific and saved to the DB
	 * as a JSON string ensuring transportability and is what makes these
	 * Processor objects so robust. The returned array must match the Html::forms()
	 * nested array structure so that one can edit these settings
	 *
	 * @param String $current_settings
	 * @return Array
	 */
	public function getSettingsForm($current_settings = null) {
		return array();
	}

	/**
	 * A processor job should never return anything because it has
	 * nothing to return to.
	 *
	 * @return none
	 */
	public function doJob() {
		
	}

}