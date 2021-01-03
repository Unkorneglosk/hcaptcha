<div id="CaptchaSettings">
	<section>
	    <h2 class="subheading"><?php echo t('Captcha Settings'); ?></h2>
	    <div class="alert alert-warning padded"><?php echo t('The basic registration form requires new users to copy text from a CAPTCHA image.', '<strong>The basic registration form requires</strong> new users to use hCaptcha to keep spammers out of the site. You need an account at <a href="https://www.hcaptcha.com/">https://www.hcaptcha.com/</a>. Signing up is FREE and easy. Once you have signed up, come back here and enter the following settings:'); ?></div>
	</section>
	<div class="table-wrap">
        <table class="table-data js-tj">
            <thead>
            <tr>
                <th><?php echo t('Key Type'); ?></th>
                <th class="column-xl"><?php echo t('Key Value'); ?></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th><?php echo t('Site Key'); ?></th>
                <td class="Alt"><?php echo $this->Form->textBox('hCaptcha.SiteKey'); ?></td>
            </tr>
            <tr>
                <th><?php echo t('Secret Key'); ?></th>
                <td class="Alt"><?php echo $this->Form->textBox('hCaptcha.SecretKey'); ?></td>
            </tr>
		    <tr>
                <th><?php echo t('Captcha Theme'); ?></th>
                <td class="Alt"><?php 
			    $Options = ['light' => t('Light'), 'dark' => t('Dark')];
			    $Fields = ['TextField' => 'Text', 'ValueField' => 'Code'];
			    echo $this->Form->dropDown('hCaptcha.Theme', $Options, $Fields); ?></td>
            </tr>
		    <tr>
                <th><?php echo t('Captcha Size'); ?></th>
                <td class="Alt"><?php 
			    $Options = ['normal' => t('Default'), 'compact' => t('Compact')];
			    $Fields = ['TextField' => 'Text', 'ValueField' => 'Code'];				
			    echo $this->Form->dropDown('hCaptcha.Size', $Options, $Fields); ?></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
