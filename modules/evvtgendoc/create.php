<?php
/*************************************************************************************************
 * Copyright 2012 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
 * You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
 * Vizsage Public License (the "License"). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
 *************************************************************************************************
 *  Module       : evvtgendoc
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
require_once 'OpenDocument.php'; // open document class

//create a new OpenDocument Text file
$odt = new OpenDocument;
//add heading
$h = $odt->createHeading('Heading', 1);
//create paragraph
$p1 = $odt->createParagraph('Paragraph 1');
//set paragraph styles
$p1->style->fontSize = '12pt';
$p1->style->fontName = 'Times New Roman';
$p1->style->color = '#009900';
$p1->style->underlineStyle = 'dotted';
$p1->style->underlineColor = '#009999';
$p1->style->underlineWidth = '2pt';
//create second paragraph
$p2 = $odt->createParagraph('Paragraph 2');
//copy styles from first one
$p2->style->copy($p1->style);
//insert space into paragraph
$p1->createTextElement(' ');
//create a link inside a paragraph
$a1 = $p1->createHyperlink('', 'https://ya.ru', 'simple', '_self', 'link');
//insert text in link
$span = $a1->createSpan('Ya.ru');
//apply color to text
$span->style->color = '#000099';
//insert space to heading
$h->createTextElement(' ');
//create link in heading
$a2 = $h->createHyperlink('Ya.ru', 'https://ya.ru');
//apply underline color to link
$a2->style->underlineColor = '#990000';
//save as test.odt
$odt->save('test.odt');

echo 'saved as test.odt';
?>
