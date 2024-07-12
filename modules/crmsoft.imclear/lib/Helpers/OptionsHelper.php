<?php

namespace CRMsoft\ImClear\Helpers;

class OptionsHelper
{
    public const MODULE_ID = 'crmsoft.imclear';

    public static function drawRightsControl($Option, $showTitle = true)
    {
        \CJSCore::Init(array("access"));
        $arToAllRights = unserialize($Option[2], ['allowed_classes' => true]);

        $access = new \CAccess();
        $arNames = $access->GetNames($arToAllRights);

        ?>
        <tr id="RIGHTS_all" style="display: table-row">
        <?if($showTitle){?><td><?=$Option[1]?></td><?}?>
        <td><?php
            ?><script>

				var rightsCont = BX('RIGHTS_all');

				function DeleteToAllAccessRow(ob)
				{
					var divNode = BX('RIGHTS_div', true);
					var div = BX.findParent(ob, {tag: 'div', className: 'toall-right'}, divNode);
					if (div)
						var right = div.getAttribute('data-bx-right');

					if (div && right)
					{
						BX.remove(div);
						var artoAllRightsNew = [];

						for(var i = 0; i < arToAllRights.length; i++)
							if (arToAllRights[i] != right)
								artoAllRightsNew[artoAllRightsNew.length] = arToAllRights[i];

						arToAllRights = BX.clone(artoAllRightsNew);

						var hidden_el = BX('<?=htmlspecialcharsbx($Option[0])?>_' + right);
						if (hidden_el)
							BX.remove(hidden_el);
					}
					return false;
				}

				function ShowToAllAccessPopup(val)
				{
					val = val || [];

					BX.Access.Init({
						other: {
							disabled: false,
							disabled_g2: true,
							disabled_cr: true
						},
						groups: { disabled: true },
						socnetgroups: { disabled: true },
						extranet: { disabled: true }
					});

					var startValue = {};
					for(var i = 0; i < val.length; i++)
						startValue[val[i]] = true;

					BX.Access.SetSelected(startValue);

					BX.Access.ShowForm({
						callback: function(arRights)
						{
							var divNode = BX('RIGHTS_div', true);
							var pr = false;

							for(var provider in arRights)
							{
								pr = BX.Access.GetProviderName(provider);
								for(var right in arRights[provider])
								{
									divNode.appendChild(BX.create('div', {
										attrs: {
											'data-bx-right': right
										},
										props: {
											'className': 'toall-right'
										},
										children: [
											BX.create('span', {
												html: (pr.length > 0 ? pr + ': ' : '') + arRights[provider][right].name + '&nbsp;'
											}),
											BX.create('a', {
												attrs: {
													href: 'javascript:void(0);',
													title: BX.message('SLToAllDel')
												},
												props: {
													'className': 'access-delete'
												},
												events: {
													click: function() { DeleteToAllAccessRow(this); }
												}
											})
										]
									}));

									divNode.appendChild(BX.create('input', {
										attrs: {
											'type': 'hidden'
										},
										props: {
											'name': '<?=htmlspecialcharsbx($Option[0])?>[]',
											'id': '<?=htmlspecialcharsbx($Option[0])?>_' + right,
											'value': right
										}
									}));

									arToAllRights[arToAllRights.length] = arRights[provider][right].id;
								}
							}
						}
					});

					return false;
				}
            </script><?

            ?><div id="RIGHTS_div"><?
                foreach($arToAllRights as $right)
                {
                    ?><input type="hidden" name="<?echo htmlspecialcharsbx($Option[0])?>[]" id="<?echo htmlspecialcharsbx($Option[0]."_".$right)?>" value="<?=htmlspecialcharsbx($right)?>"><?
                    ?><div data-bx-right="<?=htmlspecialcharsbx($right)?>" class="toall-right"><span><?=(!empty($arNames[$right]["provider"]) ? $arNames[$right]["provider"].": " : "").$arNames[$right]["name"]?>&nbsp;</span><a href="javascript:void(0);" onclick="DeleteToAllAccessRow(this);" class="access-delete" title="<?=GetMessage("SONET_LOG_TOALL_DEL")?>"></a></div><?
                }
                ?></div><?
            ?><script>
				var arToAllRights = <?=\CUtil::PhpToJSObject($arToAllRights)?>;
            </script><?

            ?><div style="padding-top: 5px;"><a href="javascript:void(0)" class="bx-action-href" onclick="ShowToAllAccessPopup(arToAllRights);"><?=GetMessage(self::MODULE_ID.'_RIGHTS_ADD_BTN')?></a></div>
        </td></tr><?
    }
}