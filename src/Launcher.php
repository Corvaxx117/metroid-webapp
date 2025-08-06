<?php

namespace Metroid;

use Metroid\Container\ServiceContainer;
use Metroid\ErrorHandler\ErrorHandler;
use Metroid\View\ViewRenderer;
use Metroid\Router\Router;
use Metroid\Http\Request;
use Metroid\Http\Response;
use Symfony\Component\Dotenv\Dotenv;

/**
 * Classe Launcher
 * Classe responsable de lancement de l'application
 * @package Metroid\Core
 */
class Launcher
{
    private ServiceContainer $container;
    private Router $router;
    private ErrorHandler $errorHandler;
    private string $basePath;

    /**
     * Constructeur
     *
     * @param string $basePath Chemin de base de l'application
     * @param string $routesFile Chemin du fichier de routes
     */
    public function __construct(string $basePath, string $routesFile)
    {
        // Initialise le chemin de base
        $this->basePath = rtrim($basePath, '/') . '/';
        $this->initializeEnvironment();
        // Création du conteneur
        $this->container = new ServiceContainer();
        // Enregistre ErrorHandler dans le conteneur
        $this->container->set(ErrorHandler::class, fn($c) => new ErrorHandler(
            $c->get(ViewRenderer::class)
        ));

        $this->errorHandler = $this->container->get(ErrorHandler::class);

        $this->router = new Router($routesFile);

        // Configurer un gestionnaire global pour les exceptions non capturées
        set_exception_handler([$this->errorHandler, 'handle']);
    }

    /**
     * Initialise les variables d'environnement
     */
    private function initializeEnvironment(): void
    {
        $dotenv = new Dotenv();
        $envFile = $this->basePath . '.env';

        if (file_exists($envFile)) {
            $dotenv->load($envFile);
        }

        $envLocal = $this->basePath . '.env.local';
        if (file_exists($envLocal)) {
            $dotenv->load($envLocal);
        }
    }


    /**
     * Lancement de l'application
     * 
     * 1. Récupère la requête actuelle
     * 2. Tente de trouver une route correspondante
     * 3. Instancie le contrôleur via le container
     * 4. Prépare les arguments à injecter dans la méthode du contrôleur
     * 5. Exécute l'action du contrôleur
     * 6. Envoie la réponse
     * 
     * Si une erreur se produit, elle est capturée et gérée par le gestionnaire d'erreur
     */
    public function run(): void
    {
        // Démarrage de la session si pas déjà démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // On récupère l'instance de la requête depuis le conteneur de services
        // Cela permet d'accéder à l'URI, la méthode HTTP, les données GET/POST, etc.
        $request = $this->container->get(Request::class);

        try {
            // 1. On demande au Router de trouver la route correspondant à l'URI et à la méthode HTTP
            // Cela retourne un tableau avec : le nom du contrôleur, la méthode à appeler, et les paramètres d'URL
            $match = $this->router->match($request->uri, $request->method);

            $controllerClass = $match['controllerClass'];   // Exemple : App\Controller\BookController
            $method = $match['controllerMethod'];           // Exemple : show
            $routeParams = $match['params'];                // Exemple : ['id' => 42]

            // 2. On demande au conteneur de construire le contrôleur avec ses dépendances
            // Grâce à l’autowiring, même s’il n’est pas enregistré, il sera instancié automatiquement
            $controller = $this->container->get($controllerClass);

            // 3. On prépare à appeler la méthode cible du contrôleur (ex : BookController::show)
            $reflection = new \ReflectionMethod($controllerClass, $method);
            $args = []; // Ce tableau contiendra les arguments à passer à la méthode du contrôleur

            // 4. On parcourt tous les paramètres de la méthode pour injecter dynamiquement les bonnes valeurs
            foreach ($reflection->getParameters() as $param) {
                $type = $param->getType();   // Exemple : int, string, Request, etc.
                $name = $param->getName();   // Exemple : 'id', 'request', etc.

                // Cas 1 : injection automatique d’un objet (ex: Request, AuthService, etc.)
                if ($type && !$type->isBuiltin()) {
                    $args[] = $this->container->get($type->getName());
                }

                // Cas 2 : injection d’un paramètre de route (ex: $id)
                elseif ($type && $type->isBuiltin()) {
                    if (array_key_exists($name, $routeParams)) {
                        // On force la valeur capturée dans l'URL à être du bon type (int, string, etc.)
                        settype($routeParams[$name], $type->getName());
                        $args[] = $routeParams[$name];
                    } else {
                        // Erreur si un paramètre attendu n’est pas présent dans l’URL
                        throw new \RuntimeException("Paramètre '$name' manquant pour l'action '$controllerClass::$method'");
                    }
                }

                // Cas non prévu (ex: pas de type, ou type invalide)
                else {
                    throw new \RuntimeException("Type inconnu ou absent pour le paramètre '$name' de '$controllerClass::$method'");
                }
            }

            // 5. On appelle la méthode du contrôleur avec les bons arguments
            // Exemple : $controller->show(42, $request)
            $response = call_user_func_array([$controller, $method], $args);

            // 6. On vérifie que le contrôleur a bien retourné un objet Response (Http\Response)
            if (!$response instanceof Response) {
                throw new \RuntimeException("Le contrôleur doit retourner une instance de Response.");
            }

            // 7. On envoie la réponse HTTP au navigateur (en-têtes, contenu, etc.)
            $response->send();
        }

        // 8. Si une exception est levée à n’importe quelle étape, on utilise le ErrorHandler du conteneur
        catch (\Throwable $e) {
            $this->container->get(ErrorHandler::class)->handle($e);
        }
    }
}
