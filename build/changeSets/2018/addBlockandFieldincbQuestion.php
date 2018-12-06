* License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
 *************************************************************************************************/

class addOrderInformation extends cbupdaterWorker {

    public function applyChange() {
        global $adb;

        if ($this->hasError()) {
            $this->sendError();
        }
        if ($this->isApplied()) {
            $this->sendMsg('Changeset '.get_class($this).' already applied!');
        } else {
            $this->sendMsg('This changeset add new blocks and fields to cbQuestion module');

            // Fields preparations
            $fieldLayout = array(
                'cbQuestion' => array(
                    'Order Information' => array(
                        'orderby' => array(
                            'columntype'=>'TEXT',
                            'typeofdata'=>'V~O',
                            'uitype'=>19,
                            'label' => 'Order by Column',
                            'displaytype'=>'1',
                        ),
                    )
                    'Grouping Information' => array(
                        'groupby' => array(
                            'columntype'=>'TEXT',
                            'typeofdata'=>'V~O',
                            'uitype'=>19,
                            'label' => 'Group by Column',
                            'displaytype'=>'1',
                        ),
                    )
                ),
            );
            $this->massCreateFields($fieldLayout);

            $this->sendMsg('Changeset '.get_class($this).' applied!');
            $this->markApplied(false);
        }
        $this->finishExecution();
    }
}