<?php

/*
 * This file is part of Composer.
 *
 * (c) Nils Adermann <naderman@naderman.de>
 *     Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Composer\Repository;

use Composer\Package\PackageInterface;

/**
 * Composite repository.
 *
 * @author Beau Simensen <beau@dflydev.com>
 */
class CompositeRepository implements RepositoryInterface
{
    /**
     * List of repositories
     * @var array
     */
    private $repositories;

    /**
     * Constructor
     * @param array $repositories
     */
    public function __construct(array $repositories)
    {
        $this->repositories = $repositories;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPackage(PackageInterface $package)
    {
        foreach ($this->repositories as $repository) {
            /* @var $repository RepositoryInterface */
            if ($repository->hasPackage($package)) {
                return true;
            }
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function findPackage($name, $version)
    {
        foreach ($this->repositories as $repository) {
            /* @var $repository RepositoryInterface */
            $package = $repository->findPackage($name, $version);
            if (null !== $package) {
                return $package;
            }
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function findPackagesByName($name)
    {
        $packages = array();
        foreach ($this->repositories as $repository) {
            /* @var $repository RepositoryInterface */
            $packages[] = $repository->findPackagesByName($name);
        }
        return call_user_func_array('array_merge', $packages);
    }

    /**
     * {@inheritdoc}
     */
    public function getPackages()
    {
        $packages = array();
        foreach ($this->repositories as $repository) {
            /* @var $repository RepositoryInterface */
            $packages[] = $repository->getPackages();
        }
        return call_user_func_array('array_merge', $packages);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        $total = 0;
        foreach ($this->repositories as $repository) {
            /* @var $repository RepositoryInterface */
            $total += $repository->count();
        }
        return $total;
    }

    /**
     * Add a repository.
     * @param RepositoryInterface $repository
     */
    public function addRepository(RepositoryInterface $repository)
    {
        $this->repositories[] = $repository;
    }
}