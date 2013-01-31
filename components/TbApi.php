<?php
/**
 * TbApi class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2013-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */

Yii::import('bootstrap.helpers.TbHtml');

/**
 * Bootstrap API component.
 */
class TbApi extends CApplicationComponent
{
	// Bootstrap plugins
	const PLUGIN_AFFIX = 'affix';
	const PLUGIN_ALERT = 'alert';
	const PLUGIN_BUTTON = 'button';
	const PLUGIN_CAROUSEL = 'carousel';
	const PLUGIN_COLLAPSE = 'collapse';
	const PLUGIN_DROPDOWN = 'dropdown';
	const PLUGIN_MODAL = 'modal';
	const PLUGIN_POPOVER = 'popover';
	const PLUGIN_SCROLLSPY = 'scrollspy';
	const PLUGIN_TAB = 'tab';
	const PLUGIN_TOOLTIP = 'tooltip';
	const PLUGIN_TRANSITION = 'transition';
	const PLUGIN_TYPEAHEAD = 'typeahead';

    /**
     * @var bool whether we should copy the asset file or directory even if it is already published before.
     */
    public $forceCopyAssets = false;

    private $_assetsUrl;

    /**
     * Registers the Bootstrap CSS.
     */
    public function registerCoreCss()
    {
        $filename = YII_DEBUG ? 'bootstrap.css' : 'bootstrap.min.css';
        Yii::app()->clientScript->registerCssFile($this->getAssetsUrl() . '/css/' . $filename);
    }

    /**
     * Registers the responsive Bootstrap CSS.
     */
    public function registerResponsiveCss()
    {
        /** @var CClientScript $cs */
        $cs = Yii::app()->getClientScript();
        $cs->registerMetaTag('width=device-width, initial-scale=1.0', 'viewport');
        $filename = YII_DEBUG ? 'bootstrap-responsive.css' : 'bootstrap-responsive.min.css';
        $cs->registerCssFile($this->getAssetsUrl() . '/css/' . $filename);
    }

    /**
     * Registers all Bootstrap CSS files.
     */
    public function registerAllCss()
    {
        $this->registerCoreCss();
        $this->registerResponsiveCss();
    }

    /**
     * Registers jQuery and Bootstrap JavaScript.
     * @param int $position the position of the JavaScript code.
     */
    public function registerCoreScripts($position = CClientScript::POS_END)
    {
        /** @var CClientScript $cs */
        $cs = Yii::app()->getClientScript();
        $cs->registerCoreScript('jquery');
        $filename = YII_DEBUG ? 'bootstrap.js' : 'bootstrap.min.js';
        $cs->registerScriptFile($this->getAssetsUrl() . '/js/' . $filename, $position);
    }

    /**
     * Registers the Tooltip and Popover plugins.
     */
    public function registerTooltipAndPopover()
    {
        $this->registerPopover();
        $this->registerTooltip();
    }

    /**
     * Registers all Bootstrap JavaScript files.
     */
    public function registerAllScripts()
    {
        $this->registerCoreScripts();
        $this->registerTooltipAndPopover();
    }

    /**
     * Registers all assets.
     */
    public function register()
    {
        $this->registerAllCss();
        $this->registerAllScripts();
    }

    /**
     * Registers the Bootstrap Popover plugin.
     * @param string $selector the CSS selector.
     * @param array $options the JavaScript options for the plugin.
     * @see http://twitter.github.com/bootstrap/javascript.html#popover
     */
    public function registerPopover($selector = 'body', $options = array())
    {
        if (!isset($options['selector']))
            $options['selector'] = 'a[rel=popover]';
        $this->registerPlugin(self::PLUGIN_POPOVER, $selector, $options);
    }

    /**
     * Registers the Bootstrap Tooltip plugin.
     * @param string $selector the CSS selector.
     * @param array $options the JavaScript options for the plugin.
     * @see http://twitter.github.com/bootstrap/javascript.html#tooltip
     */
    public function registerTooltip($selector = 'body', $options = array())
    {
        if (!isset($options['selector']))
            $options['selector'] = 'a[rel=tooltip]';
        $this->registerPlugin(self::PLUGIN_TOOLTIP, $selector, $options);
    }

    /**
     * Registers a specific Bootstrap plugin using the given selector and options.
     * @param string $name the plugin name.
     * @param string $selector the CSS selector.
     * @param array $options the JavaScript options for the plugin.
     * @param int $position the position of the JavaScript code.
     */
    public function registerPlugin($name, $selector, $options = array(), $position = CClientScript::POS_END)
    {
        if (isset($options['events']))
        {
            $this->registerEvents($selector, $options['events'], $position);
            unset($options['events']);
        }

        $options = !empty($options) ? CJavaScript::encode($options) : '';
        Yii::app()->clientScript->registerScript(
                $this->generateRandomId(), "jQuery('{$selector}').{$name}({$options});", $position);
    }

    /**
     * Registers events using the given selector.
     * @param string $selector the CSS selector.
     * @param array $events the JavaScript event configuration (name=>handler).
     * @param int $position the position of the JavaScript code.
     */
    public function registerEvents($selector, $events, $position = CClientScript::POS_END)
    {
        $script = '';
        foreach ($events as $name => $handler)
        {
            $handler = new CJavaScriptExpression($handler);
            $script .= "jQuery('{$selector}).on('{$name}', {$handler});'";
        }
        Yii::app()->clientScript->registerScript($this->generateRandomId(), $script, $position);
    }

    /**
     * Returns the url to the published assets folder.
     * @return string the url.
     */
    protected function getAssetsUrl()
    {
        if (isset($this->_assetsUrl))
            return $this->_assetsUrl;
        else
        {
            $assetsPath = Yii::getPathOfAlias('bootstrap.assets');
            $assetsUrl = Yii::app()->assetManager->publish($assetsPath, true, -1, $this->forceCopyAssets);
            return $this->_assetsUrl = $assetsUrl;
        }
    }

    /**
     * Generates a "somewhat" random id string.
     * @return string the id.
     */
    protected function generateRandomId()
    {
        return uniqid(__CLASS__ . '#', true);
    }
}