<?php

namespace Metroid\Services;

class SortHelper
{
    /**
     * @param string $field Champ sur lequel on veut trier
     * @return string
     * Méthode permettant de changer le sens de tri
     */
    public function toggleSort(string $field): string
    {
        $currentSort = $_GET['sort'] ?? 'title';
        $currentOrder = $_GET['dir'] ?? 'DESC';

        // Si on trie déjà sur le même champ, on inverse l'ordre
        $newOrder = ($currentSort === $field && $currentOrder === 'ASC') ? 'DESC' : 'ASC';

        return $newOrder;
    }
}
