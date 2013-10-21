<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Module\Design;


class System{


    public function init()
    {
        $dispatcher = \Ip\ServiceLocator::getDispatcher();

        $dispatcher->bind('site.clearCache', array($this, 'clearCacheEvent'));

        $configModel = ConfigModel::instance();
        if ($configModel->isInPreviewState()) {
            $this->initConfig();
        }

        $lessCompiler = LessCompiler::instance();
        if (DEVELOPMENT_ENVIRONMENT) {
            if ($lessCompiler->shouldRebuild(THEME)) {
                $lessCompiler->rebuild(THEME);
            }
        }

        $dispatcher->bind('site.beforeError404', array($this, 'catchError404'));



    }


    public function catchError404(\Ip\Event $event)
    {
        if (parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) == parse_url(BASE_URL, PHP_URL_PATH) . THEME_DIR . THEME . '/ipAutogeneratedCss.css') {
            $event->addProcessed();
        }
    }


    protected function initConfig()
    {
        $site = \Ip\ServiceLocator::getSite();
        $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/jquery-ui/jquery-ui.js');
        $site->addCss(BASE_URL.LIBRARY_DIR.'css/bootstrap/bootstrap.css');
        $site->addJavascript(BASE_URL.LIBRARY_DIR.'css/bootstrap/bootstrap.js');
        $site->addCss(BASE_URL.LIBRARY_DIR.'fonts/font-awesome/font-awesome.css');
        $site->addJavascript(BASE_URL.INCLUDE_DIR.'Ip/Module/Design/public/optionsBox.js');
        $site->addJavascriptVariable('ipModuleDesignConfiguration', $this->getConfigurationBoxHtml());
        $site->addCss(BASE_URL.INCLUDE_DIR.'Ip/Module/Design/public/optionsBox.css');
        if (file_exists(BASE_DIR.THEME_DIR.THEME.'/'.Model::INSTALL_DIR.'Options.js')) {
            $site->addJavascript(BASE_URL.THEME_DIR.THEME.'/'.Model::INSTALL_DIR.'Options.js');
        }
        if (file_exists(BASE_DIR.THEME_DIR.THEME.'/'.Model::INSTALL_DIR.'options.js')) {
            $site->addJavascript(BASE_URL.THEME_DIR.THEME.'/'.Model::INSTALL_DIR.'options.js');
        }

        $model = Model::instance();
        $theme = $model->getTheme(THEME_DIR, THEME);
        if (!$theme) {
            throw new \Ip\CoreException("Theme doesn't exist");
        }

        $options = $theme->getOptionsAsArray();

        $fieldNames = array();
        foreach($options as $option) {
            if (empty($option['name'])) {
                continue;
            }
            $fieldNames[] = $option['name'];
        }
        $site->addJavascriptVariable('ipModuleDesignOptionNames', $fieldNames);
    }

    protected function getConfigurationBoxHtml()
    {
        $configModel = ConfigModel::instance();

        $form = $configModel->getThemeConfigForm(THEME);
        $form->removeClass('ipModuleForm');
        $variables = array(
            'form' => $form
        );
        $optionsBox = \Ip\View::create('view/optionsBox.php', $variables);
        return $optionsBox->render();
    }

    public function clearCacheEvent(\Ip\Event\ClearCache $e)
    {
        $lessCompiler = LessCompiler::instance();
        $lessCompiler->rebuild(THEME);
    }

}


