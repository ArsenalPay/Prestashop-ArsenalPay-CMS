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
							<label for="token">Токен</label>
							<span class="annotation">Уникальный токен, который присваивается ТСП для работы с фреймом, обязательный.</span>
						</td>
						<td>
							<input type="text" name="arsenalpay_token" value="'.htmlentities(Tools::getValue('arsenalpay_token', $this->am_config['arsenalpay_token']), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" />
						</td>
					</tr>
                    <tr>
						<td valign="top" width="50%">
							<label for="token">Другой код</label>
							<span class="annotation">дополнительный номер или код, необходимый для оплаты. Недоступен для редактирования абоненту и не отображается в случае, если задан, необязательный.</span>
						</td>
						<td>
							<input type="text" name="arsenalpay_other_code" value="'.htmlentities(Tools::getValue('arsenalpay_other_code', $this->am_config['arsenalpay_other_code']), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" />
						</td>
					</tr>
                    <tr>
						<td valign="top" width="50%">
							<label for="token">Пароль для проверки</label>
							<span class="annotation">Ключ (key) для проверки подписи запросов, обязательный</span>
						</td>
						<td>
							<input type="text" name="arsenalpay_key" value="'.htmlentities(Tools::getValue('arsenalpay_key', $this->am_config['arsenalpay_key']), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" />
						</td>
					</tr>
                    <tr>
						<td valign="top" width="50%">
							<label for="token">Параметр css</label>
							<span class="annotation">адрес (URL) CSS файла, необязательный.</span>
						</td>
						<td>
							<input type="text" name="arsenalpay_css" value="'.htmlentities(Tools::getValue('arsenalpay_css', $this->am_config['arsenalpay_css']), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" />
						</td>
					</tr>
                    <tr>
						<td valign="top" width="50%">
							<label for="token">IP-адрес</label>
							<span class="annotation">IP-адрес, с которого возможен запрос от АМ, обязательный</span>
						</td>
						<td>
							<input type="text" name="arsenalpay_ip_adress" value="'.htmlentities(Tools::getValue('arsenalpay_ip_adress', $this->am_config['arsenalpay_ip_adress']), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" />
						</td>
					</tr>
                    <tr>
						<td valign="top" width="50%">
							<label for="token">Url колбэка</label>
							<span class="annotation">УРЛ колбэка платежа, обязательный</span>
						</td>
						<td>
							<input type="text" name="arsenalpay_callback_url" value="'.htmlentities(Tools::getValue('arsenalpay_callback_url', $this->am_config['arsenalpay_callback_url']), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" />
						</td>
					</tr>
                    <tr>
						<td valign="top" width="50%">
							<label for="token">Url проверки номера получателя</label>
							<span class="annotation">УРЛ проверки номера получателя, необязательный</span>
						</td>
						<td>
							<input type="text" name="arsenalpay_check_url" value="'.htmlentities(Tools::getValue('arsenalpay_check_url', $this->am_config['arsenalpay_check_url']), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" />
						</td>
					</tr>
                    <tr>
						<td valign="top" width="50%">
							<label for="token">Параметр src</label>
							<span class="annotation">Тип платежа. Возможные варианты: «mk» - оплата с мобильного телефона (мобильная коммерция), «card» - оплата с пластиковой карты (интернет эквайринг), необязательный.</span>
						</td>
						<td>
							<input type="text" name="arsenalpay_srcc" value="'.htmlentities(Tools::getValue('arsenalpay_srcc', $this->am_config['arsenalpay_srcc']), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" />
						</td>
					</tr>
                    <tr>
						<td valign="top" width="50%">
							<label for="token">Url фрейма</label>
							<span class="annotation">Адрес фрейма, обязательный</span>
						</td>
						<td>
							<input type="text" name="arsenalpay_frame_url" value="'.htmlentities(Tools::getValue('arsenalpay_frame_url', $this->am_config['arsenalpay_frame_url']), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" />
						</td>
					</tr>
                    <tr>                          
                        <td valign="top" width="50%">
							<label for="token">Режим отображения фрейма</label>
							<span class="annotation">"1"-отображать во фрейме, иначе на всю страницу.</span>
						</td>
						<td>
							<input type="text" name="arsenalpay_frame_mode" value="'.htmlentities(Tools::getValue('arsenalpay_frame_mode', $this->am_config['arsenalpay_frame_mode']), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" />
						</td>
					</tr>
                    <tr>
						<td valign="top" width="50%">
							<label for="token">Дополнительные параметры фрейма</label>
							<span class="annotation">Параметры фрейма</span>
						</td>
						<td>
							<input type="text" name="arsenalpay_frame_params" value="'.htmlentities(Tools::getValue('arsenalpay_frame_params', $this->am_config['arsenalpay_frame_params']), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" />
						</td>
					</tr>
                    
					<tr><td colspan="2" align="center"><br /><input class="button" name="btnSubmit" value="'.$this->l('Update settings').'" type="submit" /></td></tr>
                </table>
            </fieldset>
        </form>';