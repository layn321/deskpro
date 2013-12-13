<?php if ($is_widget): ?>
	<br/><br/>
	<a href="<?php echo $billing_url ?>" class="btn">Go to the billing interface &rarr;</a>
<?php else: ?>
	<form action="<?php echo $billing_url ?>login/authenticate-password" method="POST">
		<div style="margin-top: 45px;  background-color: #FFF; border: 1px solid #E8E8E8; padding: 8px; border-radius: 6px; -webkit-border-radius: 6px;">
			<strong>Log in now to add your billing information.</strong><br/><br/>
			<table cellspacing="0" cellpadding="5" border="0">
				<tr>
					<td style="vertical-align:middle; text-align: right;">Email Address:</td>
					<td style="vertical-align:middle;">
						<input type="text" name="email" style="font-family: sans-serif; line-height: 100%; padding: 5px; border-radius: 3px; border: 1px solid #aaa; width: 350px" placeholder="Enter your admin email address" />
					</td>
				</tr>
				<tr>
					<td style="vertical-align:middle; text-align: right;">Password:</td>
					<td style="vertical-align:middle;">
						<input type="password" name="password" style="font-family: sans-serif; line-height: 100%; padding: 5px; border-radius: 3px; border: 1px solid #aaa; width: 350px" placeholder="Enter your admin password" />
						&nbsp;&nbsp;
						<a href="<?php echo $billing_url ?>login?lost">Lost password?</a>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td style="vertical-align:middle;">
						<button class="btn">Go to billing &rarr;</button>
					</td>
				</tr>
			</table>
		</div>
	</form>
<?php endif ?>