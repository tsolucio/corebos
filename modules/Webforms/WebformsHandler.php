<?php
/** YOUR LICENSE TEXT HERE **/
class WebformsHandler extends VTEventHandler {

	function handleEvent($eventName, $data) {

		if($eventName == 'vtiger.entity.beforesave') {
			// Entity is about to be saved, take required action
		}

		if($eventName == 'vtiger.entity.aftersave') {
			// Entity has been saved, take next action
		}
	}
}

?>
