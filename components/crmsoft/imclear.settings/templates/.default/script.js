;(function () {
	BX.namespace('CRMsoft.ImClear.Detail');

	BX.CRMsoft.ImClear.Detail = {
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
		deleteMessage: function (id, messageOut) {
			messageOut = messageOut ? '<br /><blockquote>' + id + ': ' + messageOut + '</blockquote>' : '';
			let self = this;
			self.confirmPopup = BX.PopupWindowManager.create(
				"popup-message-" + id,
				null,
				{
					closeIcon: false,
					lightShadow: true,
					closeByEsc: true,
					content: '<div class="ui-alert ui-alert-danger ui-alert-icon-warning"><span class="ui-alert-message">' + BX.message('JS_CRMSOFT_IMC_DELETE_MESSAGE_APPROVE') + messageOut + '</span></div>',
					buttons: [
						new BX.PopupWindowButton({
							text: BX.message('JS_CRMSOFT_IMC_DELETE_MESSAGE'),
							className: "ui-btn ui-btn-danger",
							events: {
								click: function () {
									this.buttonNode.classList.add('ui-btn-clock');
									BX.ajax.runAction('crmsoft:imclear.api.Message.delete', {
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
	};
})();