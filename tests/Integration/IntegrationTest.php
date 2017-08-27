<?php
/**
 * Narrator Bundle
 *
 * @link      http://github.com/mleko/narrator-bundle
 * @copyright Copyright (c) 2017 Daniel Król
 * @license   MIT
 */


namespace Mleko\Narrator\Bundle\Tests\Integration;


use Mleko\Narrator\Bundle\Tests\Integration\TestApp\AppKernel;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

abstract class IntegrationTest extends TestCase
{
    /**
     * @var AppKernel
     */
    protected $kernel;
    /**
     * @var vfsStreamDirectory
     */
    protected $root;

    protected function setUp()
    {
        /* force reload of ContainerClass */
        $this->setRunTestInSeparateProcess(true);
        $this->setPreserveGlobalState(false);

        $this->root = vfsStream::setup('testRoot');
        $cacheDir = vfsStream::newDirectory("cache", 0777);
        $this->root->addChild($cacheDir);
        $logDir = vfsStream::newDirectory("logs", 0777);
        $this->root->addChild($logDir);

        $this->kernel = new AppKernel("test", true);
        $this->kernel->getContainerBuilder()->setParameter("kernel.secret", "secretChangeIt");
        $this->kernel->setCacheDir($cacheDir->url());
        $this->kernel->setLogDir($logDir->url());

        $config = vfsStream::newFile("config.yml");
        $this->root->addChild($config);
        $this->kernel->setConfigPath($config->url());
    }

}
