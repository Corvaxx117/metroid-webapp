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

    /**
     * Renvoie le symbole ascendant ou descendant en fonction de l'ordre de tri
     * actuel et du champ demandé.
     *
     * @param string $field Le champ sur lequel on veut afficher l'indicateur de tri
     * @return string Le symbole ascendant (▲) ou descendant (▼)
     */
    public function sortIcon(string $field): string
    {
        $currentSort = $_GET['sort'] ?? 'title';
        $currentOrder = $_GET['dir'] ?? 'DESC';

        return $currentSort === $field && $currentOrder === 'ASC' ? '▲' : '▼';
    }
}
