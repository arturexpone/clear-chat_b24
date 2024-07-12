;(function () {
	BX.namespace('CRMsoft.ImClear.List');

	BX.CRMsoft.ImClear.List = {
		grid_id: null,
		alertPopup: null,
		consfirmPopup: null,
		getAlertPopup: function () {
			if (this.alertPopup === null) {
				this.alertPopup = BX.PopupWindowManager.create(
					"popup-message",
					null,
					{
						closeIcon: true,
						lightShadow: true,
						closeByEsc: true,
					}
				);
			}

			return this.alertPopup;
		},

		reloadGrid: function () {
			var reloadParams = {apply_filter: 'Y', clear_nav: 'Y'};
			var gridObject = BX.Main.gridManager.getById(this.grid_id);

			if (gridObject.hasOwnProperty('instance')) {
				gridObject.instance.reloadTable('POST', reloadParams);
			}
		},
		clearChat: function (id, messageOut) {
			messageOut = messageOut ? '<br /><blockquote>' + id + ': ' + messageOut + '</blockquote>' : '';
			let self = this;
			self.confirmPopup = BX.PopupWindowManager.create(
				"popup-delete-message-" + id,
				null,
				{
					closeIcon: false,
					lightShadow: true,
					closeByEsc: true,
					content: '<div class="ui-alert ui-alert-danger ui-alert-icon-warning"><span class="ui-alert-message">' + BX.message('JS_CRMSOFT_IMC_CLEAR_CHAT_APPROVE') + messageOut + '</span></div>',
					buttons: [
						new BX.PopupWindowButton({
							text: BX.message('JS_CRMSOFT_IMC_CLEAR_CHAT'),
							className: "ui-btn ui-btn-danger",
							events: {
								click: function () {
									this.buttonNode.classList.add('ui-btn-clock');
									BX.ajax.runAction('crmsoft:imclear.api.Message.clearChat', {
										data: {
											id: id
										}
									}).then(
										function (response) {
											this.reloadGrid();
											this.confirmPopup.close();
										}.bind(self),
										function (response) {
											this.confirmPopup.close();
											var alertPopup = this.getAlertPopup();
											var content = '<div class="ui-alert ui-alert-danger"><span class="ui-alert-message">';
											var errors = [];
											for (var j = 0; j < response.errors.length; j++) {
												errors.push(response.errors[j].message);
											}
											content += errors.join('<br />');
											content += '</span></div>';
											alertPopup.setContent(content);
											alertPopup.show();
										}.bind(self));
								}
							}
						}),
						new BX.PopupWindowButton({
							text: BX.message('JS_CRMSOFT_IMC_CANCEL'),
							className: "ui-btn ui-btn-link",
							events: {
								click: function () {
									this.popupWindow.close();
								}
							}
						})
					]
				}
			);
			self.confirmPopup.show();
		},
		setTimer: function(id) {
			var self = this;
			var currentDate = new Date();
			var timerPopup = BX.PopupWindowManager.create(
				"popup-timer-message-" + id,
				null,
				{
					closeIcon: false,
					lightShadow: true,
					closeByEsc: true,
					titleBar: BX.message('JS_CRMSOFT_IMC_SET_TIMER_POPUP'),
					content: BX.create('form', {
						attrs: {
							id: 'imc-timer-form-' + id
						},
						children: [
							BX.create('div', {
								props: {
									className: 'imc-timer-popup_row'
								},
								children: [
									BX.create('div', {
										attrs: {
											id: 'imc-timer-content-interval-' + id
										},
										props: {
											className: 'imc-timer-popup_type-content-show'
										},
										children: [
											BX.create('div', {
												attrs: {
													className: 'imc-timer-popup_row'
												},
												children: [
													BX.create('label', {
														props: {
															className: 'imc-timer-popup_label'
														},
														text: 'С какой периодичностью запускать очистку'
													}),
													BX.create('div',{
														attrs: {className: 'ui-ctl ui-ctl-textbox ui-ctl-inline'},
														children:[
															BX.create('input', {
																attrs: {
																	type: 'text',
																	id: 'imc-timer-intervalvalue-' + id,
																	name: 'imcTimerIntervalValue',
																	value: '',
																	className: 'ui-ctl-element'
																},
																events: {
																	change: function(e) {
																		e.target.value = e.target.value.replace(/[^\d]/g, '');
																	},
																	input: function(e) {
																		e.target.value = e.target.value.replace(/[^\d]/g, '');
																	}
																}
															})
														]
													})
												]
											}),
											BX.create('div', {
												attrs: {
													className: 'imc-timer-popup_row'
												},
												children: [
													BX.create('label', {
														props: {
															className: 'imc-timer-popup_label'
														},
														text: BX.message('JS_CRMSOFT_IMC_SET_TIMER_POPUP_INTERVAL_TYPE_LABEL')
													}),
													BX.create('div', {
														attrs: {className: 'ui-ctl ui-ctl-after-icon ui-ctl-dropdown ui-ctl-inline'},
														children: [
															BX.create('div', {attrs:{className: 'ui-ctl-after ui-ctl-icon-angle'}}),
															BX.create('select', {
																attrs: {
																	id: 'imc-timer-intervaltype-' + id,
																	className: 'ui-ctl-element',
																	name: 'imcTimerIntervalType'
																},
																children: [
																	BX.create('option', {
																		attrs: {value: ''},
																		text: ''
																	}),
																	BX.create('option', {
																		attrs: {value: 'minute'},
																		text: BX.message('JS_CRMSOFT_IMC_SET_TIMER_POPUP_INTERVAL_TYPE_MINUTE')
																	}),
																	BX.create('option', {
																		attrs: {value: 'hour'},
																		text: BX.message('JS_CRMSOFT_IMC_SET_TIMER_POPUP_INTERVAL_TYPE_HOUR')
																	}),
																	BX.create('option', {
																		attrs: {value: 'day'},
																		text: BX.message('JS_CRMSOFT_IMC_SET_TIMER_POPUP_INTERVAL_TYPE_DAY')
																	})
																]
															})
														]
													})
												]
											}),
										]
									})
								]
							}),


							BX.create('div', {
								attrs: {
									className: 'imc-timer-popup_row'
								},
								children: [
									BX.create('label', {
										props: {
											className: 'imc-timer-popup_label'
										},
										text: 'Удалять сообщения старше (кол-во дней)'
									}),
									BX.create('div',{
										attrs: {className: 'ui-ctl ui-ctl-textbox ui-ctl-inline'},
										children:[
											BX.create('input', {
												attrs: {
													type: 'text',
													id: 'imc-timer-intervalvalue-' + id,
													name: 'imcTimerDeleteDaysValue',
													value: '',
													className: 'ui-ctl-element'
												},
												events: {
													change: function(e) {
														e.target.value = e.target.value.replace(/[^\d]/g, '');
													},
													input: function(e) {
														e.target.value = e.target.value.replace(/[^\d]/g, '');
													}
												}
											})
										]
									}),
								]
							}),
						]
					}),
					buttons: [
						new BX.PopupWindowButton({
							text: BX.message('JS_CRMSOFT_IMC_SET_TIMER_SAVE'),
							className: "ui-btn ui-btn-primary",
							events: {
								click: function () {
									var buttonNode = this.buttonNode;
									buttonNode.classList.add('ui-btn-clock');

									let form = document.querySelector('#imc-timer-form-' + id);
									let formData = new FormData(form);
									formData.append('chatId', id);

									BX.ajax.runAction('crmsoft:imclear.api.Message.setTimer', {
										data: formData
									}).then(
										function (response) {
											buttonNode.classList.remove('ui-btn-clock');
											timerPopup.close();
											this.reloadGrid();
										}.bind(self),
										function (response) {
											buttonNode.classList.remove('ui-btn-clock');
											timerPopup.close();
											var alertPopup = this.getAlertPopup();
											var content = '<div class="ui-alert ui-alert-danger"><span class="ui-alert-message">';
											var errors = [];
											for (var j = 0; j < response.errors.length; j++) {
												errors.push(response.errors[j].message);
											}
											content += errors.join('<br />');
											content += '</span></div>';
											alertPopup.setContent(content);
											alertPopup.show();
										}.bind(self));
								}
							}
						}),
						new BX.PopupWindowButton({
							text: BX.message('JS_CRMSOFT_IMC_SET_TIMER_CANCEL'),
							className: "ui-btn ui-btn-link",
							events: {
								click: function () {
									this.popupWindow.close();
								}
							}
						})
					]
				}
			);
			timerPopup.show();
		},
		changeTimerType: function(e) {
			document.querySelectorAll('.imc-timer-popup_type-content').forEach(function (el) {
				el.classList.remove('imc-timer-popup_type-content__selected');
			});
			console.log(e);
			if(e.target.value === 'datetime') {
				document.querySelector('#imc-timer-content-datetime-' + e.target.dataset.itemid).classList.add('imc-timer-popup_type-content__selected');
			} else if(e.target.value === 'interval') {
				document.querySelector('#imc-timer-content-interval-' + e.target.dataset.itemid).classList.add('imc-timer-popup_type-content__selected');
			}
		},
		clearTimer: function(id) {
			let self = this;
			BX.ajax.runAction('crmsoft:imclear.api.Message.clearTimer', {
				data: {
					id: id
				}
			}).then(
				function (response) {
					this.reloadGrid();
					this.confirmPopup.close();
				}.bind(self),
				function (response) {
					this.confirmPopup.close();
					var alertPopup = this.getAlertPopup();
					var content = '<div class="ui-alert ui-alert-danger"><span class="ui-alert-message">';
					var errors = [];
					for (var j = 0; j < response.errors.length; j++) {
						errors.push(response.errors[j].message);
					}
					content += errors.join('<br />');
					content += '</span></div>';
					alertPopup.setContent(content);
					alertPopup.show();
				}.bind(self)
			);
		},
		changeTimer: function(id, chatId) {
			var self = this;
			var currentDate = new Date();
			var timerPopup = BX.PopupWindowManager.create(
				"popup-timer-message-" + id,
				null,
				{
					closeIcon: false,
					lightShadow: true,
					closeByEsc: true,
					titleBar: BX.message('JS_CRMSOFT_IMC_SET_TIMER_POPUP'),
					content: BX.create('form', {
						attrs: {
							id: 'imc-timer-form-' + id
						},
						children: [
							BX.create('div', {
								props: {
									className: 'imc-timer-popup_row'
								},
								children: [
									BX.create('div', {
										attrs: {
											id: 'imc-timer-content-interval-' + id
										},
										props: {
											className: 'imc-timer-popup_type-content-show'
										},
										children: [
											BX.create('div', {
												attrs: {
													className: 'imc-timer-popup_row'
												},
												children: [
													BX.create('label', {
														props: {
															className: 'imc-timer-popup_label'
														},
														text: 'С какой периодичностью запускать очистку'
													}),
													BX.create('div',{
														attrs: {className: 'ui-ctl ui-ctl-textbox ui-ctl-inline'},
														children:[
															BX.create('input', {
																attrs: {
																	type: 'text',
																	id: 'imc-timer-intervalvalue-' + id,
																	name: 'imcTimerIntervalValue',
																	value: '',
																	className: 'ui-ctl-element'
																},
																events: {
																	change: function(e) {
																		e.target.value = e.target.value.replace(/[^\d]/g, '');
																	},
																	input: function(e) {
																		e.target.value = e.target.value.replace(/[^\d]/g, '');
																	}
																}
															})
														]
													})
												]
											}),
											BX.create('div', {
												attrs: {
													className: 'imc-timer-popup_row'
												},
												children: [
													BX.create('label', {
														props: {
															className: 'imc-timer-popup_label'
														},
														text: BX.message('JS_CRMSOFT_IMC_SET_TIMER_POPUP_INTERVAL_TYPE_LABEL')
													}),
													BX.create('div', {
														attrs: {className: 'ui-ctl ui-ctl-after-icon ui-ctl-dropdown ui-ctl-inline'},
														children: [
															BX.create('div', {attrs:{className: 'ui-ctl-after ui-ctl-icon-angle'}}),
															BX.create('select', {
																attrs: {
																	id: 'imc-timer-intervaltype-' + id,
																	className: 'ui-ctl-element',
																	name: 'imcTimerIntervalType'
																},
																children: [
																	BX.create('option', {
																		attrs: {value: ''},
																		text: ''
																	}),
																	BX.create('option', {
																		attrs: {value: 'minute'},
																		text: BX.message('JS_CRMSOFT_IMC_SET_TIMER_POPUP_INTERVAL_TYPE_MINUTE')
																	}),
																	BX.create('option', {
																		attrs: {value: 'hour'},
																		text: BX.message('JS_CRMSOFT_IMC_SET_TIMER_POPUP_INTERVAL_TYPE_HOUR')
																	}),
																	BX.create('option', {
																		attrs: {value: 'day'},
																		text: BX.message('JS_CRMsoft_IMC_SET_TIMER_POPUP_INTERVAL_TYPE_DAY')
																	})
																]
															})
														]
													})
												]
											}),
										]
									})
								]
							}),


							BX.create('div', {
								attrs: {
									className: 'imc-timer-popup_row'
								},
								children: [
									BX.create('label', {
										props: {
											className: 'imc-timer-popup_label'
										},
										text: 'Удалять сообщения старше (кол-во дней)'
									}),
									BX.create('div',{
										attrs: {className: 'ui-ctl ui-ctl-textbox ui-ctl-inline'},
										children:[
											BX.create('input', {
												attrs: {
													type: 'text',
													id: 'imc-timer-intervalvalue-' + id,
													name: 'imcTimerDeleteDaysValue',
													value: '',
													className: 'ui-ctl-element'
												},
												events: {
													change: function(e) {
														e.target.value = e.target.value.replace(/[^\d]/g, '');
													},
													input: function(e) {
														e.target.value = e.target.value.replace(/[^\d]/g, '');
													}
												}
											})
										]
									}),
								]
							}),
						]
					}),
					buttons: [
						new BX.PopupWindowButton({
							text: BX.message('JS_CRMSOFT_IMC_SET_TIMER_SAVE'),
							className: "ui-btn ui-btn-primary",
							events: {
								click: function () {
									var buttonNode = this.buttonNode;
									buttonNode.classList.add('ui-btn-clock');

									let form = document.querySelector('#imc-timer-form-' + id);
									let formData = new FormData(form);
									formData.append('agentId', id);
									formData.append('chatId', chatId);

									BX.ajax.runAction('crmsoft:imclear.api.Message.changeTimer', {
										data: formData
									}).then(
										function (response) {
											buttonNode.classList.remove('ui-btn-clock');
											timerPopup.close();
											this.reloadGrid();
										}.bind(self),
										function (response) {
											buttonNode.classList.remove('ui-btn-clock');
											timerPopup.close();
											var alertPopup = this.getAlertPopup();
											var content = '<div class="ui-alert ui-alert-danger"><span class="ui-alert-message">';
											var errors = [];
											for (var j = 0; j < response.errors.length; j++) {
												errors.push(response.errors[j].message);
											}
											content += errors.join('<br />');
											content += '</span></div>';
											alertPopup.setContent(content);
											alertPopup.show();
										}.bind(self));
								}
							}
						}),
						new BX.PopupWindowButton({
							text: BX.message('JS_CRMSOFT_IMC_SET_TIMER_CANCEL'),
							className: "ui-btn ui-btn-link",
							events: {
								click: function () {
									this.popupWindow.close();
								}
							}
						})
					]
				}
			);
			timerPopup.show();

			// BX.ajax.runAction('crmsoft:imclear.api.Message.changeTimer', {
			// 	data: {
			// 		id: id
			// 	}
			// }).then(
			// 	function (response) {
			// 		this.reloadGrid();
			// 		this.confirmPopup.close();
			// 	}.bind(self),
			// 	function (response) {
			// 		this.confirmPopup.close();
			// 		var alertPopup = this.getAlertPopup();
			// 		var content = '<div class="ui-alert ui-alert-danger"><span class="ui-alert-message">';
			// 		var errors = [];
			// 		for (var j = 0; j < response.errors.length; j++) {
			// 			errors.push(response.errors[j].message);
			// 		}
			// 		content += errors.join('<br />');
			// 		content += '</span></div>';
			// 		alertPopup.setContent(content);
			// 		alertPopup.show();
			// 	}.bind(self)
			// );
		},
	};
})();