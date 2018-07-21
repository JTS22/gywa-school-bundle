<?php

namespace GyWa\SchoolBundle\ContaoManager;

use Contao\NewsBundle\ContaoNewsBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerBundle\ContaoManagerBundle;
use GyWa\SchoolBundle\GyWaSchoolBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(GyWaSchoolBundle::class)
            ->setLoadAfter(
                [
                    ContaoCoreBundle::class,
                    ContaoManagerBundle::class
                ]
            ),
        ];
    }
}

?>
