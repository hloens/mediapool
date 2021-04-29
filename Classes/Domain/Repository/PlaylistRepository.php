<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Domain\Repository;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Class PlaylistRepository
 */
class PlaylistRepository extends Repository
{
    /**
     * Find all playlist links and uids without respecting pid
     *
     * @return array records with fields: uid, link
     */
    public function findAllLinksAndUids()
    {
        /** @var Connection $connection */
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable(
            'tx_mediapool_domain_model_playlist'
        );
        return $connection->select(['uid', 'link'], 'tx_mediapool_domain_model_playlist')->fetchAll();
    }

    /**
     * Find link and uid of records by pid
     *
     * @param string $pids comma separated list of pids
     * @return array records with fields: uid, link
     */
    public function findLinksAndUidsByPid(string $pids)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(
            'tx_mediapool_domain_model_playlist'
        );
        $query = $queryBuilder
            ->select('uid', 'link')
            ->from('tx_mediapool_domain_model_playlist')
            ->where(
                $queryBuilder->expr()->in('pid', $pids)
            );
        return $query->execute()->fetchAll();
    }

    /**
     * Find playlists by category
     *
     * @param int $categoryUid
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findByCategory(int $categoryUid)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->matching(
            $query->contains('categories', $categoryUid)
        );
        return $query->execute();
    }

    /**
     * Find pid of a playlist by uid
     *
     * @param int $playlistUid
     * @return mixed
     */
    public function findPidByUid(int $playlistUid)
    {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_mediapool_domain_model_playlist');
        return $connection
            ->select(['pid'], 'tx_mediapool_domain_model_playlist', ['uid' => $playlistUid])
            ->fetch()['pid'];
    }
}
