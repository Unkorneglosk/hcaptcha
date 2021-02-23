<?php
/**
 * hCaptcha Validation
 *
 * This plugin adds hCaptcha validation to signups.
 *
 * Changes:
 *  0.1        Development
 *
 * @author Loick Diroll <loick.diroll@losk.fr>
 * @package vanilla
 * Based on Vanilla's ReCaptcha plugin
 */
class hCaptchaPlugin extends Gdn_Plugin {

    /**
     * hCaptcha secret key
     * @var string
     */
    protected $secretKey;

    /**
     * hCaptcha site key
     * @var string
     */
    protected $siteKey;

    /**
     * Plugin initialization.
     *
     */
    public function __construct() {
        parent::__construct();

        // Get keys from config
        $this->secretKey = c('hCaptcha.SecretKey');
        $this->siteKey = c('hCaptcha.SiteKey');
    }

    /**
     * Override private key in memory.
     *
     * @param string $key
     */
    public function setSecretKey($key) {
        $this->secretKey = $key;
    }

    /**
     * Get private key from memory.
     *
     * @return string
     */
    public function getSecretKey() {
        return $this->secretKey;
    }

    /**
     * Override public key in memory.
     *
     * @param string $key
     */
    public function setSiteKey($key) {
        $this->siteKey = $key;
    }

    /**
     * Get public key from memory.
     *
     * @return string
     */
    public function getSiteKey() {
        return $this->siteKey;
    }

    /**
     * Validate a hCpatcha submission.
     *
     * @param string $captchaText
     * @return boolean
     * @throws Exception
     */
    public function validateCaptcha($captchaText) {
        $api = new Garden\Http\HttpClient('https://hcaptcha.com');
        $data = [
            'secret' => $this->getSecretKey(),
            'response' => $captchaText
        ];
        $response = $api->get('/siteverify', $data);

        if ($response->isSuccessful()) {
            $result = $response->getBody();
            $errorCodes = val('error_codes', $result);
            if ($result && val('success', $result)) {
                return true;
            } else if (!empty($errorCodes) && $errorCodes != ['invalid-input-response']) {
                throw new Exception(formatString(t('No response from hCaptcha.').' {ErrorCodes}', ['ErrorCodes' => join(', ', $errorCodes)]));
            }
        } else {
            throw new Exception(t('No response from hCaptcha.'));
        }

        return false;
    }

    /**
     * Hook (controller) to manage captcha config.
     *
     * @param SettingsController $sender
     */
    public function settingsController_registration_handler($sender) {
        $configurationModel = $sender->EventArguments['Configuration'];

        $manageCaptcha = c('Garden.Registration.ManageCaptcha', true);
        $sender->setData('_ManageCaptcha', $manageCaptcha);

        if ($manageCaptcha) {
            $configurationModel->setField('hCaptcha.SecretKey');
            $configurationModel->setField('hCaptcha.SiteKey');
            $configurationModel->setField('hCaptcha.Theme');
            $configurationModel->setField('hCaptcha.Size');
        }
    }

    /**
     * Hook to indicate a captcha service is available.
     *
     * @param Gdn_PluginManager $sender
     * @param array $args
     */
    public function captcha_isEnabled_handler($sender, $args) {
        $args['Enabled'] = true;
    }

    /**
     * Hook (view) to manage captcha config.
     *
     * THIS METHOD ECHOS DATA
     *
     * @param SettingsController $sender
     */
    public function captcha_settings_handler($sender) {
        echo $sender->fetchView('registration', 'settings', 'plugins/hcaptcha');
    }

    /**
     * Hook (view) to render a captcha.
     *
     * THIS METHOD ECHOS DATA
     *
     * @param Gdn_Controller $sender
     */
    public function captcha_render_handler($sender) {
        echo $sender->fetchView('captcha', 'display', 'plugins/hcaptcha');
    }

    /**
     * Hook to validate captchas.
     *
     * @param Gdn_PluginManager $sender
     * @return boolean
     * @throws Exception
     */
    public function captcha_validate_handler($sender) {
        $valid = &$sender->EventArguments['captchavalid'];

        $hcaptchaResponse = Gdn::request()->post('h-captcha-response');
        if (!$hcaptchaResponse) {
            return $valid = false;
        }

        return $valid = $this->validateCaptcha($hcaptchaResponse);
    }

    /**
     * Hook to return captcha submission data.
     *
     * @param Gdn_PluginManager $sender
     */
    public function captcha_get_handler($sender) {
        $hcaptchaResponse = Gdn::request()->post('h-captcha-response');
        if ($hcaptchaResponse) {
            $sender->EventArguments['captchatext'] = $hcaptchaResponse;
        }
    }

    /**
     * Display hCaptcha entry field.
     *
     * THIS METHOD ECHOS DATA
     *
     * @param Gdn_Form $sender
     * @return string
     */
    public function gdn_form_captcha_handler($sender) {
        if (!$this->getSecretKey() || !$this->getSiteKey()) {
            echo '<div class="Warning">' . t('hCaptcha has not been set up by the site administrator in registration settings. This is required to register.') .  '</div>';
        }

        //Language whitelist based off https://docs.hcaptcha.com/languages docs
        $whitelist = ['ar', 'af', 'am', 'hy', 'az', 'eu', 'bn', 'bg', 'ca', 'zh-HK', 'zh-CN', 'zh-TW', 'hr', 'cs', 'da', 'nl', 'eb-GB', 'en', 'et', 'fil', 'fi', 'fr', 'fr-CA', 'gl', 'ka', 'de', 'de-AT', 'de-CH', 'el', 'gu', 'iw', 'hi', 'hu', 'is', 'id', 'it', 'ja', 'kn', 'ko', 'lo', 'lv', 'lt', 'ms', 'ml', 'mr', 'mn', 'no', 'fa', 'pl', 'pt', 'pt-BR', 'ro', 'ru', 'sr', 'si', 'sk', 'sl', 'es', 'es-149', 'sw', 'sv', 'ta', 'te', 'th', 'tr', 'uk', 'ur', 'vi', 'zu'];

        // Use our current locale against the whitelist.
        $language = Gdn::locale()->language();
        if (!in_array($language, $whitelist)) {
            $language = (in_array(Gdn::locale()->Locale, $whitelist)) ? Gdn::locale()->Locale : false;
        }
        
		// Build script source.
        $scriptUrl = 'https://hcaptcha.com/1/api.js';
        $scriptParams = [
            'hl' => $language,
           'render' => 'explicit'
        ];
        $scriptSource = $scriptUrl . '?' . http_build_query($scriptParams);

        $attributes = [
            'class' => 'h-captcha',
            'id' => 'hCaptchaContainer',
            'data-sitekey' => $this->getSiteKey(),
            'data-theme' => Gdn::config('hCaptcha.Theme', 'light'),
			'data-size' => Gdn::config('hCaptcha.Size', 'normal'),
        ];
        $this->EventArguments['Attributes'] = &$attributes;
        $this->fireEvent('BeforeCaptcha');

        echo '<div '.attribute($attributes).'></div>',
            '<script>
                var hCaptchaHelper = function() {
                    var widgetID = hcaptcha.render("hCaptchaContainer");
                };
            </script>',
            '<script src="'.$scriptSource.'"></script>',
            '<script>(function($) { hCaptchaHelper(); }(window.jQuery));</script>';
    }

    /**
     * On plugin enable.
     *
     */
    public function setup() {}

}
