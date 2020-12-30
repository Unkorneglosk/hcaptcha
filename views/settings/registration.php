<div id="CaptchaSettings">
    <div class="alert alert-warning padded"><?php echo t('The basic registration form requires new users to copy text from a CAPTCHA image.', '<strong>The basic registration form requires</strong> new users to use hCaptcha to keep spammers out of the site. You need an account at <a href="https://www.hcaptcha.com/">https://www.hcaptcha.com/</a>. Signing up is FREE and easy. Once you have signed up, come back here and enter the following settings:'); ?></div>
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
                <th><?php echo t('Public Key'); ?></th>
                <td class="Alt"><?php echo $this->Form->textBox('hCaptcha.PublicKey'); ?></td>
            </tr>
            <tr>
                <th><?php echo t('Private Key'); ?></th>
                <td class="Alt"><?php echo $this->Form->textBox('hCaptcha.PrivateKey'); ?></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
