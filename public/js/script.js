var App = 
{
	vr : 
	{
		dataForms : {
			workSelect: ''
		},
		backgrounds : ['1.jpg', '2.jpg', '3.jpg', '4.jpg', '5.jpg']
	},
	
	init : function() {
		App.page.setBackground();
		App.page.studentSelect();
	},
	
	page : {
		setBackground : function(){
			length = App.vr.backgrounds.length;
			rnd = Math.floor((Math.random()*length)); rnd = (rnd < length-1) ? rnd : length-1;
			background = '/img/backgrounds/' + App.vr.backgrounds[rnd];
			$('body').css('background-image', 'url("' + background + '")');
		},
	
		studentSelect : function(){
			$('.studentSelect').select2({
				formatResult: format,
    		formatSelection: format2
			}).removeClass('hidden');
		},
		
		workSelect : function(type){
			if(!App.vr.dataForms.workSelect) {
				works = JSON && JSON.parse(works) || $.parseJSON(works);
				App.vr.dataForms.workSelect = works;
			}
			
			data = App.vr.dataForms.workSelect[type];
			$('.workSelect').select2({
				data:{ results: data, text: 'name' },
				formatSelection: format3,
    		formatResult: format3
			})
		},
		
		workEditingSubscribe : function(){			
			$('.works td[contenteditable]').on("focusout", function(){
					App.data.saveWork($(this));
			})	
		},
		
		tableSortable : function(){
			$('.works').sortable({
				handle: 'i.icon-move',
				itemPath: '> tbody',
				itemSelector: 'tr',
				placeholder: '<tr class="placeholder"/>',
				onDrop : function (item, container, _super){
					v = {}
					$('.works tr').each(function(index, element) {
            v[$(element).data('id')	] = index;
          });
					App.data.sortWork(v);
					_super(item)
				}
			})	
		},
		
		error : function(text){
			alert(text)
		}
	},
	
	data : {
		applyForAdmission : function(id){
			data = {
				action	: 'applyForAdmission',
				data		: {
					faculty	: id
				}
			}
			$.post('/ajax/open', data, function(data){
				if(data.error)
					App.page.error(data.error);
				else {
					$('.faculty'+ data.faculty).addClass('faculty faculty-await')
					$('.applyForAdmission').remove();
				}
			}, 'json').error(function(jqXHR, textStatus, errorThrown){
				App.page.error('Error getting data. Server responsed with message: "' + errorThrown +'"');
			})
		},
		
		markWork : function(){
			form = $('.work-form')
			student = form.find('.studentSelect option:selected').val();
			work = form.find('input.workSelect').val();
			if(student && work) {
				data = {
					action	: 'markWork',
					data		: {
						student	: student,
						work		:	work
					}
				}
				$.post('/ajax/close', data, function(data){
					if(data.error)
						App.page.error(data.error);
					else {
						window.location.reload();
					}
				}, 'json').error(function(jqXHR, textStatus, errorThrown){
					App.page.error('Error getting data. Server responsed with message: "' + errorThrown +'"');
				})
			} else {
				App.page.error('Необходимо выбрать студента и дело.');
			}
		},
		
		saveWork : function(obj){
			data = {
				action	: 'saveWork',
				data		: {
					field	: obj.data('field'),
					data	:	obj.text(),
					id		: obj.parent().data('id')
				}
			}
			$.post('/ajax/admin', data, function(data){
				if(data.error)
					App.page.error(data.error);
				else {
					color = $.Color( obj.css('backgroundColor') )
					obj.stop().animate({backgroundColor: color.lightness('-=0.1')}, 200).delay(100).animate({backgroundColor: color}, 200)
				}
			}, 'json').error(function(jqXHR, textStatus, errorThrown){
				App.page.error('Error getting data. Server responsed with message: "' + errorThrown +'"');
			})
		},
		
		removeWork : function(id){
			if(!confirm('Уверены, что хотите удалить дело?')) return false;
			
			data = {
				action	: 'removeWork',
				data		: {
					id		: id
				}
			}
			$.post('/ajax/admin', data, function(data){
				if(data.error)
					App.page.error(data.error);
				else {
					$('.row'+ id)
						.find('td').wrapInner('<div style="display: block;" />')
						.animate({paddingTop: 0, paddingBottom: 0}, 500)
						.parent()
						.find('td > div').animate({height: 0, opacity: 0}, 700, function() {
       				$(this).parent().parent().remove();
    				});
				}
			}, 'json').error(function(jqXHR, textStatus, errorThrown){
				App.page.error('Error getting data. Server responsed with message: "' + errorThrown +'"');
			})
		},
		
		sortWork : function(v){
			data = {
				action	: 'sortWork',
				data		: {
					v		: v
				}
			}
			
			$.post('/ajax/admin', data, function(data){
				if(data.error)
					App.page.error(data.error);
			}, 'json').error(function(jqXHR, textStatus, errorThrown){
				App.page.error('Error getting data. Server responsed with message: "' + errorThrown +'"');
			})
		}
	}
}

function format(state) {
	id = state.id;
	option = $('option#'+ id)
	userpic = option.data('userpic'); faculty = option.data('faculty')
	return "<img class='pull-left margin-right-10' src='" + userpic + "' style='width:40px;'/> " + "<small>" + state.text +'</small> <br> <small><b>'+ faculty +'</b></small>';
}
function format2(state) {
	id = state.id;
	option = $('option#'+ id)
	userpic = option.data('userpic')
	return "<img src='" + userpic + "' style='width:20px;'/> "+ state.text;
}
function format3(item) { return '<small class="badge">'+ item.cost +'</small> ' + item.name ; }