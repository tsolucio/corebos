--
-- Increment operation sequence
--
update vtiger_ws_operation_seq set id=id+1;

--
-- Insert webservice entry in operation table
--
INSERT INTO `vtiger_ws_operation` (`operationid`,`name` ,`handler_path` ,`handler_method` ,`type` ,`prelogin`)
VALUES (
(select max(id) from vtiger_ws_operation_seq),'getpdfdata', 'include/Webservices/GetPDFData.php', 'cbws_getpdfdata', 'POST', '0');

--
-- Insert one entry for each service's parameter
--
INSERT INTO `vtiger_ws_operation_parameters` (`operationid` ,`name` ,`type` ,`sequence`)
VALUES (
(select max(id) from vtiger_ws_operation_seq), 'id', 'string', '0');
