<?php

class Application
{
    private Router $router;

    public function __construct()
    {
        $this->router = new Router();
        $this->initDatabase();
    }

    public function run(): void
    {
        $route = $this->router->parseRoute();

        $controllerName = ucfirst($route['controller']) . 'Controller';
        $actionName = 'action_' . $route['action'];

        if (!class_exists($controllerName)) {
            $this->show404("Контролер '{$route['controller']}' не знайдено.");
            return;
        }

        $controller = new $controllerName();

        if (!method_exists($controller, $actionName)) {
            $this->show404("Дію '{$route['action']}' не знайдено в контролері '{$route['controller']}'.");
            return;
        }

        $controller->$actionName();
    }

    private function initDatabase(): void
    {
        $dbPath = ROOT_DIR . '/database/app.db';
        $schemaPath = ROOT_DIR . '/database/schema.sql';

        if (!file_exists($schemaPath)) {
            return;
        }

        if (!file_exists($dbPath)) {
            $db = Database::getInstance();
            $db->exec(file_get_contents($schemaPath));
            return;
        }

        $db = Database::getInstance();

        if ($this->hasMissingSchema($db)) {
            $this->repairDatabase($db, $schemaPath);
        }
    }

    private function hasMissingSchema(PDO $db): bool
    {
        $requiredTables = ['users', 'movies', 'shows', 'reservations'];
        $placeholders = implode(',', array_fill(0, count($requiredTables), '?'));

        $stmt = $db->prepare("SELECT name FROM sqlite_master WHERE type='table' AND name IN ($placeholders)");
        $stmt->execute($requiredTables);
        $existing = $stmt->fetchAll(PDO::FETCH_COLUMN);

        return count($existing) !== count($requiredTables);
    }

    private function repairDatabase(PDO $db, string $schemaPath): void
    {
        $sql = file_get_contents($schemaPath);
        $sql = preg_replace('/--.*$/m', '', $sql);
        $statements = array_filter(array_map('trim', explode(';', $sql)));

        foreach ($statements as $statement) {
            if ($statement === '') {
                continue;
            }

            if (preg_match('/^CREATE TABLE IF NOT EXISTS\s+(\w+)/i', $statement, $matches)) {
                $db->exec($statement);
                continue;
            }

            if (preg_match('/^INSERT INTO\s+(\w+)/i', $statement, $matches)) {
                $table = $matches[1];
                if (!$this->tableHasRows($db, $table)) {
                    $db->exec($statement);
                }
            }
        }
    }

    private function tableHasRows(PDO $db, string $table): bool
    {
        $stmt = $db->query("SELECT 1 FROM {$table} LIMIT 1");
        return (bool) $stmt->fetch();
    }

    private function show404(string $message): void
    {
        http_response_code(404);
        $view = new PageView();
        $view->setTitle('404 — Сторінку не знайдено');
        $view->render('layout/404', ['message' => $message]);
    }
}
