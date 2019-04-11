<?php
 /*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *************************************************************************************************
 *  Module       : Business Mappings:: DecisionTable
 *  Version      : 5.4.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************
 * The accepted format is:
<decision>
<hitPolicy></hitPolicy>  <!-- U F C A R G -->
<aggregate></aggregate>  <!-- only available if hitPolicy=G: sum,min,max,count -->
<rules>
<rule>
  <sequence></sequence>
  <expression></expression>
  <output></output>  <!-- ExpressionResult, FieldValue, crmObject -->
</rule>
<rule>
  <sequence></sequence>
  <mapid></mapid>
  <output></output>  <!-- ExpressionResult, FieldValue, crmObject -->
</rule>
<rule>
  <sequence></sequence>
  <decisionTable>
    <module></module>
    <conditions>  <!-- QueryGenerator conditions -->
        <condition>
          <input></input>  <!-- context variable name -->
          <operation></operation>  <!-- QueryGenerator operators -->
          <field></field>  <!-- fieldname of module -->
        </condition>
    </conditions>
    <orderby></orderby>  <!-- column to order the records by -->
    <searches>
      <search>
        <condition>
          <input></input>  <!-- context variable name -->
          <operation></operation>  <!-- QueryGenerator operators -->
          <field></field>  <!-- fieldname of module -->
        </condition>
      </search>
    </searches>
    <output></output>  <!-- fieldname -->
  </decisionTable>
  <output></output>  <!-- ExpressionResult, FieldValue, crmObject -->
</rule>
</rules>
</decision>
 *************************************************************************************************/

class DecisionTable extends processcbMap {

    public $mapping = array();

    public function processMap($arguments) {
        global $adb, $current_user;
        $this->mapping = $this->convertMap2Array();
        return $this->mapping;
    }

    private function convertMap2Array() {
        $xml = $this->getXMLContent();
        $mapping = array();
        $mapping['hitPolicy'] = (String)$xml->hitPolicy;
        if ($mapping['hitPolicy'] == 'G') {
          $mapping['aggregate'] = (String)$xml->aggregate;
        }
        $mapping['rules'] = array();

        foreach ($xml->rules->rule as $key => $value) {
            $rule = array();
            $rule['sequence'] = (String)$value->sequence;
            if (isset($value->expression)) {
                $rule['expression'] = (String)$value->expression;
            } else if (isset($value->mapid)) {
                $rule['mapid'] = (String)$value->mapid;
            } else if (isset($value->decisionTable)) {
                $rule['decisionTable']['module'] = (String)$value->decisionTable->module;
                $rule['decisionTable']['conditions'] = array();
                foreach ($value->decisionTable->conditions->condition as $k => $v) {
                  $condition = array();
                  $condition['input'] = $v->input;
                  $condition['operation'] = $v->operation;
                  $condition['field'] = $v->field;
                  $rule['decisionTable']['conditions'][] = $condition;
                }
                $rule['decisionTable']['orderby'] = (String)$value->decisionTable->orderby;
                $rule['decisionTable']['searches'] = array();
                foreach ($value->decisionTable->searches->search as $k => $v) {
                  $search = array();
                  $search['input'] = $v->input;
                  $search['operation'] = $v->operation;
                  $search['field'] = $v->field;
                  $rule['decisionTable']['searches'][] = $condition;
                }
            }
            $rule['output'] = (String)$value->output;
            $mapping['rules'][] = $rule;
        }
    }
}