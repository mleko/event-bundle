<?php
/**
 * Narrator Bundle
 *
 * @link      http://github.com/mleko/narrator-bundle
 * @copyright Copyright (c) 2017 Daniel Król
 * @license   MIT
 */


namespace Mleko\Narrator\Bundle\Tests\Integration\TestApp;


use Mleko\Narrator\Bundle\NarratorBundle;
use Mleko\Narrator\Bundle\Tests\Integration\TestApp\Container\Compiler\CompilerPass;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{

    private $cacheDir;
    private $configPath;
    private $logDir;


    public function registerBundles()
    {
        return [
            new NarratorBundle()
        ];
    }

    public function setConfigPath($configPath)
    {
        $this->configPath = $configPath;
    }

    public function getLogDir()
    {
        return $this->logDir;
    }

    public function setLogDir($logDir)
    {
        $this->logDir = $logDir;
    }

    public function getCacheDir()
    {
        return $this->cacheDir;
    }

    public function setCacheDir($dir)
    {
        $this->cacheDir = $dir;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->configPath);
    }

    public function getContainerBuilder()
    {
        return parent::getContainerBuilder();
    }

    protected function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new CompilerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION);
    }
}
