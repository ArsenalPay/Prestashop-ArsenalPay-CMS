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
							<label for="token">�����</label>
							<span class="annotation">���������� �����, ������� ������������� ��� ��� ������ � �������, ������������.</span>
						</td>
						<td>
							<input type="text" name="arsenalpay_token" value="'.htmlentities(Tools::getValue('arsenalpay_token', $this->am_config['arsenalpay_token']), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" />
						</td>
					</tr>
                    <tr>
						<td valign="top" width="50%">
							<label for="token">������ ���</label>
							<span class="annotation">�������������� ����� ��� ���, ����������� ��� ������. ���������� ��� �������������� �������� � �� ������������ � ������, ���� �����, ��������������.</span>
						</td>
						<td>
							<input type="text" name="arsenalpay_other_code" value="'.htmlentities(Tools::getValue('arsenalpay_other_code', $this->am_config['arsenalpay_other_code']), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" />
						</td>
					</tr>
                    <tr>
						<td valign="top" width="50%">
							<label for="token">������ ��� ��������</label>
							<span class="annotation">���� (key) ��� �������� ������� ��������, ������������</span>
						</td>
						<td>
							<input type="text" name="arsenalpay_key" value="'.htmlentities(Tools::getValue('arsenalpay_key', $this->am_config['arsenalpay_key']), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" />
						</td>
					</tr>
                    <tr>
						<td valign="top" width="50%">
							<label for="token">�������� css</label>
							<span class="annotation">����� (URL) CSS �����, ��������������.</span>
						</td>
						<td>
							<input type="text" name="arsenalpay_css" value="'.htmlentities(Tools::getValue('arsenalpay_css', $this->am_config['arsenalpay_css']), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" />
						</td>
					</tr>
                    <tr>
						<td valign="top" width="50%">
							<label for="token">IP-�����</label>
							<span class="annotation">IP-�����, � �������� �������� ������ �� ��, ������������</span>
						</td>
						<td>
							<input type="text" name="arsenalpay_ip_adress" value="'.htmlentities(Tools::getValue('arsenalpay_ip_adress', $this->am_config['arsenalpay_ip_adress']), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" />
						</td>
					</tr>
                    <tr>
						<td valign="top" width="50%">
							<label for="token">Url �������</label>
							<span class="annotation">��� ������� �������, ������������</span>
						</td>
						<td>
							<input type="text" name="arsenalpay_callback_url" value="'.htmlentities(Tools::getValue('arsenalpay_callback_url', $this->am_config['arsenalpay_callback_url']), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" />
						</td>
					</tr>
                    <tr>
						<td valign="top" width="50%">
							<label for="token">Url �������� ������ ����������</label>
							<span class="annotation">��� �������� ������ ����������, ��������������</span>
						</td>
						<td>
							<input type="text" name="arsenalpay_check_url" value="'.htmlentities(Tools::getValue('arsenalpay_check_url', $this->am_config['arsenalpay_check_url']), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" />
						</td>
					</tr>
                    <tr>
						<td valign="top" width="50%">
							<label for="token">�������� src</label>
							<span class="annotation">��� �������. ��������� ��������: �mk� - ������ � ���������� �������� (��������� ���������), �card� - ������ � ����������� ����� (�������� ���������), ��������������.</span>
						</td>
						<td>
							<input type="text" name="arsenalpay_srcc" value="'.htmlentities(Tools::getValue('arsenalpay_srcc', $this->am_config['arsenalpay_srcc']), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" />
						</td>
					</tr>
                    <tr>
						<td valign="top" width="50%">
							<label for="token">Url ������</label>
							<span class="annotation">����� ������, ������������</span>
						</td>
						<td>
							<input type="text" name="arsenalpay_frame_url" value="'.htmlentities(Tools::getValue('arsenalpay_frame_url', $this->am_config['arsenalpay_frame_url']), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" />
						</td>
					</tr>
                    <tr>                          
                        <td valign="top" width="50%">
							<label for="token">����� ����������� ������</label>
							<span class="annotation">"1"-���������� �� ������, ����� �� ��� ��������.</span>
						</td>
						<td>
							<input type="text" name="arsenalpay_frame_mode" value="'.htmlentities(Tools::getValue('arsenalpay_frame_mode', $this->am_config['arsenalpay_frame_mode']), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" />
						</td>
					</tr>
                    <tr>
						<td valign="top" width="50%">
							<label for="token">�������������� ��������� ������</label>
							<span class="annotation">��������� ������</span>
						</td>
						<td>
							<input type="text" name="arsenalpay_frame_params" value="'.htmlentities(Tools::getValue('arsenalpay_frame_params', $this->am_config['arsenalpay_frame_params']), ENT_COMPAT, 'UTF-8').'" style="width: 300px;" />
						</td>
					</tr>
                    
					<tr><td colspan="2" align="center"><br /><input class="button" name="btnSubmit" value="'.$this->l('Update settings').'" type="submit" /></td></tr>
                </table>
            </fieldset>
        </form>';