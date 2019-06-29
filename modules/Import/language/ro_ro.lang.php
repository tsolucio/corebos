<?php
/*************************************************************************************************
 * Copyright 2018 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************/

$mod_strings = array(
	'Import' => 'Import',
	'LBL_IMPORT_STEP_1' => 'Pasul 1',
	'LBL_IMPORT_STEP_2' => 'Pasu 2',
	'LBL_IMPORT_STEP_3' => 'Pasul 3',
	'LBL_IMPORT_STEP_4' => 'Pasul 4',
	'LBL_IMPORT_STEP_1_DESCRIPTION' => 'Selecteaza Fisier',
	'LBL_IMPORT_STEP_2_DESCRIPTION' => 'Specifica Format',
	'LBL_IMPORT_STEP_3_DESCRIPTION' => 'Duplica Manipularea Inregistrarilor',
	'LBL_IMPORT_STEP_4_DESCRIPTION' => 'Harta Coloanelor pentru Modulul de Campuri',
	'Skip' => 'Sari peste',
	'Overwrite' => 'Suprascrie',
	'Merge' => 'Imbina',
	'LBL_IMPORT_SUPPORTED_FILE_TYPES' => 'Tipuri de Fisiere Suportate: .CSV, .VCF',
	'LBL_IMPORT_STEP_3_DESCRIPTION_DETAILED' => 'Selecteaza aceasta optiune pentru a activa si seta criteriile de imbinare a duplicatelor',
	'LBL_CHARACTER_ENCODING' => 'Codarea Caracterelor',
	'LBL_DELIMITER' => 'Delimitator:',
	'LBL_HAS_HEADER' => 'Are Header',
	'LBL_SPECIFY_MERGE_TYPE' => 'Selecteaza cum sa fie manipulate inregistrarile duplicat',
	'LBL_SELECT_MERGE_FIELDS' => 'Selecteaza campurile potrivite pentru a gasi duplicate',
	'LBL_AVAILABLE_FIELDS' => 'Campuri Disponibile',
	'LBL_SELECTED_FIELDS' => 'Campuri care urmeaza a fi potrivite',
	'UTF-8' => 'UTF-8',
	'ISO-8859-1' => 'ISO-8859-1',
	'comma' => ', (virgula)',
	'semicolon' => '; (semi-coloana)',
	'LBL_USE_SAVED_MAPPING' => 'Utilizeaza Harta Salavata:',
	'LBL_SAVE_AS_CUSTOM_MAPPING' => 'Salveaza ca Harta Personalizata',
	'LBL_FILE_COLUMN_HEADER' => 'Header',
	'LBL_ROW_1' => 'Randul 1',
	'LBL_CRM_FIELDS' => 'Campuri CRM',
	'LBL_DEFAULT_VALUE' => 'Valori Implicite',
	'LBL_IMPORT_BUTTON_LABEL' => 'Import',
	'LBL_TOTAL_RECORDS_IMPORTED' => 'Numarul total de inregsitrari importate',
	'LBL_TOTAL_RECORDS_FAILED' => 'Numarul total de inregistrari esuate',
	'LBL_NUMBER_OF_RECORDS_CREATED' => 'Numarul de inregistrari create',
	'LBL_NUMBER_OF_RECORDS_UPDATED' => 'Numarul de inregistrari suprascrise',
	'LBL_NUMBER_OF_RECORDS_SKIPPED' => 'Numarul de inregistrari sarite',
	'LBL_UNDO_LAST_IMPORT' => 'Refa Ultima Inregistrare',
	'LBL_VIEW_LAST_IMPORTED_RECORDS' => 'Ultimele Inregistrari Importate',
	'LBL_IMPORT_MORE' => 'Importa mai multe',
	'LBL_FINISH_BUTTON_LABEL' => 'Termina',
	'LBL_RESULT' => 'Rezultate',
	'ERR_FILE_DOESNT_EXIST' => 'Fisierul nu exista',
	'ERR_CANT_OPEN_FILE' => 'Fisierul nu poate fi deschis sau citit',
	'ERR_UNIMPORTED_RECORDS_IN_QUEUE' => 'Utilizatorul inca are fisiere neimportate in asteptare',
	'ERR_FILE_READ_FAILED' => 'Citirea Fisierului Esuata',
	'LBL_IMPORT_SCHEDULED' => 'Import Programat',
	'Scheduled Import' => 'Programare Import',
	'LBL_SCHEDULED_IMPORT_DETAILS' => 'Importul tau a fost programat si ai sa primesti un email, odata ce importul este complect. <br>
Te rog asigurate ca serverul de Iesire si adresa ta de email sunt configurate pentru a primi notificari pe email',
	'ERR_DETAILS_BELOW' => 'Detalii Listate in jos',
	'LBL_ERROR' => 'Eroare:',
	'LBL_OK_BUTTON_LABEL' => 'OK',
	'TOTAL_RECORDS' => 'Numarul total de inregistrari',
	'LBL_NUMBER_OF_RECORDS_DELETED' => 'Numarul total de inregistrari sterse',
	'LBL_NUMBER_OF_RECORDS_MERGED' => 'Numar total de inregistrari combinate',
	'LBL_TOTAL_RECORDS' => 'Total numar inregistrari',
	'LBL_UNDO_RESULT' => 'Refa Rezultat Import',
	'LBL_LAST_IMPORTED_RECORDS' => 'Ultimele Inregistrari Importate',
	'LBL_NO_ROWS_FOUND' => 'Nu sau gasit randuri',
	'ERR_UNIMPORTED_RECORDS_EXIST' => 'There are still some unimported records in your import queue, blocking you from importing more data. <br>
											Clear data to clean it up and start with fresh import',
	'ERR_FAILED_TO_LOCK_MODULE' => 'Failed to lock the module for import. Re-try again later',
	'LBL_RUNNING' => 'Running',
	'LBL_CLEAR_DATA' => 'Clear Data',
	'ERR_MODULE_IMPORT_LOCKED' => 'You are not allowed to import for this module right now, as another import is in progress. Try again later.',
	'LBL_MODULE_NAME' => 'Module',
	'LBL_USER_NAME' => 'User',
	'LBL_LOCKED_TIME' => 'Locked Time',
	'LBL_CANCEL_IMPORT' => 'Cancel Import',
	'ERR_IMPORT_INTERRUPTED' => 'Current Import has been interrupted. Please try again later.',
	'LBL_INVALID_FILE' => 'Invalid File',
	'LBL_FILE_TYPE' => 'File Type',
	'csv' => 'CSV',
	'vcf' => 'VCard',
	'LBL_FILE_UPLOAD_FAILED' => 'File upload failed',
	'LBL_IMPORT_ERROR_LARGE_FILE' => 'File is to big to upload. The maximum upload size (',
	'LBL_IMPORT_CHANGE_UPLOAD_SIZE' => ') may be incremented in your PHP configuration',
	'LBL_IMPORT_DIRECTORY_NOT_WRITABLE' => 'Import Directory is not writable',
	'LBL_IMPORT_FILE_COPY_FAILED' => 'Error copying file to import. Check file system permissions',
	'ImportInfo' => 'This extension adds import functionality to the application and is accessed via the icon on each module not directly, so you can eliminate it from the menu.',
);
?>