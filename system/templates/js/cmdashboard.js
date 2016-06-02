var cmDashboard = {
	API_TOKEN: null,
	DELETED_RECORDS: [],
	assignments: [],
	show_tab: null,
	globXhr: null,
	orderBy: null,
	ready: function() {
		if(!localStorage.getItem('api_token') || localStorage.getItem('api_token') == 'null') {
			$.ajax({
				url: '/rest/token',
				dataType: 'json',
				success:function(d) {
					cmDashboard.API_TOKEN = d.success;
					localStorage.setItem('api_token', cmDashboard.API_TOKEN);
				}
			});
		} else {
			cmDashboard.API_TOKEN = localStorage.getItem('api_token');
		}
		if(!localStorage.getItem('show_tab')) {
			cmDashboard.show_tab = $('#cmfive_dashboard_event_tabs li:first').data('type');
		} else {
			cmDashboard.show_tab = localStorage.getItem('show_tab');
		}
		$('body').on('focus', '[contenteditable]', function() {
			var $this = $(this);
			$this.data('before', $this.html());
			return $this;
		}).on('blur keyup paste input', '[contenteditable]', function() {
			var $this = $(this);
			if ($this.data('before') !== $this.html()) {
				$this.data('before', $this.html());
				$this.trigger('change');
			}
			return $this;
		});
		$('#cmfive_dashboard_event_tabs li').click(function() {
			if($(this).hasClass('enabled')) {
				return false;
			}
			$('#cmfive_dashboard_event_tabs li').removeClass('enabled');
			$(this).addClass('enabled');
			$('.cmfive_dashboard_event_page').hide();
			$('#'+$(this).data('page')).show();
			localStorage.setItem('show_tab', $(this).data('type'));
			cmDashboard.updateFilter($(this).data('page'), $('#'+$(this).data('page')).find('.cmfive_event_filters').data('tag'));
			$('.cmfive_event_filter_actions button[data-action="reset"]');
			$('.cmfive_event_filter_actions button[data-action="reset"]').attr('disabled', 'disabled');
		});
		$('table.cmfive_event_page_table thead tr th.sortable').click(function() {
			var $this = $(this);
			$('table.cmfive_event_page_table thead .sortable-dir').removeClass('fi-arrow-down').removeClass('fi-arrow-up');
			if($this.data('sorted') == false || cmDashboard.orderBy.match($this.data('field')) != $this.data('field') || $this.data('sorted') == 'up') {
				$this.data('sorted', 'down');
				$this.find('.sortable-dir').addClass('fi-arrow-down');
				cmDashboard.orderBy = $this.data('field')+' DESC';
			} else {
				$this.data('sorted', 'up');
				$this.find('.sortable-dir').addClass('fi-arrow-up');
				cmDashboard.orderBy = $this.data('field')+' ASC';
			}
			cmDashboard.updateFilter($('.cmfive_dashboard_event_page:visible').attr('id'),$('.cmfive_event_filters:visible').data('tag'));
		});
		$('table.cmfive_event_page_table tbody').bind('change', function(e) {
			if(e.target.parentElement.dataset.timer == undefined) {
				e.target.parentElement.dataset.timer = setTimeout(function() {
					cmDashboard.updateAnyRow(e.target.parentElement.dataset.id, e.target.parentElement.parentElement.parentElement.parentElement.dataset.type, e.target.parentElement);
				}, 1000);
			} else {
				clearTimeout(e.target.parentElement.dataset.timer);
				e.target.parentElement.dataset.timer = setTimeout(function() {
					cmDashboard.updateAnyRow(e.target.parentElement.dataset.id, e.target.parentElement.parentElement.parentElement.parentElement.dataset.type, e.target.parentElement);
				}, 1000);
			}
		});
		
		if(cmDashboard.show_tab === false) {
			$('#cmfive_dashboard_event_tabs li:first').trigger('click');
		} else {
			if($('#cmfive_dashboard_event_tabs li[data-page="cmfive_event_page_'+cmDashboard.show_tab+'"]').length == 1) {
				$('#cmfive_dashboard_event_tabs li[data-page="cmfive_event_page_'+cmDashboard.show_tab+'"]').trigger('click');
			} else {
				$('#cmfive_dashboard_event_tabs li:first').trigger('click');
			}
		}
		$('.cmfive_event_filter_select select').change(function(e) {
			var v = $(this).val();
			if(v == 'null') return false;
			cmDashboard.addTag(
				$(this).closest('.cmfive_event_filters'),
				$(this).closest('.cmfive_event_filter').find('.cmfive_event_filter_tags'),
				$(this).data('field'),
				$(this).data('color'),
				v,
				$(this).find('option:selected').text()
			);
			$(this).val('null');
		});
		$('.cmfive_event_keyword button').click(function(e) {
			e.preventDefault();
			cmDashboard.updateKeyword($(this).parent().find('input'));
			return false;
		});
		$('.cmfive_event_keyword input').keyup(function(e) {
			//$(this).qtip().show();
			if(e.keyCode == 13) {
				cmDashboard.updateKeyword($(this));
			}
		});
		$('.cmfive_event_filter_actions button').click(function(e) {
			e.preventDefault();
			switch($(this).data('action')) {
				case 'reset':
					cmDashboard.resetFilter($(this));
					break;
				case 'new':
					cmDashboard.addAnyRow($(this));
					break;
				case 'undo':
					cmDashboard.restoreLastRecord();
					break;
			}
		});
		$('.cmfive_event_filter_tags').on('click', '.filter_tag_close', function(e) {
			e.preventDefault();
			cmDashboard.removeTag(
				$(this).closest('.cmfive_event_filters'),
				$(this).parent().data('tag')
			);
			$(this).parent().hide();
			return false;
		});
	},
	addAnyRow: function(obj) {
		var page = obj.closest('.cmfive_dashboard_event_page');
		var cols = [];
		$(page).find('table thead th').each(function() {
			if(this.dataset.field != undefined) {
				cols.push(this.dataset.field);
			}
		});
		var rows = '<tr data-id="0">';
		for(j in cols) {
			rows += '<td contentEditable="true" data-field="'+cols[j]+'"></td>';
		}
		rows += '<td><button type="button" onclick="return cmDashboard.deleteRow(this);" class="button round alert">Delete</button></td>';
		rows += '</tr>';
		$(page).find('table tbody').prepend(rows);
		$(page).find('table').trigger("update");
	},
	updateAnyRow: function(id, dataType, row) {
		var aUrl = '/rest/save/'+dataType+'/'+id+'?token='+cmDashboard.API_TOKEN;
		var data = {'id': id};
		$(row).find('td').each(function() {
			data[this.dataset.field] = this.textContent;
		});
		$.ajax({
			url: aUrl,
			method:'POST',
			data: data,
			dataType: 'json',
			success: function(data) {
				if(data.success) {
					row.dataset.id = data.success.id;
				}
			}
		});
	},
	updateKeyword: function(obj) {
		var tags = obj.closest('.cmfive_event_filters').data('tag');
		if(tags == undefined) {
			tags = {};
		}
		if(obj.val() != '') {
			tags['description'] = obj.val();
		} else {
			delete tags['description'];
		}
		obj.closest('.cmfive_event_filters').data('tag', tags);
		cmDashboard.updateFilter(obj.closest('.cmfive_dashboard_event_page')[0].id ,tags);
	},
	resetFilter: function(obj) {
		var tags = {};
		var page = obj.closest('.cmfive_dashboard_event_page');
		var filters = obj.closest('.cmfive_event_filters');
		filters.data('tag', tags);
		filters.find('.cmfive_event_filter_tag').hide();
		filters.find('.cmfive_event_keyword input').val('');
		cmDashboard.updateFilter(page[0].id ,tags);
		page.find('.cmfive_event_filter_actions button[data-action="reset"]').addClass('disabled');
		page.find('.cmfive_event_filter_actions button[data-action="reset"]').attr('disabled', 'disabled');
	},
	updateFilter: function(type, tags) {
		if(cmDashboard.API_TOKEN == null || cmDashboard.API_TOKEN == 'null') {
			setTimeout(function() {cmDashboard.updateFilter(type, tags);}, 200);
			return;
		}
		$('#'+type+' .cmfive_event_filter_actions button[data-action="reset"]').removeClass('disabled');
		$('#'+type+' .cmfive_event_filter_actions button[data-action="reset"]').removeAttr('disabled');
		if(cmDashboard.globXhr !== null) {
			cmDashboard.globXhr.abort();
		}
		var filter = '';
		var addDescription = '';
		for(i in tags) {
			if(tags.hasOwnProperty(i)) {
				if(i == 'description') {
					addDescription = '/AND/' + $('#'+type).data('titlefield')+'___contains/' + encodeURI(tags[i]);
				} else {
					filter += '/OR/' + encodeURIComponent(tags[i])+'___equal/' + encodeURIComponent(i);
				}
			}
		}
		filter += addDescription;
		var orderBy = '';
		if(cmDashboard.orderBy !== null) {
			orderBy = '/ORDERBY/'+cmDashboard.orderBy;
		}
		var aUrl = '/rest/index/'+$('#'+type).data('type')+'/LIMIT/500'+orderBy+filter+'?token='+cmDashboard.API_TOKEN;
		$('#'+type+' .cmfive_loading_overlay').show();
		$('#'+type+' .cmfive_loading_overlay').css('top', $('#'+type+' .cmfive_event_filters').outerHeight()+'px');
		$('#'+type+' .cmfive_loading_overlay').css('min-height', (500-$('#'+type+' .cmfive_event_filters').outerHeight())+'px');
		$('#'+type+' .cmfive_loading_overlay').css('height', $('#'+type+' .cmfive_event_page_table').outerHeight()+'px');
		cmDashboard.globXhr = $.ajax({
			url: aUrl,
			dataType: 'json',
			success: function(data) {
				if(data.error) {
					var msg = '';
					if(data.error.match('No access')) {
						msg = data.error + '\n' + 'You need to add the above module to system.rest_allow in the config.'
					} else {
						msg = data.error;
					}
					alert(msg);
					return false;
				}
				$('#'+type+' .cmfive_loading_overlay').css('height', $('#'+type+' .cmfive_event_page_table').outerHeight()+'px');
				var rows = '';
				var cols = [];
				$('#'+type+' table thead th').each(function() {
					if(this.dataset.field != undefined) {
						cols.push(this.dataset.field);
					}
				});
				for(i in data.success) {
					var row = data.success[i];
					rows += '<tr data-id="'+row.id+'">';
					for(j in cols) {
						rows += '<td contentEditable="true" data-field="'+cols[j]+'">'+row[cols[j]]+'</td>';
					}
					rows += '<td><button type="button" onclick="return cmDashboard.deleteRow(this);" class="button round alert">Delete</button></td>';
					rows += '</tr>';
				}
				$('#'+type+' table tbody').html(rows);
				$('#'+type+' table').trigger("update");
				$('.cmfive_loading_overlay').fadeOut();
				//addqTip()
			}
		});
	},
	restoreLastRecord: function() {
		if(cmDashboard.DELETED_RECORDS.length == 0) return;
		var restore = cmDashboard.DELETED_RECORDS.pop();
		var aUrl = '/rest/save/'+restore.type+'?token='+cmDashboard.API_TOKEN;
		var data = {id:restore.id, is_deleted:0}; 
		$.ajax({
			url: aUrl,
			method:'POST',
			data: data,
			dataType: 'json',
			success: function(data) {
				restore.tr.show();
				if(cmDashboard.DELETED_RECORDS.length == 0) {
					restore.tr.parent().parent().parent().find('.cmfive_event_filters button[data-action="undo"]').addClass('disabled').prop('disabled', true);
				}
			}
		});
	},
	deleteRow: function (self) {
		var tr = $(self).parent().parent();
		var aUrl = '/rest/delete/'+tr.parent().parent().parent().data('type')+'/'+tr.data('id')+'?token='+cmDashboard.API_TOKEN;
		$.ajax({
			url: aUrl,
			method:'POST',
			dataType: 'json',
			success: function(data) {
				cmDashboard.DELETED_RECORDS.push({'type':tr.parent().parent().parent().data('type'),'id':tr.data('id'),'tr':tr});
				tr.parent().parent().parent().find('.cmfive_event_filters button[data-action="undo"]').removeClass('disabled').prop('disabled', false);
				tr.hide();
			}
		});
	},
	removeTag: function(parent, str) {
		var tags = parent.data('tag');
		if(tags == undefined) {
			tags = {};
		}
		delete tags[str];
		parent.data('tag', tags);
		cmDashboard.updateFilter(parent.closest('.cmfive_dashboard_event_page')[0].id, tags);
	},
	addTag: function(parent, apdObj, field, color, val, title) {
		var tags = parent.data('tag');
		if(tags == undefined) {
			tags = {};
		}
		if(!tags.hasOwnProperty(val)) {
			tags[val] = field;
			parent.data('tag', tags);
			if(apdObj.find('.cmfive_event_filter_tag[data-tag="'+val+'"]').length > 0) {
				apdObj.find('.cmfive_event_filter_tag[data-tag="'+val+'"]').show();
			} else {
				apdObj.append('<div class="cmfive_event_filter_tag '+color+'" data-tag="'+val+'">'+title+'<span class="filter_tag_close"></span></div>');
			}
			cmDashboard.updateFilter(parent.closest('.cmfive_dashboard_event_page')[0].id, tags);
		}
	}
}

$(function() {
	cmDashboard.ready();
});