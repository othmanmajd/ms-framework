<?php

namespace MS\Core\View;

use MS\Core\Controller\CoreException;

/**
 * Class Render
 *
 * @package MS\Core\View
 */
class Render
{

    protected static ?Render $instance = null;
    private string $layout = "BE-Default";
    private string $template = "Index";
    private string $templateFolder = TEMPLATE_PATH;
    private string $templateType = '.php';
    private array $arguments = [];

    /**
     * Get instance of this Controller
     *
     * @return Render|null
     */
    public static function getInstance()
    {

        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * set arguments to Pass to View
     *
     * @param array $arguments
     */
    public function setArguments($arguments = [])
    {
        $this->arguments = $arguments;
    }

    /**
     * set arguments from Controller to Pass to View Template
     *
     * @return array|null
     */
    public function getArguments()
    {
        return !empty($this->arguments)
            ? $this->arguments
            : null;
    }

    /**
     * set arguments to Pass to View
     *
     * @param array $arguments
     */
    public function setArgument($arguments = [])
    {
        $this->arguments += $arguments;
    }

    /**
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @param string $layout
     */
    public function setLayout($layout)
    {
        if (!empty($layout)) {
            $this->layout = $layout;
        }
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @param string $template
     */
    public function setTemplate(string $template): void
    {
        $this->template = ucfirst($template);
    }

    /**
     * @return string
     */
    public function getTemplateFolder(): string
    {
        return $this->templateFolder;
    }

    /**
     * @param string $templateFolder
     */
    public function setTemplateFolder(string $templateFolder): void
    {
        $this->templateFolder = TEMPLATE_PATH . '/' . $templateFolder;
    }

    /**
     * @return string
     */
    public function getTemplateType(): string
    {
        return $this->templateType;
    }

    /**
     * @param string $templateType
     */
    public function setTemplateType(string $templateType): void
    {
        $this->templateType = $templateType;
    }


    /**
     * Render view with Arguments
     * @return bool
     */
    public function render(): bool
    {
        /** $arguments are the Parameters to Page -*/
        if (file_exists(LAYOUTS_PATH . '/' . $this->getLayout() . ".php")) {
            /**
             * New Impl with ob_get_contents
             */
            //  Include layout.
            ob_start();
            include_once LAYOUTS_PATH . '/' . $this->getLayout() . ".php";
            $layout = ob_get_contents();
            ob_end_clean();
            $template = 'No Template';
            if (file_exists($this->getTemplateFolder() . '/' . ucfirst($this->getTemplate()) . $this->getTemplateType())) {
                // Include template.
                ob_start();
                include_once $this->getTemplateFolder() . '/' . ucfirst($this->getTemplate()) . $this->getTemplateType();
                $template = ob_get_contents();
                ob_end_clean();
            } else {
                CoreException::writeError(
                    "Render",
                    "Template " . "Not Founded",
                    "1540465674",
                    __FILE__,
                    __LINE__
                );
            }
            echo str_replace('{{MS_BODY}}', $template, $layout);
            return true;
        } else {
            CoreException::writeError(
                "Render",
                "Layout " . "Not Founded",
                "1540465674",
                __FILE__,
                __LINE__
            );

            return false;
        }
    }

    public function getMsBeBody()
    {
        require_once $this->getTemplateFolder() . '/' . ucfirst($this->getTemplate()) . $this->getTemplateType();
    }

    public function renderHeadJs()
    {
        if (defined('MS_ENV') && !empty(MS_ENV['FE']['assets']['js']['head'])) {
            foreach (MS_ENV['FE']['assets']['js']['head'] as $js) {
                if (str_contains(strtolower($js), 'http:') || str_contains(strtolower($js), 'https:')) {
                    echo '<script src="' . $js . '" ></script>';
                } else {
                    echo '<script src="' . rtrim(BASE_URL, '/') . '/' . ltrim($js, '/') . '"></script>';
                }
            }
        }
    }

    public function renderFooterJs()
    {
        if (defined('MS_ENV') && !empty(MS_ENV['FE']['assets']['js']['footer'])) {
            foreach (MS_ENV['FE']['assets']['js']['footer'] as $js) {
                if (str_contains(strtolower($js), 'http:') || str_contains(strtolower($js), 'https:')) {
                    echo '<script src="' . $js . '" ></script>';
                } else {
                    echo '<script src="' . rtrim(BASE_URL, '/') . '/' . ltrim($js, '/') . '"></script>';
                }
            }
        }
    }

    public function renderHeadCss()
    {
        if (defined('MS_ENV') && !empty(MS_ENV['FE']['assets']['css'])) {
            foreach (MS_ENV['FE']['assets']['css'] as $css) {
                echo '<link rel="stylesheet" href="' . $css . '">';
                if (str_contains(strtolower($css), 'http:') || str_contains(strtolower($css), 'https:')) {
                    echo '<link rel="stylesheet" href="' . $css . '">';
                } else {
                    echo '<link rel="stylesheet" href="' . rtrim(BASE_URL, '/') . '/' . ltrim($css, '/') . '">';
                }
            }
        }
    }
}
