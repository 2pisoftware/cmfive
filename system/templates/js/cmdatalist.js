var cmDatalist = {
	//API Token required for REST service
	API_TOKEN : null,
	//Store of deleted records so they can be recovered
	DELETED_RECORDS : [],
	//Last request, can be cancelled if a newer request comes in before it's completed
	globXhr : null,
	//Order of records to load
	orderBy : null,

	//Init function
	ready : function () {
		//Get and store API token
		if (!localStorage.getItem('api_token') || localStorage.getItem('api_token') == 'null') {
			$.ajax({
				url : '/rest/token',
				dataType : 'json',
				success : function (d) {
					cmDatalist.API_TOKEN = d.success;
					localStorage.setItem('api_token', cmDatalist.API_TOKEN);
				}
			});
		} else {
			cmDatalist.API_TOKEN = localStorage.getItem('api_token');
		}

		//Show last tab if found
		if (!localStorage.getItem('show_tab')) {
			cmDatalist.show_tab = $('#cmfive_datalist_event_tabs li:first').data('type');
		} else {
			cmDatalist.show_tab = localStorage.getItem('show_tab');
		}

		//Bind save triggers to all contentEditable fields
		//Only save when the content changes
		$('body').on('focus', '[contenteditable]', function () {
			var $this = $(this);
			$this.data('before', $this.html());
			return $this;
		}).on('blur keyup paste input', '[contenteditable]', function () {
			var $this = $(this);
			if ($this.data('before') !== $this.html()) {
				$this.data('before', $this.html());
				$this.trigger('change');
			}
			return $this;
		});

		//Switch between modules
		$('#cmfive_datalist_event_tabs li').click(function () {
			if ($(this).hasClass('enabled')) {
				return false;
			}
			$('#cmfive_datalist_event_tabs li').removeClass('enabled');
			$(this).addClass('enabled');
			$('.cmfive_datalist_event_page').hide();
			$('#' + $(this).data('page')).show();
			localStorage.setItem('show_tab', $(this).data('type'));
			cmDatalist.updateFilter($(this).data('page'), $('#' + $(this).data('page')).find('.cmfive_event_filters').data('tag'));
			$('.cmfive_event_filter_actions button[data-action="reset"]');
			$('.cmfive_event_filter_actions button[data-action="reset"]').attr('disabled', 'disabled');
		});

		//Bind sortable fields
		$('table.cmfive_event_page_table thead tr th.sortable').click(function () {
			var $this = $(this);
			$('table.cmfive_event_page_table thead .sortable-dir').removeClass('fi-arrow-down').removeClass('fi-arrow-up');
			if ($this.data('sorted') == false || cmDatalist.orderBy.match($this.data('field')) != $this.data('field') || $this.data('sorted') == 'up') {
				$this.data('sorted', 'down');
				$this.find('.sortable-dir').addClass('fi-arrow-down');
				cmDatalist.orderBy = $this.data('field') + ' DESC';
			} else {
				$this.data('sorted', 'up');
				$this.find('.sortable-dir').addClass('fi-arrow-up');
				cmDatalist.orderBy = $this.data('field') + ' ASC';
			}
			cmDatalist.updateFilter($('.cmfive_datalist_event_page:visible').attr('id'), $('.cmfive_event_filters:visible').data('tag'));
		});

		//Catch triggered changes
		$('table.cmfive_event_page_table tbody').bind('change', function (e) {
			if(e.target.tagName == 'INPUT') {
				//If an input element triggered the change it was a datepicker element
				//Update the displayed date and move the target to the parent
				$(e.target).parent().find('div').text(e.target.value);
				e.target = e.target.parentElement;
			}
			if (e.target.parentElement.dataset.timer == undefined) {
				e.target.parentElement.dataset.timer = setTimeout(function () {
						cmDatalist.updateAnyRow(e.target.parentElement.dataset.id, e.target.parentElement.parentElement.parentElement.parentElement.dataset.type, e.target.parentElement);
					}, 1000);
			} else {
				clearTimeout(e.target.parentElement.dataset.timer);
				e.target.parentElement.dataset.timer = setTimeout(function () {
						cmDatalist.updateAnyRow(e.target.parentElement.dataset.id, e.target.parentElement.parentElement.parentElement.parentElement.dataset.type, e.target.parentElement);
					}, 1000);
			}
		});

		//Show and load requested tab
		if (cmDatalist.show_tab === false) {
			$('#cmfive_datalist_event_tabs li:first').trigger('click');
		} else {
			if ($('#cmfive_datalist_event_tabs li[data-page="cmfive_event_page_' + cmDatalist.show_tab + '"]').length == 1) {
				$('#cmfive_datalist_event_tabs li[data-page="cmfive_event_page_' + cmDatalist.show_tab + '"]').trigger('click');
			} else {
				$('#cmfive_datalist_event_tabs li:first').trigger('click');
			}
		}

		//Bind select filters
		$('.cmfive_event_filter_select select').change(function (e) {
			var v = $(this).val();
			if (v == 'null')
				return false;
			cmDatalist.addTag(
				$(this).closest('.cmfive_event_filters'),
				$(this).closest('.cmfive_event_filter').find('.cmfive_event_filter_tags'),
				$(this).data('field'),
				$(this).data('color'),
				v,
				$(this).find('option:selected').text());
			$(this).val('null');
		});
		$('.cmfive_event_keyword button').click(function (e) {
			e.preventDefault();
			cmDatalist.updateKeyword($(this).parent().find('input'));
			return false;
		});
		$('.cmfive_event_keyword input').keyup(function (e) {
			//$(this).qtip().show();
			if (e.keyCode == 13) {
				cmDatalist.updateKeyword($(this));
			}
		});
		$('.cmfive_event_filter_actions button').click(function (e) {
			e.preventDefault();
			switch ($(this).data('action')) {
			case 'reset':
				cmDatalist.resetFilter($(this));
				break;
			case 'new':
				cmDatalist.addAnyRow($(this));
				break;
			case 'undo':
				cmDatalist.restoreLastRecord();
				break;
			}
		});
		$('.cmfive_event_filter_tags').on('click', '.filter_tag_close', function (e) {
			e.preventDefault();
			cmDatalist.removeTag(
				$(this).closest('.cmfive_event_filters'),
				$(this).parent().data('tag'));
			$(this).parent().hide();
			return false;
		});
	},

	//Adds new blank data row to table
	addAnyRow : function (obj) {
		var page = obj.closest('.cmfive_datalist_event_page');
		var cols = [];
		$(page).find('table thead th').each(function () {
			if (this.dataset.field != undefined) {
				cols.push(this.dataset.field);
			}
		});
		var rows = '<tr data-id="0">';
		for (var j in cols) {
			rows += '<td contentEditable="true" data-field="' + cols[j] + '"></td>';
		}
		rows += '<td><button type="button" onclick="return cmDatalist.deleteRow(this);" class="button round alert">Delete</button></td>';
		rows += '</tr>';
		$(page).find('table tbody').prepend(rows);
		$(page).find('table').trigger("update");
	},

	//Saves any data row to database
	updateAnyRow : function (id, dataType, row) {
		var aUrl = '/rest/save/' + dataType + '/' + id + '?token=' + cmDatalist.API_TOKEN;
		var data = {
			'id' : id
		};
		$(row).find('td').each(function () {
			if(this.dataset.field === undefined) return true; //continue...
			if(
				this.dataset.field.substr(0, 3) == 'dt_' ||
				this.dataset.field.substr(0, 2) == 'd_'
			) {
				data[this.dataset.field] = $(this).find('input').val();
			} else if(this.dataset.field.substr(0, 2) == 't_') {
				data[this.dataset.field] = moment($(this).find('input').val(), 'HH:mm:ss').unix();
			} else {
				data[this.dataset.field] = this.textContent;
			}
		});
		$.ajax({
			url : aUrl,
			method : 'POST',
			data : data,
			dataType : 'json',
			success : function (data) {
				if (data.success) {
					row.dataset.id = data.success.id;
				}
			}
		});
	},

	//Triggers search
	updateKeyword : function (obj) {
		var tags = obj.closest('.cmfive_event_filters').data('tag');
		if (tags == undefined) {
			tags = {};
		}
		if (obj.val() != '') {
			tags['description'] = obj.val();
		} else {
			delete tags['description'];
		}
		obj.closest('.cmfive_event_filters').data('tag', tags);
		cmDatalist.updateFilter(obj.closest('.cmfive_datalist_event_page')[0].id, tags);
	},

	//Resets search
	resetFilter : function (obj) {
		var tags = {};
		var page = obj.closest('.cmfive_datalist_event_page');
		var filters = obj.closest('.cmfive_event_filters');
		filters.data('tag', tags);
		filters.find('.cmfive_event_filter_tag').hide();
		filters.find('.cmfive_event_keyword input').val('');
		cmDatalist.updateFilter(page[0].id, tags);
		page.find('.cmfive_event_filter_actions button[data-action="reset"]').addClass('disabled');
		page.find('.cmfive_event_filter_actions button[data-action="reset"]').attr('disabled', 'disabled');
	},

	//Main function that loads the content based on filters
	updateFilter : function (type, tags) {
		if (cmDatalist.API_TOKEN == null || cmDatalist.API_TOKEN == 'null') {
			setTimeout(function () {
			}, 200);
			return;
		}
		$('#' + type + ' .cmfive_event_filter_actions button[data-action="reset"]').removeClass('disabled');
		$('#' + type + ' .cmfive_event_filter_actions button[data-action="reset"]').removeAttr('disabled');
		if (cmDatalist.globXhr !== null) {
			cmDatalist.globXhr.abort();
		}
		var filter = '';
		var addDescription = '';
		for (var i in tags) {
			if (tags.hasOwnProperty(i)) {
				if (i == 'description') {
					var titleField = $('#' + type).data('titlefield');
					if (titleField.constructor === Array) {
						addDescription = [];
						for (var j in titleField) {
							addDescription.push(titleField[j] + '___contains/' + encodeURI(tags[i]));
						}
						addDescription = '/OR/' + addDescription.join('/OR/');
					} else {
						addDescription = '/AND/' + titleField + '___contains/' + encodeURI(tags[i]);
					}
				} else {
					filter += '/OR/' + encodeURIComponent(tags[i]) + '___equal/' + encodeURIComponent(i);
				}
			}
		}
		filter += addDescription;
		var orderBy = '';
		if (cmDatalist.orderBy !== null) {
			orderBy = '/ORDERBY/' + cmDatalist.orderBy;
		}
		var aUrl = '/rest/index/' + $('#' + type).data('type') + '/LIMIT/500' + orderBy + filter + '?token=' + cmDatalist.API_TOKEN;
		$('#' + type + ' .cmfive_loading_overlay').show();
		$('#' + type + ' .cmfive_loading_overlay').css('top', $('#' + type + ' .cmfive_event_filters').outerHeight() + 'px');
		$('#' + type + ' .cmfive_loading_overlay').css('min-height', (500 - $('#' + type + ' .cmfive_event_filters').outerHeight()) + 'px');
		$('#' + type + ' .cmfive_loading_overlay').css('height', $('#' + type + ' .cmfive_event_page_table').outerHeight() + 'px');
		cmDatalist.globXhr = $.ajax({
				url : aUrl,
				dataType : 'json',
				success : function (data) {
					if (data.error) {
						var msg = '';
						if (data.error.match('No access')) {
							msg = data.error + '\n' + 'You need to add the above module to system.rest_allow in the config.'
						} else {
							msg = data.error;
						}
						alert(msg);
						return false;
					}
					//Show loading overlay
					$('#' + type + ' .cmfive_loading_overlay').css('height', $('#' + type + ' .cmfive_event_page_table').outerHeight() + 'px');
					var rows = '';
					var cols = [];
					//Build list of columns we need to fetch data for
					$('#' + type + ' table thead th').each(function () {
						if (this.dataset.field != undefined) {
							cols.push(this.dataset.field);
						}
					});
					for (var i in data.success) {
						var row = data.success[i];
						rows += '<tr data-id="' + row.id + '">';
						for (var j in cols) {
							if (cols[j].substr(0,2) == 'd_') {
								var date = new Date(row[cols[j]] * 1000);
								rows += '<td data-field="' + cols[j] + '"><input type="hidden" class="datepicker dp_input" value="' + moment(date).format('DD/MM/YYYY') + '" /><div class="datepicker_trigger">' + moment(date).format('DD/MM/YYYY') + '</div></td>';
							} else if (cols[j].substr(0,2) == 't_') {
								rows += '<td data-field="' + cols[j] + '"><input type="hidden" class="timepicker dp_input" value="' + row[cols[j]] + '" /><div class="datepicker_trigger">' + row[cols[j]] + '</div></td>';
							} else if (cols[j].substr(0,3) == 'dt_') {
								var date = new Date(row[cols[j]] * 1000);
								rows += '<td data-field="' + cols[j] + '"><input type="hidden" class="datetimepicker dp_input" value="' + moment(date).format('DD/MM/YYYY HH:mm:ss') + '" /><div class="datepicker_trigger">' + moment(date).format('DD/MM/YYYY HH:mm:ss') + '</div></td>';
							} else {
								rows += '<td contentEditable="true" data-field="' + cols[j] + '">' + row[cols[j]] + '</td>';
							}
						}
						rows += '<td><button type="button" onclick="return cmDatalist.deleteRow(this);" class="button round alert">Delete</button></td>';
						rows += '</tr>';
					}
					$('#' + type + ' table tbody').html(rows);
					$('#' + type + ' table').trigger("update");
					$('.cmfive_loading_overlay').fadeOut();
					$('td input.datepicker').datepicker({dateFormat: 'dd/mm/yy', changeMonth: true, changeYear: true});
					$('td input.datetimepicker').datetimepicker({ampm: false, timeFormat: 'hh:mm:ss', dateFormat: 'dd/mm/yy', changeMonth: true, changeYear: true});
					$('td input.timepicker').timepicker({ampm: false, timeFormat: 'hh:mm:ss', dateFormat: 'dd/mm/yy'});
					$('td div.datepicker_trigger').click(function() {
						$(this).parent().find('.dp_input').datepicker('show');
					});
				}
			});
	},

	//Restores most recently deleted row
	restoreLastRecord : function () {
		if (cmDatalist.DELETED_RECORDS.length == 0) {
			return;
		}
		var restore = cmDatalist.DELETED_RECORDS.pop();
		var aUrl = '/rest/save/' + restore.type + '?token=' + cmDatalist.API_TOKEN;
		var data = {
			id : restore.id,
			is_deleted : 0
		};
		$.ajax({
			url : aUrl,
			method : 'POST',
			data : data,
			dataType : 'json',
			success : function (data) {
				restore.tr.show();
				if (cmDatalist.DELETED_RECORDS.length == 0) {
					restore.tr.parent().parent().parent().find('.cmfive_event_filters button[data-action="undo"]').addClass('disabled').prop('disabled', true);
				}
			}
		});
	},

	//Soft deletes a data row
	deleteRow : function (self) {
		var tr = $(self).parent().parent();
		var aUrl = '/rest/delete/' + tr.parent().parent().parent().data('type') + '/' + tr.data('id') + '?token=' + cmDatalist.API_TOKEN;
		$.ajax({
			url : aUrl,
			method : 'POST',
			dataType : 'json',
			success : function (data) {
				cmDatalist.DELETED_RECORDS.push({
					'type' : tr.parent().parent().parent().data('type'),
					'id' : tr.data('id'),
					'tr' : tr
				});
				tr.parent().parent().parent().find('.cmfive_event_filters button[data-action="undo"]').removeClass('disabled').prop('disabled', false);
				tr.hide();
			}
		});
	},

	//Filter tag functions
	removeTag : function (parent, str) {
		var tags = parent.data('tag');
		if (tags == undefined) {
			tags = {};
		}
		delete tags[str];
		parent.data('tag', tags);
		cmDatalist.updateFilter(parent.closest('.cmfive_datalist_event_page')[0].id, tags);
	},
	addTag : function (parent, apdObj, field, color, val, title) {
		var tags = parent.data('tag');
		if (tags == undefined) {
			tags = {};
		}
		if (!tags.hasOwnProperty(val)) {
			tags[val] = field;
			parent.data('tag', tags);
			if (apdObj.find('.cmfive_event_filter_tag[data-tag="' + val + '"]').length > 0) {
				apdObj.find('.cmfive_event_filter_tag[data-tag="' + val + '"]').show();
			} else {
				apdObj.append('<div class="cmfive_event_filter_tag ' + color + '" data-tag="' + val + '">' + title + '<span class="filter_tag_close"></span></div>');
			}
			cmDatalist.updateFilter(parent.closest('.cmfive_datalist_event_page')[0].id, tags);
		}
	}
}

$(function () {
	cmDatalist.ready();
});
