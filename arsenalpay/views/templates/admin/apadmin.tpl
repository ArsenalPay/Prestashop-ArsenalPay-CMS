<style>
			input[type=text]
			{
				width: 300px;
			}
			span.annotation
			{
				display: block;
				position: relative;
				color: gray;
				font-size: 11px;
			}
			</style>
			<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
            <fieldset>
            <legend><img src="../img/admin/cog.gif" />'.$this->l('Configuration').'</legend>
                <table border="0" width="500" cellpadding="0" cellspacing="0" id="form">
					<tr>
						<td valign="top" width="50%">
							<label for="arsenalpay_widget_id">Widget ID</label>
							<span style="color:red"> *</span>
						</td>
						<td>
							<input type="text" name="arsenalpay_widget_id" value="'.htmlentities(Tools::getValue('arsenalpay_widget_id', $this->am_config['arsenalpay_widget_id']), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" />
						</td>
					</tr>
					<tr>
						<td valign="top" width="50%">
							<label for="arsenalpay_widget_key">Widget Key</label>
							<span style="color:red"> *</span>
						</td>
						<td>
							<input type="text" name="arsenalpay_widget_key" value="'.htmlentities(Tools::getValue('arsenalpay_widget_key', $this->am_config['arsenalpay_widget_key']), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" />
						</td>
					</tr>
					<tr>
						<td valign="top" width="50%">
							<label for="arsenalpay_callback_key">Callback Key</label>
							<span style="color:red"> *</span>
						</td>
						<td>
							<input type="text" name="arsenalpay_callback_key" value="'.htmlentities(Tools::getValue('arsenalpay_callback_key', $this->am_config['arsenalpay_callback_key']), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" />
						</td>
					</tr>
                    <tr>
						<td valign="top" width="50%">
							<label for="arsenalpay_ip_address">IP-адрес</label>
							<span class="annotation">IP-адрес, с которого возможен запрос от АМ, обязательный</span>
						</td>
						<td>
							<input type="text" name="arsenalpay_ip_address" value="'.htmlentities(Tools::getValue('arsenalpay_ip_address', $this->am_config['arsenalpay_ip_address']), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" />
						</td>
					</tr>
                    <tr>
						<td valign="top" width="50%">
							<label for="arsenalpay_callback_url">Url колбэка</label>
							<span class="annotation">УРЛ колбэка платежа, обязательный</span>
						</td>
						<td>
							<input type="text" name="arsenalpay_callback_url" value="'.htmlentities(Tools::getValue('arsenalpay_callback_url', $this->am_config['arsenalpay_callback_url']), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" />
						</td>
					</tr>

					<tr><td colspan="2" align="center"><br /><input class="button" name="btnSubmit" value="'.$this->l('Update settings').'" type="submit" /></td></tr>
                </table>
            </fieldset>
        </form>';