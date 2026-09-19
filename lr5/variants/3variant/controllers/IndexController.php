<?php

class IndexController extends PageController
{
    public function action_main(): void
    {
        $db = Database::getInstance();

        $moviesCount = (int)$db->query('SELECT COUNT(*) FROM movies')->fetchColumn();
        $showsCount = (int)$db->query('SELECT COUNT(*) FROM shows')->fetchColumn();
        $usersCount = (int)$db->query('SELECT COUNT(*) FROM users')->fetchColumn();
        $reservationsCount = (int)$db->query('SELECT COUNT(*) FROM reservations')->fetchColumn();

        $featuredMovies = $db->query('SELECT m.*, (SELECT COUNT(*) FROM shows s WHERE s.movie_id = m.id) AS show_count FROM movies m ORDER BY show_count DESC, m.year DESC LIMIT 4')->fetchAll();
        $upcomingShows = $db->query('SELECT s.*, m.title AS movie_title, m.genre, m.duration_min FROM shows s JOIN movies m ON m.id = s.movie_id ORDER BY s.show_time ASC LIMIT 4')->fetchAll();

        $palette = ['#f97316', '#8b5cf6', '#38bdf8', '#34d399', '#f43f5e', '#facc15'];
        foreach ($featuredMovies as $index => $movie) {
            $posterUrl = trim((string)($movie['poster_url'] ?? ''));
            $color = $palette[$index % count($palette)];

            if ($posterUrl !== '') {
                $featuredMovies[$index]['poster_style'] = 'background-image: url(\'' . htmlspecialchars($posterUrl, ENT_QUOTES) . '\'); background-size: cover; background-position: center; background-repeat: no-repeat;';
            } else {
                $featuredMovies[$index]['poster_style'] = 'background: linear-gradient(135deg, ' . $color . ' 0%, #111827 100%);';
            }
        }

        $this->render('index/main', [
            'moviesCount' => $moviesCount,
            'showsCount' => $showsCount,
            'usersCount' => $usersCount,
            'reservationsCount' => $reservationsCount,
            'featuredMovies' => $featuredMovies,
            'upcomingShows' => $upcomingShows,
        ], 'Головна');
    }
}
