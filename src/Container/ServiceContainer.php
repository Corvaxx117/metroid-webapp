<?php

namespace Metroid\Container;

/**
 * Classe responsable de la gestion des services
 */
class ServiceContainer
{
    private array $services = [];

    /**
     * Enregistre un service.
     *
     * Le service est stocké dans un tableau avec comme clé  l'ID du service.
     * Si l'ID du service existe déjà , il sera écrasé .
     *
     * @param string $id ID du service
     * @param \Closure $factory Closure qui instancie le service
     */
    public function set(string $id, \Closure $factory): void
    {
        $this->services[$id] = $factory;
    }

    /**
     * Retourne un service.
     *
     * Si le service n'est pas encore instancié, il sera instancié en appelant
     * la Closure enregistrée pour ce service.
     *
     * @param string $id ID du service
     *
     * @throws \RuntimeException Si le service n'est pas enregistré
     *
     * @return object Instance du service
     */
    public function get(string $className): object
    {
        // 1. Si le service a déjà été enregistré manuellement (via ->set()), on le retourne
        if (isset($this->services[$className])) {

            // Pourquoi vérifier s'il s'agit d'une Closure ?
            // → Quand on enregistre un service avec ->set(Foo::class, fn() => new Foo()), on stocke une fonction (Closure)
            //    Cela permet de différer la création de l'objet jusqu'au moment où on en a vraiment besoin (lazy loading)

            if ($this->services[$className] instanceof \Closure) {
                // On exécute la Closure (la "factory") pour créer le service.
                // On lui passe $this pour qu’elle puisse récupérer d’autres services si besoin.
                $this->services[$className] = ($this->services[$className])($this);
            }

            // On retourne l'instance du service, qu'elle ait été créée avant ou juste maintenant.
            return $this->services[$className];
        }

        // 2. Si le service n'est pas enregistré mais que la classe existe, on fait de l'autowiring automatique
        if (class_exists($className)) {

            // On utilise Reflection pour analyser la classe dynamiquement
            $reflection = new \ReflectionClass($className);
            $constructor = $reflection->getConstructor();

            if (!$constructor) {
                // Si la classe n'a pas de constructeur → on peut l'instancier directement
                $instance = new $className();
            } else {
                // Sinon, il faut résoudre chaque dépendance attendue par le constructeur
                $params = [];
                /** @var \ReflectionParameter $param */
                foreach ($constructor->getParameters() as $param) {
                    $type = $param->getType();

                    // Si on ne connaît pas le type ou si c'est un type primitif (int, string, etc.), on ne peut pas l'injecter
                    if (!$type || $type->isBuiltin() && !$param->isDefaultValueAvailable()) {
                        throw new \RuntimeException("Impossible de résoudre la dépendance : " . $param->getName());
                    }
                    // Si le type est builtin, on récupère la valeur par defaut du parametre du constructeur
                    if ($type->isBuiltin()) {
                        $params[] = $param->getDefaultValue();
                    } else {
                        // Récursion : on demande au container de créer l'objet attendu
                        // Exemple : si le constructeur demande un ViewRenderer, on appelle $this->get(ViewRenderer::class)
                        $params[] = $this->get($type->getName());
                    }
                }

                // Une fois tous les paramètres récupérés, on crée l’instance de la classe avec newInstanceArgs
                $instance = $reflection->newInstanceArgs($params);
            }

            // On stocke l’instance pour éviter de la reconstruire la prochaine fois (singleton)
            $this->services[$className] = $instance;

            // On retourne l’instance construite automatiquement
            return $instance;
        }

        // Si ni enregistré ni instanciable automatiquement, on lève une exception
        throw new \RuntimeException("Le service '{$className}' n'est pas enregistré.");
    }
}
