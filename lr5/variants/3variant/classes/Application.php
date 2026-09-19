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

        $db = Database::getInstance();

        if (!file_exists($dbPath)) {
            $db->exec(file_get_contents($schemaPath));
            $this->ensureAdminCompatibility($db);
            $this->ensureMoviePosterCompatibility($db);
            return;
        }

        if ($this->hasMissingSchema($db)) {
            $this->repairDatabase($db, $schemaPath);
        }

        $this->ensureAdminCompatibility($db);
        $this->ensureMoviePosterCompatibility($db);
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

    private function ensureAdminCompatibility(PDO $db): void
    {
        $columns = $db->query("PRAGMA table_info(users)")->fetchAll(PDO::FETCH_ASSOC);
        $hasRole = false;

        foreach ($columns as $column) {
            if (($column['name'] ?? '') === 'role') {
                $hasRole = true;
                break;
            }
        }

        if (!$hasRole) {
            $db->exec("ALTER TABLE users ADD COLUMN role VARCHAR(20) NOT NULL DEFAULT 'user'");
        }

        $adminStmt = $db->prepare('SELECT id, role FROM users WHERE login = :login LIMIT 1');
        $adminStmt->execute([':login' => 'admin']);
        $adminUser = $adminStmt->fetch(PDO::FETCH_ASSOC);

        if (!$adminUser) {
            $db->prepare('INSERT INTO users (login, password, email, first_name, last_name, phone, city, gender, about, role) VALUES (:login, :password, :email, :first_name, :last_name, :phone, :city, :gender, :about, :role)')
                ->execute([
                    ':login' => 'admin',
                    ':password' => password_hash('admin123', PASSWORD_DEFAULT),
                    ':email' => 'admin@cinema.local',
                    ':first_name' => 'Адмін',
                    ':last_name' => 'Система',
                    ':phone' => '',
                    ':city' => 'Kyiv',
                    ':gender' => 'male',
                    ':about' => 'Керівник системи адміністрування',
                    ':role' => 'admin',
                ]);
        } elseif (($adminUser['role'] ?? '') !== 'admin') {
            $db->prepare('UPDATE users SET role = :role WHERE login = :login')
                ->execute([':role' => 'admin', ':login' => 'admin']);
        }
    }

    private function ensureMoviePosterCompatibility(PDO $db): void
    {
        $columns = $db->query("PRAGMA table_info(movies)")->fetchAll(PDO::FETCH_ASSOC);
        $hasPoster = false;

        foreach ($columns as $column) {
            if (($column['name'] ?? '') === 'poster_url') {
                $hasPoster = true;
                break;
            }
        }

        if (!$hasPoster) {
            $db->exec("ALTER TABLE movies ADD COLUMN poster_url VARCHAR(255) NOT NULL DEFAULT ''");
        }
    }

    private function show404(string $message): void
    {
        http_response_code(404);
        $view = new PageView();
        $view->setTitle('404 — Сторінку не знайдено');
        $view->render('layout/404', ['message' => $message]);
    }
}
