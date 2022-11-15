class Operation {

	constructor() {
		this.qid = 0;
		this.OPGridInstance = '__empty__';
		this.CheckedRows = [];
		this.__API = 'index.php?module=Utilities&action=UtilitiesAjax&file=OperationAPI';
	}

	Filter(qid, qmodule) {
		this.qid = qid;
		this.Request(this.__API, 'post', {
			qid: this.qid,
			opaction: 'GridColumns'
		}).then(function (columns) {
			operation.Grid(columns, qmodule);
		});
	}

	Grid(columns, qmodule) {
		document.getElementById('listview-content').innerHTML = '';
		this.CheckedRows = [];
		this.OPGridInstance = new tui.Grid({
			el: document.getElementById('listview-content'),
			rowHeaders: ['checkbox'],
			columns: columns,
			data: {
				api: {
					readData: {
						url: `index.php?module=cbQuestion&action=cbQuestionAjax&file=getJSON&qid=${this.qid}&qmodule=${qmodule}&contextid=`,
						method: 'GET'
					}
				}
			},
			useClientSort: false,
			pageOptions: {
				perPage: 5
			},
			rowHeight: 'auto',
			bodyHeight: 'auto',
			scrollX: false,
			scrollY: true,
			columnOptions: {
				resizable: true
			},
			header: {
				align: 'left',
				valign: 'top'
			}
		});
		tui.Grid.applyTheme('striped');
		this.OPGridInstance.on('checkAll', (ev) => {
			this.SetRows();
		});
		this.OPGridInstance.on('check', (ev) => {
			this.SetRows();
		});
		this.OPGridInstance.on('uncheckAll', (ev) => {
			this.SetRows();
		});
		this.OPGridInstance.on('uncheck', (ev) => {
			this.SetRows();
		});
		this.OPGridInstance.on('afterPageMove', (ev) => {
			this.CheckRows(ev);
		});
	}

	SetRows() {
		const page = this.OPGridInstance.getPagination()._currentPage;
		const rows = this.OPGridInstance.getCheckedRows();
		this.CheckedRows[page] = rows;
	}

	CheckRows(ev) {
		const _currentPage = this.CheckedRows[ev.page];
		for (let i in _currentPage) {
			this.OPGridInstance.check(_currentPage[i].rowKey);
		}
	}

	FilterIds() {
		const ids = [];
		for (let pg in this.CheckedRows) {
			for (let j = 0; j < this.CheckedRows[pg].length; j++) {
				ids.push(this.CheckedRows[pg][j]['id']);
			}
		}
		return ids;
	}

	async Request(url, method, body = {}) {
		const options = {
			method: method,
			credentials: 'same-origin',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
			}
		};
		if (method == 'post') {
			options.body = '&'+csrfMagicName+'='+csrfMagicToken+'&data='+JSON.stringify(body);
		}
		const response = await fetch(url, options);
		return response.json();
	}
}

function MassOperations(wfid) {
	let rows = operation.FilterIds();
	if (rows.length == 0) {
		ldsPrompt.show(alert_arr.ERROR, 'Please select at least one row', 'error');
		return false;
	}
	runBAWorkflow(wfid, rows.join(';'));
}

var operation = new Operation();